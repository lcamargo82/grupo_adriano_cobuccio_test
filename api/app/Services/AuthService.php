<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    protected $userRepository;

    /**
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param $encryptedPassword
     * @return string
     * @throws Exception
     */
    public function decryptPassword($encryptedPassword)
    {
        $privateKey = env('RSA_PRIVATE_KEY');
        $privateKeyResource = openssl_pkey_get_private($privateKey);

        if (!$privateKeyResource) {
            throw new Exception('Invalid private key.');
        }

        $decryptedPassword = '';
        openssl_private_decrypt(base64_decode($encryptedPassword), $decryptedPassword, $privateKeyResource);

        return $decryptedPassword;
    }

    /**
     * @param array $data
     * @return mixed
     * @throws Exception
     */
    public function register(array $data)
    {
        try {
            $decryptedPassword = $this->decryptPassword($data['password']);

            return $this->userRepository->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($decryptedPassword),
            ]);
        } catch (Exception $e) {
            throw new Exception('Erro ao registrar usuário.');
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
            $decryptedPassword = $this->decryptPassword($credentials['password']);

            $user = $this->userRepository->findByEmail($credentials['email']);

            if (!$user || !Hash::check($decryptedPassword, $user->password)) {
                return null;
            }

            $token = Auth::login($user);

            if (!$token) {
                throw new Exception('Fail to authenticate.');
            }

            return ['token' => $token];
        } catch (Exception $e) {
            throw new Exception('Error in authentication.');
        }
    }
}
