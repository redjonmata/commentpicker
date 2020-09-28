<?php
namespace App\Http\Controllers;

use JWTAuth;
use Exception;
use Illuminate\Http\Request;

use App\Enums\ReturnType;
use App\Services\ProfileService;
use App\Exceptions\PasswordNotMatchException;

class ProfileController extends Controller
{
    /**
     * @var \App\Services\ProfileService
     */
    private $profileService;

    /**
     * @param \App\Services\ProfileService $profileService
     * 
     * @return void
     */
    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    /**
     * @param \Illuminate\Http\Request  $request
     * 
     * @return \Illuminate\Http\Response
     */
    public function passwordChange(Request $request)
    {
        $this->validate($request, [
            'password' => 'required|confirmed|min:6'
        ]);        

        try {

          $user = JWTAuth::user();          

          $this->profileService->changePassword($request->all(), $user);

        } catch (PasswordNotMatchException $ex) {

            return response()->json(['old_password' => [ $ex->getMessage() ]], 422);

        } catch (Exception $ex) {

            return $this->errorResponse($ex);   

        }

        return response()->json([
            'type' => ReturnType::SUCCESS,
            'message' => trans('messages.password_change_success'),
        ]);        
    }

    /**
     * Change profile data (language)
     *
     * @param \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function updateProfile(Request $request)
    {
        $this->validate($request, [
            'language' => 'in:en,it'
        ]);

        try {

            $user = $this->profileService->updateProfile($request->all());
        
        } catch (Exception $ex) {
        
            return $this->errorResponse($ex);
        
        }

        return response()->json($user);
    }

    /**
     * Change profile image
     *
     * @param \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function uploadImage(Request $request)
    {
        $this->validate($request, [
            'image' => 'required|mimes:jpeg,jpg,png|max:10000'
        ]);

        try {

            $user = $this->profileService->uploadImage($request);

        } catch (Exception $ex) {

            return $this->errorResponse($ex);

        }

        return response()->json($user);
    }

}
