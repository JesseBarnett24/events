<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller implements HasMiddleware
{
    public static function middleware(): array {
        return [ new Middleware('auth') ];
    }

    public function index()
    {
        $users = User::paginate(10);
        return view('users.index', compact('users'));
    }
}
