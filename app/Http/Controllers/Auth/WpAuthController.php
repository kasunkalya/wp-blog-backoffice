<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class WpAuthController extends Controller
{
    public function redirect()
    {
        $query = http_build_query([
            'client_id' => config('services.wordpress.client_id'),
            'redirect_uri' => config('services.wordpress.redirect'),
            'response_type' => 'code',
            'scope' => 'global',
        ]);

        return redirect('https://public-api.wordpress.com/oauth2/authorize?' . $query);
    }

    public function callback(Request $request)
    {
        $code = $request->get('code');
        abort_unless($code, 400, 'Authorization failed');
        $resp = Http::asForm()->post('https://public-api.wordpress.com/oauth2/token', [
            'client_id' => config('services.wordpress.client_id'),
            'client_secret' => config('services.wordpress.client_secret'),
            'redirect_uri' => config('services.wordpress.redirect'),
            'grant_type' => 'authorization_code',
            'code' => $code,
        ]);

     
        abort_if($resp->failed(), 401, 'Token exchange failed');

        $token = $resp->json();        
        $accessToken = $token['access_token'] ?? null;
         abort_unless($accessToken, 401, 'No access token');
        $me = Http::withToken($accessToken)
        ->get('https://public-api.wordpress.com/rest/v1.1/me')
        ->json();
    
        $email = $me['email'] ?? ("wp_{$me['ID']}@example.local");

        $user = User::firstOrCreate(
            ['email' => $email],
            ['name' => $me['display_name'] ?? 'WP User']
        );
        $user->wp_token = json_encode($token);
        $user->save();
       
        $me = Http::withToken($accessToken)
            ->get('https://public-api.wordpress.com/rest/v1.1/me')
            ->json();

        $site = env('WP_SITE_SLUG');
        $meOnSite = Http::withToken($accessToken)
            ->get("https://public-api.wordpress.com/wp/v2/sites/{$site}/users/me")
            ->json();

        $isAdmin = false;
        if (is_array($meOnSite)) {
            $roles = $meOnSite['roles'] ?? [];
            $caps = array_keys($meOnSite['capabilities'] ?? []);
            $isAdmin = in_array('administrator', $roles) || in_array('manage_options', $caps);
        }

        // if (! $isAdmin) {
        //     return redirect('/back-office')->with('error', 'Only WordPress administrators can log in.');
        // }
     
        $user = User::firstOrCreate(
            ['email' => $me['email'] ?? ("wp_{$me['ID']}@example.local")],
            ['name' => $me['display_name'] ?? 'WP User']
        );

        $user->wp_user_id = (string)($me['ID'] ?? null);
        $user->wp_token = json_encode($token);
        $user->save();

     
        Auth::login($user, true);

        return redirect('/back-office');
    }
}