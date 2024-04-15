<?php
namespace App\Traits;
use App\Models\User;

use Illuminate\Support\Facades\App;


trait MergeObjects {
    public function toArray($user, $token = null, $photo = null)
    {
        $user = User::findOrFail($user->id);
        if($token)
        {
            $user["token"] = collect($token)->flatten(1)[0];
        }

        if($photo)
        {
            $user["photo"] = collect($photo)->flatten(1)[0];
        }

        return $user;

    }
}
