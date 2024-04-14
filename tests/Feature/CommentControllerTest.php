<?php

use App\Actions\StoreCommentAction;
use App\Actions\StoreUserAction;
use App\Controllers\CommentController;
use App\Controllers\UserController;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use Garaekz\Http\Request;
use Garaekz\Services\Database;

beforeEach(function () {
    $db = Database::getInstance();
    $db->initializeForTests();
    $this->pdo = $db->getConnection();
    $this->request = new Request();
});

test('store a new comment', function () {
    $user = $this->createUser($this->pdo, 1);
    // Check we created a user
    expect($this->getTableCount($this->pdo, 'user'))->toBe(1);
    // Check we have no comments
    expect($this->getTableCount($this->pdo, 'user_comment'))->toBe(0);

    $controller = new CommentController();
    $userRequest = new StoreCommentRequest();
    $userRequest->setData([
        'comment' => 'This is a comment',
        'likes' => 10,
    ]);

    $action = new StoreCommentAction();
    $response = $controller->store($userRequest, $user['id'], $action);
    $parsedResponse = json_decode($response->getBody(), true);
    expect($parsedResponse['status'])->toBe(201);
    expect($parsedResponse['data']['user'])->toBe($user['id']);
    expect($parsedResponse['data']['coment_text'])->toBe('This is a comment');
    expect($parsedResponse['data']['likes'])->toBe(10);
});

test('updates an existing comment', function () {
    $user = $this->createUser($this->pdo, 1);
    $comment = $this->createComment($this->pdo, $user['id'], 1);

    $controller = new CommentController();
    $updateCommentRequest = new UpdateCommentRequest();
    $updateCommentRequest->setData([
        'comment' => 'This is an updated comment',
    ]);
    $action = new StoreCommentAction();
    $response = $controller->update($updateCommentRequest, $comment['id'], $action);

    $parsedResponse = json_decode($response->getBody(), true);
    expect($parsedResponse['status'])->toBe(200);
    expect($parsedResponse['data']['coment_text'])->toBe('This is an updated comment');
});

it('returns a 404 response when user is not found', function () {
    $controller = new CommentController();
    $response = $controller->show(1);
    $parsedResponse = json_decode($response->getBody(), true);
    expect($parsedResponse['status'])->toBe(404);
    expect($parsedResponse['data']['message'])->toBe('Resource not found');
});

it('returns a paginated list of comments', function () {
    $user = $this->createUser($this->pdo, 1);
    $this->createComment($this->pdo, $user['id'], 100);

    $controller = new CommentController();
    $response = $controller->index($this->request, $user['id']);
    $parsedResponse = json_decode($response->getBody(), true);

    expect($parsedResponse['status'])->toBe(200);
    expect(count($parsedResponse['data']['items']))->toBe(10);
    expect($parsedResponse['data']['meta']['current_page'])->toBe(1);
    expect($parsedResponse['data']['meta']['per_page'])->toBe(10);
    expect($parsedResponse['data']['meta']['total'])->toBe(100);
});

it('shows a comment', function () {
    $user = $this->createUser($this->pdo, 1);
    $comment = $this->createComment($this->pdo, $user['id'], 1);

    $controller = new CommentController();
    $response = $controller->show($comment['id']);
    $parsedResponse = json_decode($response->getBody(), true);
    expect($parsedResponse['status'])->toBe(200);
    expect($parsedResponse['data']['coment_text'])->toBe($comment['coment_text']);
    expect($parsedResponse['data']['likes'])->toBe($comment['likes']);
});

it('deletes a comment', function () {
    $user = $this->createUser($this->pdo, 1);
    $comment = $this->createComment($this->pdo, $user['id'], 1);

    $controller = new CommentController();
    $response = $controller->destroy($comment['id']);
    $parsedResponse = json_decode($response->getBody(), true);
    expect($parsedResponse['status'])->toBe(204);
});
