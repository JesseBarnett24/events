<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller implements HasMiddleware
{
    // Apply authentication middleware to ensure only logged-in users can access these routes
    // @return array
    public static function middleware(): array {
        return [ new Middleware('auth') ];
    }

    // Display a paginated list of all users
    // @return \Illuminate\View\View
    public function index()
    {
        $users = User::paginate(10);
        return view('users.index', compact('users'));
    }
}
