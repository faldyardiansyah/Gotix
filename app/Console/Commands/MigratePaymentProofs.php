<?php

namespace App\Console\Commands;

use App\Models\KonfirmasiPembayaran;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class MigratePaymentProofs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gotix:migrate-payment-proofs {--dry-run : Pratinjau migrasi tanpa memindahkan file atau mengubah database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrasi file bukti pembayaran dari public disk ke private storage disk secara aman dengan verifikasi checksum';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $this->info($dryRun
            ? '[DRY RUN] Memeriksa status berkas bukti pembayaran publik...'
            : 'Memulai migrasi berkas bukti pembayaran ke private storage...');

        $records = KonfirmasiPembayaran::whereNotNull('bukti_path')->get();

        $migrated = 0;
        $alreadyPrivate = 0;
        $missing = 0;
        $errors = 0;
        $claimedPaths = [];

        foreach ($records as $record) {
            $path = $record->bukti_path;

            // Jika sudah ada di disk local, berkas sudah terlindungi
            if (Storage::disk('local')->exists($path)) {
                $alreadyPrivate++;

                continue;
            }

            // Jika ada di disk public, perlu dimigrasi ke disk local
            if (Storage::disk('public')->exists($path)) {
                $filename = basename($path);
                $extension = pathinfo($filename, PATHINFO_EXTENSION);
                $name = pathinfo($filename, PATHINFO_FILENAME);

                // Buat path tujuan yang unik dan pastikan tidak menimpa file privat yang sudah ada
                $newPath = 'bukti_pembayaran/'.$filename;
                $counter = 1;
                while (Storage::disk('local')->exists($newPath) || in_array($newPath, $claimedPaths, true)) {
                    $suffix = '_'.($counter === 1 ? substr(hash('sha256', $path.'_'.$record->id), 0, 8) : $counter);
                    $newFilename = $name.$suffix.($extension ? '.'.$extension : '');
                    $newPath = 'bukti_pembayaran/'.$newFilename;
                    $counter++;
                }

                $claimedPaths[] = $newPath;

                $this->line("Ditemukan berkas publik: {$path} -> target private: {$newPath}");

                if ($dryRun) {
                    $migrated++;

                    continue;
                }

                try {
                    $content = Storage::disk('public')->get($path);
                    $originalHash = hash('sha256', $content);

                    // Tulis ke disk private ('local')
                    Storage::disk('local')->put($newPath, $content);

                    // Verifikasi integritas checksum
                    $savedContent = Storage::disk('local')->get($newPath);
                    $savedHash = hash('sha256', $savedContent);

                    if ($originalHash !== $savedHash) {
                        $this->error("Checksum tidak cocok untuk {$path}! Migrasi dibatalkan untuk baris ini.");
                        Storage::disk('local')->delete($newPath);
                        $errors++;

                        continue;
                    }

                    // Perbarui database dan hapus dari storage publik
                    $record->update(['bukti_path' => $newPath]);
                    Storage::disk('public')->delete($path);

                    $this->info("Berhasil memigrasikan: ID {$record->id} ke {$newPath}");
                    $migrated++;
                } catch (\Throwable $e) {
                    if (Storage::disk('local')->exists($newPath)) {
                        Storage::disk('local')->delete($newPath);
                    }
                    $this->error("Gagal memigrasikan ID {$record->id}: ".$e->getMessage());
                    $errors++;
                }
            } else {
                $this->warn("Berkas tidak ditemukan baik di public maupun private: {$path} (ID: {$record->id})");
                $missing++;
            }
        }

        $this->newLine();
        $this->table(
            ['Metrik', 'Jumlah'],
            [
                ['Total Baris Diperiksa', $records->count()],
                [$dryRun ? 'Dapat Dimigrasikan' : 'Berhasil Dimigrasikan', $migrated],
                ['Sudah Private (Aman)', $alreadyPrivate],
                ['Berkas Fisik Tidak Ditemukan', $missing],
                ['Gagal / Error', $errors],
            ]
        );

        return $errors > 0 ? \Symfony\Component\Console\Command\Command::FAILURE : \Symfony\Component\Console\Command\Command::SUCCESS;
    }
}
