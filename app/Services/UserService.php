<?php

namespace App\Services;

use App\Models\User;
use App\Services\LDAPService;
use App\Transformers\UserTransformer;
use App\Services\Search\UserSearchService;
use App\Services\Utils\RoleHierarchyService;

use DB;
use JWTAuth;
use Exception;
use ChannelLog as Log;

class UserService
{
    /**
     * @param int $id
     *
     * @return array
     */
    public function getUser(int $id)
    {
        $user = User::whereId($id)
            ->with(['roles' => function ($query) {
                $query->select('id', 'name', 'permissions');
            }, 'businessUnit' => function ($query) {
                $query->select('id', 'name', 'permissions');
            }])
            ->firstOrFail();

        return (new UserTransformer)->modelTransform($user);
    }

    /**
     * @param array $requestData
     *
     * @return Collection|array
     */
    public function index(array $requestData)
    {
        $loggedUser = JWTAuth::user();

        if (isset($requestData['term'])) {
            return User::where(DB::raw('concat(first_name," ",last_name)'), 'LIKE', '%' . $requestData['term'] . '%')
                ->take(10)
                ->get();
        }

        $users = User::where('id', '<>', $loggedUser->id)
            ->orderBy('created_at', 'desc');

        $searchService = new UserSearchService($users, $requestData, $loggedUser);

        $users = $searchService->search();

        $perPage = $requestData['per_page'] ?? 10;

        $users = $users->paginate($perPage);

        return (new UserTransformer)->paginationTransform($users);
    }

    /**
     * @param array $data
     *
     * @return \App\Models\User
     */
    public function store(array $data): User
    {
        $user = User::create($data);

        Log::info('audit', 'store user', ['user' => $user->id]);

        return $user;
    }

    /**
     * @param \App\Models\User $user
     * @param array $data
     *
     * @return \App\Models\User
     */
    public function update(User $user, array $data): User
    {
        $authUser = JWTAuth::user();

        $user->update($data);

        Log::info('audit', 'update user', ['author' => $authUser->id, 'user' => $user->id]);

        return $user;
    }

    /**
     * @param \App\Models\User $user
     *
     * @return void
     */
    public function delete(User $user): void
    {
        $userId = JWTAuth::user()->id;

        if (!$user->delete()) {
            throw new Exception(trans('messages.user_delete_error'));
        }

        Log::info('audit', 'delete user', ['author' => $userId, 'user' => $user->id]);
    }

    /**
     * @param \App\Models\User $user
     *
     * @return void
     */
    public function restore(User $user): void
    {
        $userId = JWTAuth::user()->id;

        if (!$user->restore()) {
            throw new Exception(trans('messages.user_restore_error'));
        }

        Log::info('audit', 'restore user', ['author' => $userId, 'user' => $user->id]);
    }

    /**
     * @param int $id
     *
     * @return void
     */
    public function forceDelete(int $id): void
    {
        $userId = JWTAuth::user()->id;
        $user = User::onlyTrashed()->findOrFail($id);
        $user->forceDelete();

        Log::info('audit', 'Force delete user', ['author' => $userId, 'user' => $id]);
    }
}
