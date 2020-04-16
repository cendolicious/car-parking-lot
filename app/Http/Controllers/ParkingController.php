<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Traits\PriceTrait;
use App\Traits\SpaceTrait;
use App\Traits\ParkingTrait;
use Illuminate\Http\Request;
use App\Http\Helpers\Constant;
use Illuminate\Validation\Rule;
use App\Transformers\ParkingTransformer;
use Illuminate\Support\Facades\Validator;
use App\Transformers\ParkingReportTransformer;

class ParkingController extends Controller
{
    use SpaceTrait, ParkingTrait, PriceTrait;
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
        $prices = $this->findManyPrice();

        $validator = Validator::make($params, [
            'license_number' => "required|string",
            'color' => "required|string",
            'type' => ['required', Rule::in(array_keys((array) $prices))]
        ]);

        if ($validator->fails()) {
            return ['error' => true, 'message' => implode($validator->errors()->all(), " | ")];
        }

        try
        {
            $parkingData = $this->findOneParkingByLicenseNumberAndStatus($params['license_number'], Constant::STATUS_BOOKED);
            if($parkingData)
                return ['error' => true, 'message' => Constant::MSG_IS_REGISTERED, 'data' => (new ParkingTransformer())->transform($parkingData)];
            
            $emptySpace = $this->findOneSpaceByStatus(Constant::STATUS_EMPTY);
            if (!$emptySpace)
                return ['error' => true, 'message' => Constant::MSG_NO_SPACE_LEFT];
            
            $result = $this->createParking(
                $params['license_number'],
                $params['color'],
                $params['type'],
                $emptySpace->block,
                $emptySpace->number,
                $prices->{$params['type']}
            );

            $this->updateSpaceStatus($emptySpace->block, $emptySpace->number, Constant::STATUS_BOOKED);
        }
        catch (Exception $ex)
        {
            return ['error' => true, 'message' => $ex->getMessage()];
        }

        $result = (new ParkingTransformer())->transform($result);

        return [
            'error' => false,
            'message' => 'Successfully registered in parking space.',
            'data' => $result
        ];
    }

    public function checkout(Request $request){
        $params = $request->all();
        $result = array();

        $validator = Validator::make($params, [
            'license_number' => "required|string"
        ]);

        if ($validator->fails()) {
            return ['error' => true, 'message' => implode($validator->errors()->all(), " | ")];
        }

        try
        {
            $parkingData = $this->findOneParkingByLicenseNumberAndStatus($params['license_number'], Constant::STATUS_BOOKED);
            if(!$parkingData)
                return ['error' => true, 'message' => Constant::MSG_CAR_NOT_FOUND];

            $finalBill = self::calculateBill($parkingData->bill, $parkingData->type, $parkingData->created_at);
            $result = $this->updateParkingBillAndStatus($parkingData->license_number, $finalBill, Constant::STATUS_DONE);
            
            $this->updateSpaceStatus($parkingData->block, $parkingData->number, Constant::STATUS_EMPTY);
        }
        catch (Exception $ex)
        {
            return ['error' => true, 'message' => $ex->getMessage()];
        }

        $result = (new ParkingTransformer())->transform($result);

        return [
            'error' => false,
            'message' => 'Successfully registered out parking space.',
            'data' => $result
        ];
    }

    public function report(Request $request){
        $params = $request->all();
        $result = array();

        try
        {
            $result = $this->findManyParking();
            $result = collect($result)->sortBy('status')->sortBy('number')->sortBy('block');

            if (isset($params['type']) && !empty($params['type'])) {
                $result = $result->where('type', $params['type']);
            }

            if (isset($params['color']) && !empty($params['color'])) {
                $result = $result->where('color', $params['color']);
            }

            if (isset($params['status']) && !empty($params['status'])) {
                $result = $result->where('status', $params['status']);
            }
            
        }
        catch (Exception $ex)
        {
            return ['error' => true, 'message' => $ex->getMessage()];
        }

        $result = (new ParkingReportTransformer())->transform($result);

        return [
            'error' => false,
            'message' => 'Successfully get parking list.',
            'data' => $result
        ];
    }

    private function calculateBill($bill, $type, $dateIn){
        $dateIn = new Carbon($dateIn, 'Asia/Jakarta');
        $diff = $dateIn->diffInHours(Carbon::now('Asia/Jakarta')->toDateTimeString());

        $bill = $bill + (($bill * 0.2) * $diff);

        return $bill;
    }

}
