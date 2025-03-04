<?php

namespace Tests\Unit\Services;

use App\Services\AuthService;
use App\Services\SecurityService;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
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

    public function test_register_success()
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
            ->with(Mockery::on(function ($arg) use ($data) {
                return $arg['name'] === $data['name'] &&
                    $arg['email'] === $data['email'] &&
                    isset($arg['password']); // Aceita qualquer hash
            }))
            ->andReturn((object)['id' => 1, 'name' => 'John Doe', 'email' => 'johndoe@example.com']);

        $result = $this->authService->register($data);

        $this->assertNotNull($result);
        $this->assertEquals(1, $result->id);
        $this->assertEquals('John Doe', $result->name);
        $this->assertEquals('johndoe@example.com', $result->email);
    }


    public function test_register_fails_on_decryption_error()
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

    public function test_login_fails_on_invalid_credentials()
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
