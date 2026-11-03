<?php

namespace App\Enums;

enum StatusPesanan: string
{
    case BelumBayar = 'Belum Bayar';
    case DalamProses = 'Dalam Proses';
    case Lunas = 'Lunas';
    case Dibatalkan = 'Dibatalkan';
}
