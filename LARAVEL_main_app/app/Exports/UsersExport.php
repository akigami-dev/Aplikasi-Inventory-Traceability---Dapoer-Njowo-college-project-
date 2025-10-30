<?php

namespace App\Exports;

use App\Http\Resources\MasterBahanBakuResource;
use App\Models\MasterBahanBaku;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;

class UsersExport implements FromArray
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function array(): array
    {
        $mb = MasterBahanBaku::where('is_archived', false)->get();
        $result = MasterBahanBakuResource::collection($mb)->resolve();
        return $result;
    }
}
