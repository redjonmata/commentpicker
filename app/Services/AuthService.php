<?php
namespace App\Services;

use Tymon\JWTAuth\JWTAuth;
use JWTAuth as JWT;

use ChannelLog as Log;
use App\Models\User;

use App\Exceptions\UserBlockedException;
use App\Exceptions\UserNotFoundException;
use App\Exceptions\UserNoRoleException;

class AuthService
{
    protected $jwt;
    protected $loginData;

    public function __construct(array $loginData = null)
    {
        $this->loginData = $loginData;
    }

    /**
     * @param \Tymon\JWTAuth\JWTAuth $jwt
     *
     * @return void
     */
    public function setJwt(JWTAuth $jwt): void
    {
        $this->jwt = $jwt;
    }

    /**
     * @return array
     */
    public function login(): array
    {
        $token = $this->jwtLogin();

        $user = $this->jwt->user();

        (new AuditService)->customAudit('login', $user);

        Log::info('audit', 'Login', ['user' => $user['id']]);

        return [
            'token' => $token,
            'user' => $user
        ];
    }

    /**
     * Invalidate token
     * Audit logout event
     *
     * @return void
     */
    public function logout(): void
    {
        $user = JWT::user();

        (new AuditService)->customAudit('logout', $user);

        Log::info('audit', 'Logout', ['user' => $user->id]);

        $this->jwt->parseToken()->invalidate();
    }

    /**
     * Get token
     *
     * @return string
     * @throws UserNotFoundException when no user in database
     */
    private function jwtLogin(): string
    {
        if (!$token = $this->jwt->attempt($this->loginData)) {
            throw new UserNotFoundException(trans('messages.user.not_found'));
        }

        return $token;
    }

    /**
     * @param \App\Models\User $user
     *
     * @return void
     * @throws UserBlockedException if user is not super admin and blocked
     */
    private function checkIfUserBlocked(User $user): void
    {

        if (!$user['is_super_admin'] && isset($user['blocked_at'])) {
            throw new UserBlockedException(trans('messages.user.is_blocked'));
        }
    }

    /**
     * @param \App\Models\User $user
     *
     * @return void
     *
     * @throws UserNoRoleException if user attempting login has no current roles assigned
     */
    private function checkIfUserHasRole(User $user): void
    {
        if(!$user->roles()->exists()){
            throw new UserNoRoleException(trans('messages.user.has_no_role'));
        }
    }
}