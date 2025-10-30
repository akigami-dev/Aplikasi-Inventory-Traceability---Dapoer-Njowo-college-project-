<?php

namespace App\Observers\Owner;

use App\Models\MasterKategori;

class KategoriObserver
{
    public function updating(MasterKategori $kategori): void
    {
        $kategori->updated_at = now();
    }
}
