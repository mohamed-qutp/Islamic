<?php

namespace App\Http\Controllers\Api\User;

use App\Models\Reservation;
use Illuminate\Http\Request;
use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Api\Reservation\ReservationStoreRequest;
use App\Models\Car;

class ReservationController extends Controller
{
    use ApiResponseHelper;
    //Get all Reservations
    public function rents (Request $request)
    {
        $per_page = (int) ($request->per_page ?? 10);
        $pageNumber = (int) ($request->current_page ?? 1);
        $user = Auth::user();
        // dd($user);
        $reservations = Reservation::with('car')->with('user')->where('user_id',$user->id)->paginate($per_page, ['*'], 'page', $pageNumber);
        return $this->setCode(200)->setMessage('Success')->setData($this->formatData($reservations))->send();
    }

    public function store(ReservationStoreRequest $request)
    {
        $user = Auth::user();
        $reservation = Reservation::create([
            'user_id' => $user->id,
            'car_id' => $request->car_id,
            'received_date' => $request->received_date,
            'returned_date' => $request->returned_date,
        ]);
        return $this->setCode(200)->setMessage('Success')->setData($reservation)->send();
    }
}
