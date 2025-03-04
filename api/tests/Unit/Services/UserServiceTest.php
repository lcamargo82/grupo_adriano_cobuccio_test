<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\UserService;
use App\Repositories\UserRepository;
use App\Repositories\AccountRepository;
use App\Services\SecurityService;
use Mockery;
use Exception;
use Illuminate\Support\Facades\Hash;

class UserServiceTest extends TestCase
{
    protected $userService;
    protected $userRepository;
    protected $accountRepository;
    protected $securityService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = Mockery::mock(UserRepository::class);
        $this->accountRepository = Mockery::mock(AccountRepository::class);
        $this->securityService = Mockery::mock(SecurityService::class);

        $this->userService = new UserService(
            $this->userRepository,
            $this->accountRepository,
            $this->securityService
        );
    }

    public function test_get_user_profile_success()
    {
        $userId = 1;
        $userMock = (object) ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com'];

        $this->userRepository->shouldReceive('findById')
            ->with($userId, $userId)
            ->andReturn($userMock);

        $response = $this->userService->getUserProfile($userId);
        $this->assertEquals($userMock, $response);
    }

    public function test_get_user_profile_not_found()
    {
        $userId = 1;

        $this->userRepository->shouldReceive('findById')
            ->with($userId, $userId)
            ->andReturn(null);

        $response = $this->userService->getUserProfile($userId);
        $this->assertEquals(500, $response->getStatusCode());
    }

    public function test_update_user_success()
    {
        $userId = 1;
        $data = ['name' => 'New Name'];
        $userMock = (object) ['id' => 1, 'name' => 'John Doe'];

        $this->userRepository->shouldReceive('findById')
            ->with($userId, $userId)
            ->andReturn($userMock);

        $this->userRepository->shouldReceive('update')
            ->with($userId, $data, $userId)
            ->andReturn(true);

        $response = $this->userService->update($userId, $data);
        $this->assertTrue($response);
    }

    public function test_update_user_not_found()
    {
        $userId = 1;
        $data = ['name' => 'New Name'];

        $this->userRepository->shouldReceive('findById')
            ->with($userId, $userId)
            ->andReturn(null);

        $response = $this->userService->update($userId, $data);
        $this->assertEquals(500, $response->getStatusCode());
    }

    public function test_update_user_invalid_password_format()
    {
        $userId = 1;
        $data = ['password' => '123456'];

        $this->userRepository->shouldReceive('findById')
            ->with($userId, $userId)
            ->andReturn((object) ['id' => $userId]);

        $response = $this->userService->update($userId, $data);
        $this->assertEquals(500, $response->getStatusCode());
    }

    public function test_update_user_failed_password_decryption()
    {
        $userId = 1;
        $data = ['password' => str_repeat('a', 100)];

        $this->userRepository->shouldReceive('findById')
            ->with($userId, $userId)
            ->andReturn((object) ['id' => $userId]);

        $this->securityService->shouldReceive('decryptPassword')
            ->with($data['password'])
            ->andReturn(null);

        $response = $this->userService->update($userId, $data);
        $this->assertEquals(500, $response->getStatusCode());
    }

    public function test_delete_user_success()
    {
        $userId = 1;
        $accountMock = (object) ['balance' => 0];

        $this->accountRepository->shouldReceive('findByUserId')
            ->with($userId, $userId)
            ->andReturn($accountMock);

        $this->userRepository->shouldReceive('delete')
            ->with($userId, $userId)
            ->andReturn(true);

        $response = $this->userService->delete($userId);
        $this->assertTrue($response);
    }

    public function test_delete_user_with_balance()
    {
        $userId = 1;
        $accountMock = (object) ['balance' => 100];

        $this->accountRepository->shouldReceive('findByUserId')
            ->with($userId, $userId)
            ->andReturn($accountMock);

        $response = $this->userService->delete($userId);
        $this->assertEquals(500, $response->getStatusCode());
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
