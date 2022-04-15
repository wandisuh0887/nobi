<?php

namespace App\Imports;

use App\Models\Data;
use Maatwebsite\Excel\Concerns\ToModel;

class DataImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        if(is_numeric($row[0])) {
            return new Data([
                'id' => $row[0],
                'name' => $row[1],
                'ticker' => $row[2],
                'coin_id' => $row[3],
                'code' => $row[4],
                'exchange' => $row[5],
                'invalid' => $row[6],
                'record_time' => $row[7],
                'usd' => $row[8],
                'idr' => $row[9],
                'hnst' => $row[10],
                'eth' => $row[11],
                'btc' => $row[12],
                'created_at' => $row[13],
                'updated_at' => $row[14],
            ]);
        }
    }
}
