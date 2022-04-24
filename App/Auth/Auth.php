<?php

namespace App\Auth;

use App\Controllers\UsersController;

class Auth {

    public static function AuthenticateUser($db, $credentials) {
        $userController = new UsersController($db, $credentials);
        $user = $userController->logUser();
        return $user;
    }
}