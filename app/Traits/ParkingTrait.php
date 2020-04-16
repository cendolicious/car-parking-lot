<?php

namespace App\Traits;

use Carbon\Carbon;
use App\Http\Helpers\Constant;
use App\Http\Helpers\FileHelper;

trait ParkingTrait
{

    public function createParking($licenseNumber, $color, $type, $block, $number, $bill, $status = false, $createdDate = false){

        $parking = new \stdClass();
        $parking->license_number = $licenseNumber;
        $parking->color = $color;
        $parking->type = $type;
        $parking->block = $block;
        $parking->number = $number;
        $parking->bill = $bill;
        $parking->status = ($status) ? $status : Constant::STATUS_BOOKED;
        $parking->created_at = ($createdDate) ? $createdDate : Carbon::now('Asia/Jakarta')->toDateTimeString();
        $parking->updated_at = Carbon::now('Asia/Jakarta')->toDateTimeString();
        FileHelper::saveToFile(storage_path(FileHelper::PARKING_FILE_PATH), $parking);

        return $parking;
    }

    public function updateParkingBillAndStatus($licenseNumber, $bill, $status){
        $parking = self::findManyParking();
        $updatedParking = collect($parking)->where('license_number', $licenseNumber);
        $updatedParking = collect($parking)->where('status', Constant::STATUS_BOOKED);

        foreach ($updatedParking as $key => $value) {
            $color = $parking[$key]->color;
            $type = $parking[$key]->type;
            $block = $parking[$key]->block;
            $number = $parking[$key]->number;
            $createdDate = $parking[$key]->created_at;
            unset($parking[$key]);
        }

        FileHelper::rewriteFile(storage_path(FileHelper::PARKING_FILE_PATH), array_values($parking));    
        $parking = self::createParking($licenseNumber, $color, $type, $block, $number, $bill, $status, $createdDate);

        return $parking;
    }

    public function findManyParking(){
        $space = FileHelper::getFile(storage_path(FileHelper::PARKING_FILE_PATH), true);        
        
        return $space;
    }

    public function findOneParkingByLicenseNumberAndStatus($licenseNumber, $status){
        $parking = FileHelper::getFile(storage_path(FileHelper::PARKING_FILE_PATH), true);
        $parking = collect($parking)->where('license_number', $licenseNumber);
        $parking = collect($parking)->firstWhere('status', $status);

        return $parking;
    }
}