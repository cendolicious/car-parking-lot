<?php

namespace App\Traits;

use Carbon\Carbon;
use App\Http\Helpers\Constant;
use App\Http\Helpers\FileHelper;

trait SpaceTrait
{

    public function createSpace($params){

        $space = new \stdClass();
        $space->block = $params['block'];
        $space->number = $params['number'];
        $space->status = Constant::STATUS_EMPTY;
        $space->created_at = Carbon::now('Asia/Jakarta')->toDateTimeString();
        $space->updated_at = Carbon::now('Asia/Jakarta')->toDateTimeString();
        FileHelper::saveToFile(storage_path(FileHelper::SPACE_FILE_PATH), $space);

        return $space;
    }

    public function findManySpace(){
        $space = FileHelper::getFile(storage_path(FileHelper::SPACE_FILE_PATH), true);        
        
        return $space;
    }

    public function findManySpaceByBlock($block){
        $space = FileHelper::getFile(storage_path(FileHelper::SPACE_FILE_PATH), true);
        $space = collect($space)->where('block', $block)->toArray();        

        return $space;
    }
}