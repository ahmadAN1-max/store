<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected function redirectTo()
    {
        $user = Auth::user();

        if (in_array($user->utype, ['POS', 'POSADM'])) {
            return route('pos.index'); 
        } elseif ($user->utype === 'ADM') {
            session(['AdminRole' => true]);
            return route('admin.index');
        }
        //elseif ($user->utype === 'USR') {
        //     return '/account-dashboard';
        // }

        return '/';
    }



    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }
}
