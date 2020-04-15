<?php

namespace App\Traits;

use Carbon\Carbon;
use App\Http\Helpers\Constant;
use App\Http\Helpers\FileHelper;

trait ParkingTrait
{

    public function createParking($params){

        $parking = new \stdClass();
        $parking->license_number = $params['license_number'];
        $parking->color = $params['color'];
        $parking->type = $params['type'];
        $parking->space = $params['space'];
        $parking->created_at = Carbon::now('Asia/Jakarta')->toDateTimeString();
        $parking->updated_at = Carbon::now('Asia/Jakarta')->toDateTimeString();
        FileHelper::saveToFile(storage_path(FileHelper::PARKING_FILE_PATH), $parking);

        return $parking;
    }
}