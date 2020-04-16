<?php

namespace App\Http\Helpers;

class Constant {

    const STATUS_EMPTY = 'empty';
    const STATUS_BOOKED = 'booked';
    const STATUS_DONE = 'done';

    //RESPONSE MESSAGE
    const MSG_TRANSACTION_FAIL = "Data transaction failed, please try again.";
    const MSG_NO_SPACE_LEFT = "There's no space left, please add more space.";
    const MSG_IS_REGISTERED = "This car has been registered.";
    const MSG_CAR_NOT_FOUND = "There's no car with that license number.";

}