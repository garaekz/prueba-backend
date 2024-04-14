<?php

namespace App\Controllers;

use App\Actions\StoreUserAction;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Garaekz\Http\Request;
use App\Models\User;

/**
 * This class represents the controller for managing user-related operations.
 * It extends the base Controller class.
 */
class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return void|HttpResponse
     */
    public function index(Request $request)
    {
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 10);
        $users = User::paginate($page, $perPage);
        return $this->jsonPaginate($users);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return void|HttpResponse
     */
    public function show($id)
    {
        try {
            $user = User::findOrFail($id);
            return $this->jsonResponse($user);
        } catch (\Exception $e) {
            return $this->jsonResponse([
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return void|HttpResponse
     */
    public function store(StoreUserRequest $request, StoreUserAction $action)
    {
        try {
            $data = $request->validated();
            $user = $action->execute($data);
            return $this->jsonResponse($user, 201);
        } catch (\Exception $e) {
            return $this->jsonResponse([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return void|HttpResponse
     */
    public function update(UpdateUserRequest $request, $id, StoreUserAction $action)
    {
        $data = $request->validated();
        $user = $action->execute($data, $id);

        return $this->jsonResponse($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return void|HttpResponse
     */
    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) {
            return $this->jsonResponse([
                'message' => 'Resource not found',
            ], 404);
        }

        User::destroy($id);

        return $this->jsonResponse([
            'message' => 'User deleted successfully',
        ], 204);
    }
}
