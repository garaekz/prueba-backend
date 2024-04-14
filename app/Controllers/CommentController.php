<?php

namespace App\Controllers;

use App\Actions\StoreCommentAction;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Models\User;
use Garaekz\Http\Request;
use App\Models\UserComment;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return void|HttpResponse
     */
    public function index(Request $request, $userId)
    {
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 10);
        $users = UserComment::paginate($page, $perPage, ['user' => $userId]);
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
            $comment = UserComment::findOrFail($id);
            return $this->jsonResponse($comment);
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
    public function store(StoreCommentRequest $request, $userId, StoreCommentAction $action)
    {
        $exists = User::exists($userId);
        if (!$exists) {
            return $this->jsonResponse([
                'message' => 'User not found',
            ], 404);
        }

        $data = $request->validated();
        $data['user'] = $userId;
        $comment = $action->execute($data);

        return $this->jsonResponse($comment, 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return void|HttpResponse
     */
    public function update(UpdateCommentRequest $request, $id, StoreCommentAction $action)
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
        $user = UserComment::find($id);
        if (!$user) {
            return $this->jsonResponse([
                'message' => 'Resource not found',
            ], 404);
        }

        UserComment::destroy($id);

        return $this->jsonResponse([
            'message' => 'User deleted successfully',
        ], 204);
    }
}
