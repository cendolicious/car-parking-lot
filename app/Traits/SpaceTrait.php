<?php

namespace App\Traits;

use Carbon\Carbon;
use App\Http\Helpers\Constant;
use App\Http\Helpers\FileHelper;

trait SpaceTrait
{

    public function createSpace($block, $number, $status = false, $createdDate = false){

        $space = new \stdClass();
        $space->block = $block;
        $space->number = $number;
        $space->status = ($status) ? $status : Constant::STATUS_EMPTY;
        $space->created_at = ($createdDate) ? $createdDate : Carbon::now('Asia/Jakarta')->toDateTimeString();
        $space->updated_at = Carbon::now('Asia/Jakarta')->toDateTimeString();
        FileHelper::saveToFile(storage_path(FileHelper::SPACE_FILE_PATH), $space);

        return $space;
    }

    public function updateSpaceStatus($block, $number, $status){
        $space = self::findManySpace();
        $updatedSpace = collect($space)->where('block', $block);
        $updatedSpace = collect($space)->where('number', $number);

        foreach ($updatedSpace as $key => $value) {
            $createdDate = $space[$key]->created_at;
            unset($space[$key]);
        }

        FileHelper::rewriteFile(storage_path(FileHelper::SPACE_FILE_PATH), array_values($space));    
        $space = self::createSpace($block, $number, $status, $createdDate);

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

    public function findOneSpaceByStatus($status){
        $space = FileHelper::getFile(storage_path(FileHelper::SPACE_FILE_PATH), true);
        $space = collect($space)->sortBy('number');
        $space = $space->firstWhere('status', $status);

        return $space;
    }
}