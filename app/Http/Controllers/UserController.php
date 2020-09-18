<?php
namespace App\Http\Controllers;

use DB;
use Exception;
use Illuminate\Http\Request;

use App\Models\User;
use App\Enums\ReturnType;
use App\Services\UserService;
use App\Services\ExportService;
use App\Http\Requests\UserRequest;
use App\Services\Exports\UserExportService;

class UserController extends Controller
{
    /**
     * @var \App\Services\UserService
     */
    protected $userService;

    /**
     * @param \App\Services\UserService $userService
     *
     * @return void
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {

            $users = $this->userService->index($request->all());

        } catch (Exception $ex) {

            return $this->errorResponse($ex);

        }

        return response()->json($users);
    }

    /**
     * @param  \App\Http\Requests\UserRequest  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        try {

            DB::beginTransaction();

            $user = $this->userService->store($request->all());

            DB::commit();

        } catch (Exception $ex) {

            DB::rollback();

            return $this->errorResponse($ex);
        }

        return response()->json([
            'type' => ReturnType::SUCCESS,
            'user' => $user
        ], 201)->header('Location', route('users.show', $user->id));
    }

    /**
     * @param  int  $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {

            $user = $this->userService->getUser($id);

        } catch (Exception $ex) {

            return $this->errorResponse($ex);

        }

        return response()->json($user);
    }

    /**
     * @param \App\Http\Requests\UserRequest  $request
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(UserRequest $request, int $id)
    {
        try {

            $userEntity = User::findOrFail($id);

            DB::beginTransaction();

            $user = $this->userService->update($userEntity, $request->all());

            DB::commit();

        } catch (Exception $ex) {

            DB::rollback();

            return $this->errorResponse($ex);

        }

        return response()->json($user);
    }

    /**
     * @param  int  $id
     *
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        try {

            $userEntity = User::findOrFail($id);

            $this->userService->delete($userEntity);

        } catch (Exception $ex) {

            return $this->errorResponse($ex);

        }

        return response()->json(null, 204);
    }

    /**
     * @param  int  $id
     *
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        try {

            $userEntity = User::withTrashed()->findOrFail($id);

            $this->userService->restore($userEntity);

        } catch (Exception $ex) {

            return $this->errorResponse($ex);

        }

        return response()->json($userEntity);
    }

    /**
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function forceDelete(int $id)
    {
        try{

            $this->userService->forceDelete($id);

        } catch(Exception $ex) {

            return $this->errorResponse($ex);
        }

        return response()->json(null, 204);
    }
}
