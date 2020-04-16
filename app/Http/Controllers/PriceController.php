<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Traits\PriceTrait;
use Illuminate\Http\Request;
use App\Http\Helpers\Constant;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class PriceController extends Controller
{
    use PriceTrait;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function create(Request $request){
        $params = $request->all();
        $result = array();

        $validator = Validator::make($params, [
            'name' => "required|string",
            'value' => "required|int"
        ]);

        if ($validator->fails()) {
            return ['error' => true, 'message' => implode($validator->errors()->all(), " | ")];
        }

        try
        {
            $price = $this->createPrice($params['name'], $params['value']);
        }
        catch (Exception $ex)
        {
            return ['error' => true, 'message' => $ex->getMessage()];
        }

        return [
            'error' => false,
            'message' => 'Successfully create parking price.',
            'data' => $params
        ];
    }

    public function list(Request $request){
        $params = $request->all();
        $result = array();

        try
        {
            $result = $this->findManyPrice();
        }
        catch (Exception $ex)
        {
            return ['error' => true, 'message' => $ex->getMessage()];
        }

        return [
            'error' => false,
            'message' => 'Successfully get parking price list.',
            'data' => $result
        ];
    }

}
