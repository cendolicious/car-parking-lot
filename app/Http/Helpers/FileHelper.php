<?php

namespace App\Http\Helpers;

class FileHelper {

    const SPACE_FILE_PATH = 'app/space.json';
    const PARKING_FILE_PATH = 'app/parking.json';
    const PRICE_FILE_PATH = 'app/price.json';

    public static function getFile($path, $parse = false){
        if (!file_exists($path))
            fwrite(fopen($path, 'a+'), "");

        if ($parse)
            return json_decode(file_get_contents($path));
        return file_get_contents($path);
    }

    public static function saveToFile($path, $content){
        if (!file_exists($path)) {
            $content = array((array) $content);
            fwrite(fopen($path, 'a+'), json_encode($content));
        }else{
            $file = file_get_contents($path);
            $data = json_decode($file);
            unset($file); //Release Memory

            if(!$data)
                $data = array();    
            array_push($data, (array) $content);
            file_put_contents($path, json_encode($data));
            unset($data);
        }

        return true;
    }

    public static function rewriteFile ($path, $content){
        fwrite(fopen($path, 'w'), json_encode($content));

        return true;
    }

}