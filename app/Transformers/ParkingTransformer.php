<?php

namespace App\Transformers;

use App\Http\Helpers\Constant;
use League\Fractal\TransformerAbstract;

class ParkingTransformer extends TransformerAbstract
{

    public function transform($parkingData)
    {
        $item['license_number'] = (string) $parkingData->license_number;
        $item['parking_lot'] = (string) $parkingData->block . $parkingData->number;
        $item['parking_check_in_date'] = (string) $parkingData->created_at;

        if ($parkingData->status == Constant::STATUS_DONE){
            $item['parking_check_out_date'] = $parkingData->updated_at;
            $item['total'] = (int) $parkingData->bill;
        }

        return $item;
    }
}