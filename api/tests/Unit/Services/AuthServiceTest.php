<?php

namespace Tests\Unit\Services;

use App\Services\AuthService;
use App\Services\SecurityService;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tests\TestCase;
use Mockery;
use Exception;

class AuthServiceTest extends TestCase
{
    protected $authService;
    protected $userRepositoryMock;
    protected $securityServiceMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepositoryMock = Mockery::mock(UserRepository::class);
        $this->securityServiceMock = Mockery::mock(SecurityService::class);

        $this->authService = new AuthService(
            $this->userRepositoryMock,
            $this->securityServiceMock
        );
    }

    public function testRegisterSuccess()
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'password' => 'encrypted_password'
        ];

        $this->securityServiceMock
            ->shouldReceive('decryptPassword')
            ->with($data['password'])
            ->andReturn('decrypted_password');

        $this->userRepositoryMock
            ->shouldReceive('create')
            ->with([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make('decrypted_password'),
            ])
            ->andReturn(true);

        $result = $this->authService->register($data);

        $this->assertTrue($result);
    }

    public function testRegisterFailsOnDecryptionError()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Erro ao registrar usuário.');

        $data = [
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'password' => 'invalid_encrypted_password'
        ];

        $this->securityServiceMock
            ->shouldReceive('decryptPassword')
            ->with($data['password'])
            ->andThrow(new Exception('Decryption failed'));

        $this->authService->register($data);
    }

    public function testLoginSuccess()
    {
        $credentials = ['email' => 'johndoe@example.com', 'password' => 'encrypted_password'];

        $this->securityServiceMock
            ->shouldReceive('decryptPassword')
            ->with($credentials['password'])
            ->andReturn('decrypted_password');

        Auth::shouldReceive('attempt')
            ->with(['email' => $credentials['email'], 'password' => 'decrypted_password'])
            ->andReturn(true);

        JWTAuth::shouldReceive('attempt')
            ->andReturn('jwt_token');

        $result = $this->authService->login($credentials);

        $this->assertArrayHasKey('token', $result);
        $this->assertEquals('jwt_token', $result['token']);
    }

    public function testLoginFailsOnInvalidCredentials()
    {
        $credentials = ['email' => 'johndoe@example.com', 'password' => 'encrypted_password'];

        $this->securityServiceMock
            ->shouldReceive('decryptPassword')
            ->with($credentials['password'])
            ->andReturn('decrypted_password');

        Auth::shouldReceive('attempt')
            ->with(['email' => $credentials['email'], 'password' => 'decrypted_password'])
            ->andReturn(false);

        $result = $this->authService->login($credentials);

        $this->assertNull($result);
    }
}
