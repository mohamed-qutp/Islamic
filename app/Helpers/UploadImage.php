<?php

namespace App\Helpers;

use Illuminate\Support\Facades\File;


trait UploadImage
{

    function uploadImage($request, $folder, $image=null)
    {

        if ($request->hasFile('photo'))
        {
            if (File::exists(str_replace(env('APP_URL'), "", $image))) {
                File::delete(str_replace(env('APP_URL'), "", $image));
            }
            $image = $request->file("photo");
            $ext = strtolower($image->getClientOriginalExtension());
            $name = "uploads/$folder/" . time() . ".$ext";
            $image->move("uploads/$folder/", $name);
            return $name;
        }
        else{
            return null;
        }
    }
}


