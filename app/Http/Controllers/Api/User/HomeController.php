<?php

namespace App\Http\Controllers\Api\User;

use App\Models\Car;
use App\Models\User;
use App\Models\Brand;
use App\Models\Image;
use App\Models\Cartype;
use App\Models\Contract;
use App\Traits\CarDetails;
use App\Helpers\UploadImage;
use App\Traits\MergeObjects;
use Illuminate\Http\Request;
use App\Helpers\ApiResponseHelper;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\Auth\UpdateUserRequest;
use App\Http\Requests\Api\Auth\RegisterUserRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class HomeController extends Controller
{
    use MergeObjects;
    use ApiResponseHelper;
    use UploadImage;
    use CarDetails;


public function home (Request $request)
    {
        $brands = Brand::select('id','name_' . App::currentLocale() . ' as name')->withCount('cars')->with('image')->get();
        $responseData = [];
        $brandsArr = [];
        foreach ($brands as $brand) {
            $brandsArr[] = [
                'id' => $brand->id,
                'name' => $brand->name,
                'photo' => $brand->image ? $brand->image->photo : null,
                'cars_count' => $brand -> cars_count
            ];
        }
        $carTypes = Cartype::select('id','name_' . App::currentLocale() . ' as name' )->withCount('cars')->with('image')->get();
        $carTypesArr = [];

        foreach ($carTypes as $carType) {
            $carTypesArr[] = [
                'id' => $carType->id,
                'name' => $carType->name,
                'photo' => $carType->image ? $carType->image->photo : null,
                'cars_count' => $carType -> cars_count
            ];
        }
        $per_page = (int) ($request->per_page ?? 10);
        $pageNumber = (int) ($request->current_page ?? 1);

            $cars = Car::orderBy('count_of_rent', 'DESC')
            // ->with('branch')
                ->paginate($per_page, ['*'], 'page', $pageNumber);

                // $details_Arr = $this->CarDetails($cars);

                $responseData = [
                    'brands' => $brandsArr,
                    'carTypes' => $carTypesArr,
                    'cars' => $this->formatData($cars)
                ];

    return $this->setCode(200)->setMessage('Success')->setData($responseData)->send();
    }

    public function rents (Request $request)
    {
        $per_page = (int) ($request->per_page ?? 10);
        $pageNumber = (int) ($request->current_page ?? 1);
        $user = Auth::user();
        $contracts = Contract::with('car')->with('user')->where('user_id',$user->id)->paginate($per_page, ['*'], 'page', $pageNumber);
        return $this->setCode(200)->setMessage('Success')->setData($this->formatData($contracts))->send();
    }


}
