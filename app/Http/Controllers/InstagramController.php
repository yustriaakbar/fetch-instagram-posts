<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InstagramPost;
use App\Models\InstagramUser;
use Illuminate\Support\Facades\Http;
use Socialite;
use Exception;
use Illuminate\Support\Carbon;
use DB;

class InstagramController extends Controller
{
    public function loginInstagram()
    {
        $url = Socialite::driver('instagrambasic')
            ->redirect()
            ->getTargetUrl();
        return redirect()->away($url);
    }

    public function instagramCallback()
    {
        $driver = Socialite::driver('instagrambasic');
        $credential = $driver->getAccessTokenResponse(request('code'));
        $userdata = $driver->userFromToken($credential['access_token']);

        $response = Http::get(config('services.instagrambasic.token_url'), [
            'grant_type' => 'ig_exchange_token',
            'client_secret' => config('services.instagrambasic.client_secret'),
            'access_token' => $credential['access_token']
        ]);

        $new_token = json_decode($response->body(), true);

        $instagram_user = InstagramUser::where('username', $userdata->user['username'])->first();
        if ($instagram_user == null) {
            $create = InstagramUser::create([
                'username' => $userdata->user['username'],
                'user_id' => $userdata->id,
                'state' => request('state'),
                'access_token' => $new_token['access_token'],
                'token_expired_at' => now()->addSeconds($new_token['expires_in'])
            ]);
        } else {
            $update = $instagram_user->update([
                'state' => request('state'),
                'access_token' => $new_token['access_token'],
                'token_expired_at' => now()->addSeconds($new_token['expires_in'])
            ]);
        }

        return redirect('/');
    }

    public function instagramFetchPost(Request $request)
    {
        $credential = InstagramUser::where('username', $request->username)->first();
        $response = Http::get(
            'https://graph.instagram.com' . "/{$credential['user_id']}/media",
            [
                'access_token' => $credential['access_token'],
                'fields' => 'id,media_url,media_type,permalink,thumbnail_url,timestamp,caption'
            ]
        );
        $data = json_decode($response->body(), true)['data'];

        foreach ($data as $feed) {
            $parsed_feed[] = [
                'media_id' => $feed['id'],
                'type' => $feed['media_type'],
                'permalink' => $feed['permalink'] ?? null,
                'caption' => $feed['caption'] ?? null,
                'path' => $feed['media_url'] ?? null,
                'created_at' => Carbon::create($feed['timestamp']) ?? null,
            ];
        }

        $delete_posts = InstagramPost::truncate();

        $upsert_medias = InstagramPost::insert(
            $parsed_feed,
            [
                'media_id',
            ],
            [
                'type',
                'permalink',
                'caption',
                'path',
                'created_at'
            ]
        );

        return redirect('instagram-feeds');
    }

    public function instagramFeed()
    {
        $instagram_posts = InstagramPost::get();
        return view('instagram_feed', compact('instagram_posts'));
    }
}
