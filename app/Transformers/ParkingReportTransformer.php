<?php

namespace App\Transformers;

use App\Http\Helpers\Constant;
use League\Fractal\TransformerAbstract;

class ParkingReportTransformer extends TransformerAbstract
{

    public function transform($parkingData)
    {
        $item['total_car'] = (int) $parkingData->count();
        $item['license_number'] = $parkingData->pluck('license_number')->toArray();

        return $item;
    }
}