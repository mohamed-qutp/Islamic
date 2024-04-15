<?php
namespace App\Traits;


use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\AuthorizationException;

trait AuthorizeCheck {

    public function authorizCheck($permission)
    {

            if (!Auth::user()->can($permission)) {
                throw new AuthorizationException('Sorry, Un Authorized, This action for admins Only');
            }

    }
}