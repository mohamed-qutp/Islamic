<?php
namespace App\Traits;

use App\Models\Image;
use Illuminate\Database\Eloquent\Relations\MorphOne;




trait Imageable{
    public function image(): MorphOne
        {
            return $this->morphOne(Image::class, 'imageable');
        }
}
