<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Role;
use Starmoozie\LaravelMenuPermission\app\Models\MenuPermission;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class DataImport implements ToCollection, WithStartRow, WithChunkReading
{
    /**
     * @return int
     */
    public function startRow(): int
    {
        return 2;
    }

    public function chunkSize(): int
    {
        return 100;
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function collection(Collection $row)
    {
        //
    }
}
