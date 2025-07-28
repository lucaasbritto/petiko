<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User\User;

class UserController extends Controller
{
    public function index(){
        return User::where('is_admin', false)->select('id', 'name')->get();
    }
}
