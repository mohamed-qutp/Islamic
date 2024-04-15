<?php
namespace App\Traits;

use Illuminate\Support\Facades\File;


trait ContractImages
{
    function ContractImages($data, $folder)
    {
        $uploadedImages = [];
        if($folder ==='Contract_images/Recived_images')
        {
            $imageFields = [
                'received_front',
                'received_front2',
                'received_back',
                'received_back2',
                'received_right',
                'received_right2',
                'received_left',
                'received_left2'
            ];
        }
        else{
            $imageFields = [
                'returned_front',
                'returned_front2',
                'returned_back',
                'returned_back2',
                'returned_right',
                'returned_right2',
                'returned_left',
                'returned_left2',
            ];
        }

        foreach ($imageFields as $field) {
            if ($data->hasFile($field)) {
                $photo = $data->file($field);
                $ext = strtolower($photo->getClientOriginalExtension());
                $name = time() . '_' . uniqid() . ".$ext"; // Using time and uniqid to ensure unique filenames
                $photo->move("uploads/$folder/", $name);
                $uploadedImages[$field] = env('APP_URL') . "uploads/$folder/" . $name;
            }
        }

        return $uploadedImages;
    }

}
