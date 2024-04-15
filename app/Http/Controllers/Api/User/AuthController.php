<?php

namespace App\Http\Controllers\Api\User;

use App\Models\User;
use App\Models\Image;
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

class AuthController extends Controller
{
    use MergeObjects;
    use ApiResponseHelper;
    use UploadImage;
    public function profile()
    {
        $user = User::findOrFail(Auth::user()->id) ;
        if($user -> image){
            $name = $user -> image->photo;
        }
        else{
            $name = null;
        }
        $user = $this->toArray($user, null, $name);
        return $this->setCode(200)->setMessage('Success')->setData($user)->send();
    }
//function to handle Register operation
public function Register(RegisterUserRequest $request)
{

    $name =$this->UploadImage($request, 'Users_images');
        $user = User::create([
            'name' => $request->name  ,
            'email'=> $request->email ,
            'password'=> Hash::make($request->password),
            'phone' => $request->phone ,
        ]);

        if($request->has('photo')){
            $user->image()->create([
                'photo' => $name == null ? null : env('APP_URL') . $name,
            ]);
        }
    $token = $user->createToken("API TOKEN")->plainTextToken;
    $user = $this->toArray($user, $token, $name ? env('APP_URL') . $name : null);
    return $this->setCode(200)->setMessage('User Created Successfully')->setData($user)->send();
}//End Method
    //function to handle Login operation
    public function Login(LoginRequest $request)
    {
        if (!Auth::attempt(['phone' => $request->phone, 'password' => $request->password]) )
        {
            $error = $request->header('lang') ? 'the credintials is not correct' :  'رفم الموبايل او الباسورد غير صحيح ' ;
            return response()->json(['status' => false,'message' => $error, 'code' => 401], 401);
        }
        $user = User::where('phone',$request->phone)->first();
        $token = $user->createToken("API TOKEN")->plainTextToken;

         if($user -> image){
            $name = $user -> image->photo;
        }
        else{
            $name = null;
        }
        $user = $this->toArray($user, $token, $name);
        return $this->setCode(200)->setMessage('User Logeed in Successfully')->setData($user)->send();
    }//End Method

    public function update(UpdateUserRequest $request)
    {
        try {
        $user = $request->user();

        if($user -> image){
            $name = $user->image->photo;
            $newName =$this->UploadImage($request, 'Users_images', $name);
        }
        else{
            $name = null ;
            $newName =$this->UploadImage($request, 'Users_images');
        }
        $user->update([
            'name' => $request->name ?? $user->name ,
            'email'=> $request->email ?? $user->email ,
            'password'=> Hash::make($request->password) ?? $user->password,
            'phone' => $request->phone ?? $user->phone,
            ]);
            if($newName){
                if($user -> image){
                    $user->image()->update([
                        'photo' => env('APP_URL') . $newName,
                    ]);
                }else{
                    $user->image()->create([
                        'photo' => env('APP_URL') . $newName,
                    ]);
                }
                $user = $this->toArray($user, null, env('APP_URL') . $newName );
            }
            else{
                $user = $this->toArray($user, null, $name ? $name : null);
            }


        return $this->setCode(200)->setMessage('User Updated Successfully')->setData($user)->send();
    } catch (ModelNotFoundException $exception) {
        return $this->setCode(404)->setMessage('User not found')->send();
    }
    }//End Method



    public function delete (Request $request)
    {
        try{
        // $this->authorizCheck('حذف المستخدمين');
        $user = $request->user();
        // Get the associated image record
        $image = Image::where('imageable_type', User::class)
        ->where('imageable_id', $user->id)
        ->first();

        // Check if the image exists and delete it
            if ($image) {
                // Delete the image file from storage if it exists
                if (File::exists(str_replace(env('APP_URL'), '', $image->photo))) {
                    File::delete(str_replace(env('APP_URL'), '', $image->photo));
                }

                // Delete the image record from the database
                $image->delete();
            }

            User::find($user->id)->delete();
            return $this->setCode(200)->setMessage('Success')->send();
        } catch (ModelNotFoundException $exception) {
            return $this->setCode(404)->setMessage('User not found')->send();
        }
    }

    //function to handle logout operation
    public function logout(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete();
        return $this->setCode(200)->setMessage('User Logged Out Successfully')->send();
    }//End Method
}
