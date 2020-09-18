<?php

namespace App\Http\Controllers;

use Tymon\JWTAuth\JWTAuth;
use Illuminate\Http\Request;

use App\Enums\ReturnType;
use App\Services\AuthService;
use App\Exceptions\UserBlockedException;
use App\Exceptions\UserNotFoundException;
use App\Exceptions\UserNoRoleException;


class AuthController extends Controller
{
    /**
     * @var \Tymon\JWTAuth\JWTAuth
     */
    protected $jwt;


    /**
     * Constructor
     *
     * @param \Tymon\JWTAuth\JWTAuth $jwt
     * @return void
     */
    public function __construct(JWTAuth $jwt)
    {
        $this->jwt = $jwt;
    }

    /**
     * User login
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function postLogin(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email|max:255',
            'password' => 'required',
        ]);

        $authService = new AuthService($request->all());
        $authService->setJwt($this->jwt);

        try {

            $loggedIn = $authService->login();

            return response()->json(['type' => ReturnType::SUCCESS] + $loggedIn);

        } catch (UserBlockedException $blocked) {

            return $this->errorResponse($blocked, 401);

        } catch (UserNotFoundException $notFound) {

            return $this->errorResponse($notFound, 404);

        } catch (\Exception $ex) {

            return $this->errorResponse($ex);

        }
    }

    /**
     * User logout
     *
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
        $authService = new AuthService;

        $authService->setJwt($this->jwt);

        $authService->logout();

        return response()->json(null, 204);
    }
}
