<?php
namespace App\Traits;

use App\Models\Brand;
use App\Models\Color;
use App\Models\Image;
use App\Models\Branch;
use App\Models\Cartype;
use App\Models\Fueltype;
use App\Models\Bodystyle;

use App\Models\Transmission;
use Illuminate\Support\Facades\App;

trait CarDetails{
    public function CarDetails($cars )
    {
        $responseData = [];
        $car_images =[];


            foreach ($cars as $car) {
                $images = Image::select('id','photo')->where('imageable_type','App\Models\Car')->where('imageable_id',$car->id)->get();
                foreach ($images as $image => $photo) {
                    $car_images[] = [
                        'id' => $photo->id,
                        'image' => $photo->photo,
                    ];
                }





            $brand = Brand::select('name_' . App::currentLocale() . ' as name')->where('id',$car->brand_id)->get();
            $cartype = Cartype::select('name_' . App::currentLocale() . ' as name')->where('id',$car->cartype_id)->get();
            $branch = Branch::select('name_' . App::currentLocale() . ' as name')->where('id',$car->branch_id)->get();
            $fueltype = Fueltype::select('name_' . App::currentLocale() . ' as name')->where('id',$car->fueltype_id)->get();
            $color = Color::select('name_' . App::currentLocale() . ' as name')->where('id',$car->color_id)->get();
            $transmission = Transmission::select('name_' . App::currentLocale() . ' as name')->where('id',$car->transmission_id)->get();
            $bodystyle = Bodystyle::select('id','name_' . App::currentLocale() . ' as name')->where('id',$car->bodystyle_id)->get();

            $responseData[] = [
                'id' => $car -> id ,
                'name'=>App::currentLocale() ==='ar'? $car->name_ar:$car->name_en,
                'day_price'=>$car ->day_price,
                'horse_power'=>$car ->horse_power,
                'passenger'=>$car ->passenger,
                'description' => $car -> description,
                'brand' => $brand->first()->name,
                'cartype' => $cartype->first()->name,
                'branch' => $branch->first()->name,
                'fueltype' => $fueltype->first()->name,
                'color' => $color->first()->name,
                'transmission' => $transmission->first()->name,
                'bodystyle' => $bodystyle->first()->name,
                'code'=>$car ->code,
                'kilometer'=>$car ->kilometer,
                'sn'=>$car ->sn,
                'gbs'=>$car ->gbs,
                'year'=>$car ->year,
                'available' => $car ->available,
                'count_of_rent' =>$car ->count_of_rent,
                'Car_images' => $car_images
            ];
        }
        return $responseData;
    }
}
