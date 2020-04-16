<?php

namespace App\Traits;

use Carbon\Carbon;
use App\Http\Helpers\Constant;
use App\Http\Helpers\FileHelper;

trait priceTrait
{

    public function createPrice($name, $value){

        $price = FileHelper::getFile(storage_path(FileHelper::PRICE_FILE_PATH), true);
        $price->{$name} = $value;
        FileHelper::rewriteFile(storage_path(FileHelper::PRICE_FILE_PATH), $price);   

        return $price;
    }

    public function findManyPrice(){
        $price = FileHelper::getFile(storage_path(FileHelper::PRICE_FILE_PATH), true);        
        
        return $price;
    }
}