<?php

namespace App\Console\Commands;

use App\Enums\StatusPesanan;
use App\Models\Pesanan;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

class CancelExpiredOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gotix:cancel-expired-orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Batalkan pesanan berstatus Belum Bayar yang telah melewati batas pembayaran dan lepaskan kursi secara otomatis.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $now = now();
        $expiredCandidates = Pesanan::where('status', StatusPesanan::BelumBayar)
            ->whereNotNull('batas_bayar')
            ->where('batas_bayar', '<', $now)
            ->get();

        if ($expiredCandidates->isEmpty()) {
            $this->info('Tidak ada pesanan kedaluwarsa yang perlu dibatalkan.');

            return SymfonyCommand::SUCCESS;
        }

        $cancelledCount = 0;

        foreach ($expiredCandidates as $candidate) {
            DB::transaction(function () use ($candidate, $now, &$cancelledCount) {
                /** @var Pesanan|null $lockedPesanan */
                $lockedPesanan = Pesanan::where('id', $candidate->id)->lockForUpdate()->first();

                if (! $lockedPesanan) {
                    return;
                }

                // Verifikasi ulang status dan batas bayar di dalam lock
                if ($lockedPesanan->status === StatusPesanan::BelumBayar && $lockedPesanan->batas_bayar && $lockedPesanan->batas_bayar < $now) {
                    $lockedPesanan->update(['status' => StatusPesanan::Dibatalkan]);
                    $cancelledCount++;

                    $this->line("Pesanan #{$lockedPesanan->kode_pesanan} (Kursi #{$lockedPesanan->nomor_kursi}) berhasil dibatalkan karena melewati batas bayar.");
                }
            });
        }

        $this->info("Total {$cancelledCount} pesanan kedaluwarsa berhasil dibatalkan dan kursi dilepaskan.");

        return SymfonyCommand::SUCCESS;
    }
}

