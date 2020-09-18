<?php

namespace App\Http\Controllers;

use App\Enums\ReturnType;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\RedirectResponse;
use Socialite;

class SocialController extends Controller
{
    /**
     * Redirects to appropriate providers based on
     * $provider
     *
     * @param string $provider
     * @return RedirectResponse
     */
    public function redirectToProvider(string $provider) : RedirectResponse
    {
        switch ($provider) {
            case 'facebook':
                return Socialite::driver('facebook')->stateless()->redirect();
            case 'instagram':
                return Socialite::driver('instagram')->stateless()->scopes('user_profile,user_media')->redirect();
        }
    }

    /**
     * Undocumented function
     *
     * @param string $provider
     * @return RedirectResponse
     */
    public function handleProviderCallback(string $provider) : RedirectResponse
    {
        $data = Socialite::driver('facebook')->stateless()->user();
        return $this->handleUser($data, $provider);
    }

    /**
     * Handles the user's information and creates/updates
     * the record accordingly.
     *
     * @param object $data
     * @param string $provider
     * @return RedirectResponse
     */
    public function handleUser(object $data, string $provider) : RedirectResponse
    {
        $user = User::where([
            'access_data->'.$provider.'->id' => $data->id,
        ])->first();
        if (!$user) {
            /**
             * If we don't user associated with the facebook id, then
             * check for the user's email and associate the facebook id
             */
            $user = User::where([
                'email' => $data->email,
            ])->first();
        }
        if (!$user) {
            return $this->createUser($data, $provider);
        }
        $user->access_data->facebook->token = $data->token;
        $user->save();
        return $this->login($user);
    }

    /**
     * Undocumented function
     *
     * @param object $data
     * @param string $provider
     * @return RedirectResponse
     */
    public function createUser(object $data, string $provider) : RedirectResponse
    {
        try {
            $user = new User;
            $user->first_name   = $data->name;
            $user->email  = $data->email;
            $user->password  = "test";
            $user->access_data = json_encode([
                $provider => [
                    'id'    => $data->id,
                    'token' => $data->token
                ]
            ]);
            $user->save();
            return $this->login($user);
        } catch (Exception $e) {
            return redirect(route('login'))->with(['status' => 'Login failed. Please try again']);
        }
    }

    /**
     * Logins the given user and redirects to home
     *
     * @param User $user
     * @return RedirectResponse
     */
    public function login(User $user) : RedirectResponse
    {
        $data = [
            'email' => $user->email,
            'password' => 'test'
        ];

        $authService = new AuthService($data);
        $loggedIn = $authService->login();

        return response()->json(['type' => ReturnType::SUCCESS] + $loggedIn);
    }
}