<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService
{
    protected $userRepository;
    protected $securityService;


    /**
     * @param UserRepository $userRepository
     * @param SecurityService $securityService
     */
    public function __construct(UserRepository $userRepository, SecurityService $securityService)
    {
        $this->userRepository = $userRepository;
        $this->securityService = $securityService;
    }

    /**
     * @param array $data
     * @return mixed
     * @throws Exception
     */
    public function register(array $data)
    {
        try {
            $decryptedPassword = $this->securityService->decryptPassword($data['password']);

            return $this->userRepository->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($decryptedPassword),
            ]);
        } catch (Exception $e) {
            throw new Exception('Erro ao registrar usuário.' . $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * @param array $credentials
     * @return array|null
     * @throws Exception
     */
    public function login(array $credentials)
    {
        try {
            if (empty($credentials['password'])) {
                throw new Exception('Password is required.');
            }

            $decryptedPassword = $this->securityService->decryptPassword($credentials['password']);

            if (!$decryptedPassword) {
                throw new Exception('Failed to decrypt password.');
            }

            if (!Auth::attempt(['email' => $credentials['email'], 'password' => $decryptedPassword])) {
                return null;
            }

            $token = JWTAuth::attempt([
                'email' => $credentials['email'],
                'password' => $decryptedPassword
            ]);

            if (!$token) {
                throw new Exception('Unauthorized');
            }

            return ['token' => $token];
        } catch (Exception $e) {
            throw new Exception('Error in authentication: ' . $e->getMessage(), $e->getCode() ?: 500);
        }
    }
}
