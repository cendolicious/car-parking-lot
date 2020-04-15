<?php

namespace App\Http\Controllers;

use App\Traits\SpaceTrait;
use Illuminate\Http\Request;
use App\Http\Helpers\Constant;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class SpaceController extends Controller
{
    use SpaceTrait;
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
            'block' => "required|string",
            'quantity' => "required|int"
        ]);

        if ($validator->fails()) {
            return ['error' => true, 'message' => implode($validator->errors()->all(), " | ")];
        }

        try
        {
            $latestSpace = $this->findManySpaceByBlock($params['block']);
            if (!$latestSpace)
                $latestSpaceBlockNumber = 1;
            else
                $latestSpaceBlockNumber = $latestSpace[sizeof($latestSpace) - 1]->number + 1;
            
            for ($i = $latestSpaceBlockNumber; $i < ($latestSpaceBlockNumber + $params['quantity']); $i++) { 
                $params['number'] = $i;
                $space = $this->createSpace($params['block'], $params['number']);
                array_push($result, $space);
            }
        }
        catch (Exception $ex)
        {
            return ['error' => true, 'message' => $ex->getMessage()];
        }

        return [
            'error' => false,
            'message' => 'Successfully create parking space.',
            'data' => $result
        ];
    }

    public function list(Request $request){
        $params = $request->all();
        $result = array();

        try
        {
            $result = $this->findManySpace();
            $result = collect($result)->sortBy('status')->sortBy('number')->sortBy('block');

            if (isset($params['status']) && !empty($params['status'])) {
                $result = $result->where('status', $params['status']);
            }

            if (isset($params['block']) && !empty($params['block'])) {
                $result = $result->where('block', $params['block']);
            }
            
            $result = $result->toArray();
        }
        catch (Exception $ex)
        {
            return ['error' => true, 'message' => $ex->getMessage()];
        }

        return [
            'error' => false,
            'message' => 'Successfully get parking space list.',
            'data' => array_values($result)
        ];
    }

}
