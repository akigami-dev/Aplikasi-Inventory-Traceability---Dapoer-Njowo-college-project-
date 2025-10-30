<?php

namespace App\Observers\Owner;

use App\Models\MasterKategori;

class MasterKategoriObserver
{
    public function updating(MasterKategori $kategori): void
    {
        $kategori->updated_at = now();
    }
}
