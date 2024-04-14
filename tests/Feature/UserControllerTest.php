<?php

use App\Actions\StoreUserAction;
use App\Controllers\UserController;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Garaekz\Http\Request;
use Garaekz\Services\Database;


beforeEach(function () {
    $db = Database::getInstance();
    $db->initializeForTests();
    $this->pdo = $db->getConnection();
    $this->request = new Request();
});

test('store a new user', function () {
    $controller = new UserController();
    $userRequest = new StoreUserRequest();
    $userRequest->setData([
        'fullname' => 'John Doe',
        'email' => 'test@test.io',
        'pass' => 'password',
        'openid' => '123456',
    ]);

    $action = new StoreUserAction();
    $response = $controller->store($userRequest, $action);
    $parsedResponse = json_decode($response->getBody(), true);

    expect($parsedResponse['status'])->toBe(201);
    expect($parsedResponse['data']['fullname'])->toBe('John Doe');
    expect($parsedResponse['data']['email'])->toBe('test@test.io');
});

test('updates an existing user', function () {
    $this->createUser($this->pdo, 1);

    $controller = new UserController();
    $updateUserRequest = new UpdateUserRequest();
    $updateUserRequest->setData([
        'fullname' => 'Eren Yeager',
    ]);
    $action = new StoreUserAction();
    $response = $controller->update($updateUserRequest, 1, $action);

    $parsedResponse = json_decode($response->getBody(), true);
    expect($parsedResponse['status'])->toBe(200);
    expect($parsedResponse['data']['fullname'])->toBe('Eren Yeager');
});

it('returns a 404 response when user is not found', function () {
    $controller = new UserController();
    $response = $controller->show(1);
    $parsedResponse = json_decode($response->getBody(), true);
    expect($parsedResponse['status'])->toBe(404);
    expect($parsedResponse['data']['message'])->toBe('Resource not found');
});

it('returns a paginated list of users', function () {
    $this->createUser($this->pdo, 100);

    $controller = new UserController();
    $response = $controller->index($this->request);
    $parsedResponse = json_decode($response->getBody(), true);

    expect($parsedResponse['status'])->toBe(200);
    expect(count($parsedResponse['data']['items']))->toBe(10);
    expect($parsedResponse['data']['meta']['current_page'])->toBe(1);
    expect($parsedResponse['data']['meta']['per_page'])->toBe(10);
    expect($parsedResponse['data']['meta']['total'])->toBe(100);
});

it('shows a user', function () {
    $user = $this->createUser($this->pdo, 1);
    $controller = new UserController();
    $response = $controller->show(1);
    $parsedResponse = json_decode($response->getBody(), true);
    expect($parsedResponse['status'])->toBe(200);
    expect($parsedResponse['data']['fullname'])->toBe($user['fullname']);
    expect($parsedResponse['data']['email'])->toBe($user['email']);
});

it('deletes a user', function () {
    $this->createUser($this->pdo, 1);
    // Check initial count
    expect($this->getTableCount($this->pdo, 'user'))->toBe(1);

    $controller = new UserController();
    $response = $controller->destroy(1);
    $parsedResponse = json_decode($response->getBody(), true);
    expect($parsedResponse['status'])->toBe(204);

    // Check count after deletion
    expect($this->getTableCount($this->pdo, 'user'))->toBe(0);
});
