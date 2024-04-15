<?php
namespace App\Helpers;

use Illuminate\Support\Facades\File;

trait MultibleImage
{
    function MultibleImage($request, $folder, $updateImage = null)
    {
        $uploadedImages = [];

        if ($request->hasFile('photos')) {
            if($updateImage)
            foreach ($request->file("photos") as $image)
            {
                if (File::exists(str_replace(env('APP_URL'), "", $updateImage)))
                {
                    File::delete(str_replace(env('APP_URL'), "", $updateImage));
                }
            }
            foreach ($request->file("photos") as $image) {
                if ($image) {
                    $ext = strtolower($image->getClientOriginalExtension());
                    $name = time() . '_' . uniqid() . ".$ext"; // Using time and uniqid to ensure unique filenames
                    $image->move("uploads/$folder/", $name);
                    $uploadedImages[] = env('APP_URL')."uploads/$folder/". $name;
                }else{
                    continue;
                }



            }
        }
        // dd($uploadedImages);
        return $uploadedImages;
    }
}
