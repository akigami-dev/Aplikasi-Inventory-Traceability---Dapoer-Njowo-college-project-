<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ArrayExport implements FromArray, WithHeadings
{
    protected $data;
    protected $heading;

    public function __construct(array $data, array $heading)
    {
        $this->data = $data;
        $this->heading = $heading;
    }

    public function array(): array
    {
        return $this->data;
    }

    public function headings(): array
    {
        return $this->heading;
    }
}

