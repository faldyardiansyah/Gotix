<?php

namespace App\Enums;

enum KategoriKeluhan: string
{
    case Akun = 'Akun';
    case Pesanan = 'Pesanan';
    case Pembayaran = 'Pembayaran';
    case Pelayanan = 'Pelayanan';
    case Armada = 'Armada';
    case Lainnya = 'Lainnya';
}
