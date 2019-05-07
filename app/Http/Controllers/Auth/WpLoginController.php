<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;

class WpLoginController extends Controller{

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function auth(Request $request){
        $user_email = $request->get('user_email');
        $user = User::where('email', $user_email)->first();
        if($user){
            auth()->login($user);
        }
        return redirect()->route('leave-days.index');
    }

    public function authAlex(\App\User $user){
        if(auth()){
            auth()->logout();
        }
        auth()->login($user);
        return redirect()->route('leave-days.index');
    }
    /**
     * Method to redirect a user to the Wordpress application after logging out.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function logout()
    {
        auth()->logout();
        return redirect(env('WIKI_URL'));
    }

}
