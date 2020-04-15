<?php

namespace App\Http\Controllers;

use App\Traits\SpaceTrait;
use App\Traits\ParkingTrait;
use Illuminate\Http\Request;
use App\Http\Helpers\Constant;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class ParkingController extends Controller
{
    use SpaceTrait, ParkingTrait;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function checkin(Request $request){
        $params = $request->all();
        $result = array();

        $validator = Validator::make($params, [
            'license_number' => "required|string",
            'color' => "required|string",
            'type' => ['required', Rule::in(Constant::CAR_TYPE)]
        ]);

        if ($validator->fails()) {
            return ['error' => true, 'message' => implode($validator->errors()->all(), " | ")];
        }

        try
        {
            $emptySpace = $this->findOneSpaceByStatus(Constant::STATUS_EMPTY);
            if (!$emptySpace)
                return ['error' => true, 'message' => Constant::MSG_NO_SPACE_LEFT];
            
            $params['space'] = $emptySpace->block . $emptySpace->number;
            $result = $this->createParking($params);
            $this->updateSpaceStatus($emptySpace->block, $emptySpace->number, Constant::STATUS_BOOKED);
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

}
