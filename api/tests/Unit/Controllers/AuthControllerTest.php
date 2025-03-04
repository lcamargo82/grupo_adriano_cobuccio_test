<?php

namespace Tests\Unit\Controllers;

use App\Http\Controllers\AuthController;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    protected $authServiceMock;
    protected $authController;

    protected function setUp(): void
    {
        parent::setUp();

        $this->authServiceMock = Mockery::mock(AuthService::class);
        $this->authController = new AuthController($this->authServiceMock);
    }

    public function test_register_success()
    {
        $requestData = [
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'password' => bcrypt('password123')
        ];

        $request = Request::create('/api/register', 'POST', $requestData);

        $this->authServiceMock
            ->shouldReceive('register')
            ->once()
            ->with($requestData)
            ->andReturn(true);

        $response = $this->authController->register($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals(['message' => 'User registered successfully'], $response->getData(true));
    }

    public function test_register_invalid_password()
    {
        $requestData = [
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'password' => '123456' // Não criptografado
        ];

        $request = Request::create('/api/register', 'POST', $requestData);

        $response = $this->authController->register($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertArrayHasKey('error', $response->getData(true));
        $this->assertEquals('Invalid password format. Password must be encrypted.', $response->getData(true)['error']);
    }

    public function test_login_success()
    {
        $credentials = ['email' => 'johndoe@example.com', 'password' => 'password123'];
        $request = Request::create('/api/login', 'POST', $credentials);

        $this->authServiceMock
            ->shouldReceive('login')
            ->once()
            ->with($credentials)
            ->andReturn('jwt_token_123');

        $response = $this->authController->login($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['token' => 'jwt_token_123'], $response->getData(true));
    }

    public function test_login_unauthorized()
    {
        $credentials = ['email' => 'johndoe@example.com', 'password' => 'wrongpassword'];
        $request = Request::create('/api/login', 'POST', $credentials);

        $this->authServiceMock
            ->shouldReceive('login')
            ->once()
            ->with($credentials)
            ->andReturn(null);

        $response = $this->authController->login($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals(['error' => 'Unauthorized'], $response->getData(true));
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
