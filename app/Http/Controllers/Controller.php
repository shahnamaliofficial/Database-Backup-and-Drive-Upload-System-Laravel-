<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public function Backup()
    {
        $googleOauthURL = 'https://accounts.google.com/o/oauth2/auth?scope=' . config('customeenv.GOOGLE_OAUTH_SCOPE') . '&redirect_uri=' . config('customeenv.REDIRECT_URI') . '&response_type=code&client_id=' . config('customeenv.GOOGLE_CLIENT_ID') . '&access_type=online';
        header("Location: $googleOauthURL");
       
        exit();
    }
}
