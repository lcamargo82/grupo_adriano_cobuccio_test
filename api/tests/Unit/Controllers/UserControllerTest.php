<?php

namespace Tests\Unit\Controllers;

use App\Http\Controllers\UserController;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    protected $userServiceMock;
    protected $userController;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userServiceMock = Mockery::mock(UserService::class);
        $this->userController = new UserController($this->userServiceMock);
    }

    public function test_profile_success()
    {
        $user = ['id' => 1, 'name' => 'John Doe', 'email' => 'johndoe@example.com'];

        Auth::shouldReceive('user')->once()->andReturn($user);

        $response = $this->userController->profile();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($user, $response->getData(true));
    }

    public function test_update_profile_success()
    {
        $requestData = ['name' => 'John Doe Updated', 'email' => 'johnupdated@example.com'];
        $updatedUser = ['id' => 1, 'name' => 'John Doe Updated', 'email' => 'johnupdated@example.com'];

        Auth::shouldReceive('id')->once()->andReturn(1);
        $this->userServiceMock->shouldReceive('update')->once()->with(1, $requestData)->andReturn($updatedUser);

        $request = Request::create('/api/profile', 'PUT', $requestData);

        $response = $this->userController->updateProfile($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['message' => 'Perfil atualizado com sucesso', 'user' => $updatedUser], $response->getData(true));
    }

    public function test_delete_profile_success()
    {
        Auth::shouldReceive('id')->once()->andReturn(1);
        $this->userServiceMock->shouldReceive('delete')->once()->with(1)->andReturn(true);

        $response = $this->userController->deleteProfile();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['message' => 'Perfil excluído com sucesso'], $response->getData(true));
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
