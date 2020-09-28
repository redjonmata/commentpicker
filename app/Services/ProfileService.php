<?php
namespace App\Services;

use App\Models\User;
use App\Exceptions\PasswordNotMatchException;

use JWTAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileService
{
    /**
     * Change user password
     * 
     * @param array $data
     * @param \App\Models\User $user
     * 
     * @return void
     * 
     * @throws \App\Exceptions\PasswordNotMatchException if old password confirmation do not match
     */
    public function changePassword(array $data, User $user): void
    {
        $passwordIsRight = Hash::check($data['old_password'] , $user->password );

        if (!$passwordIsRight) {
           throw new PasswordNotMatchException(trans('messages.user.old_password_wrong'));
        }  

        $user->update([
            'password' => $data['password']
        ]);        
    }  

    /**
     * @param \Illuminate\Http\Request $request
     * 
     * @return \App\Models\User
     */
    public function uploadImage(Request $request): User
    {
        $hashName = $request->image->hashName();

        $imagesFolderPath = config('app.user_images');

        $path = public_path($imagesFolderPath);

        # helper method
        create_directory(public_path(), $path);

        $fullPath = $imagesFolderPath.'/'.$hashName;

        $request->image->move($path, $hashName);
        
        return $this->saveImage($fullPath);
    }

    /**
     * @param array $requestData
     * 
     * @return \App\Models\User
     */
    public function updateProfile(array $requestData): User
    {
        $user = JWTAuth::user();

        $user->update([
            'language' => $requestData['language']
        ]);

        return $user;
    }

    /**
     * Save user image
     * 
     * @param string $path
     * 
     * @return \App\Models\User
     */
    private function saveImage(string $path): User
    {
        $user = JWTAuth::user();

        $user->update([
            'profile_picture' => $path
        ]);

        return $user;
    }

}