<?php

namespace App\Services;

use App\Repositories\AccountRepository;
use App\Repositories\UserRepository;
use Exception;
use Illuminate\Support\Facades\Hash;

class UserService
{
    protected $userRepository;
    protected $accountRepository;
    protected $securityService;


    /**
     * @param UserRepository $userRepository
     * @param AccountRepository $accountRepository
     * @param SecurityService $securityService
     */
    public function __construct(UserRepository $userRepository, AccountRepository $accountRepository, SecurityService $securityService)
    {
        $this->userRepository = $userRepository;
        $this->accountRepository = $accountRepository;
        $this->securityService = $securityService;
    }

    /**
     * @param $userId
     * @return mixed
     */
    public function getUserProfile($userId)
    {
        try {
            return $this->userRepository->findById($userId, $userId);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode() ?: 500);
        }
    }

    /**
     * @param $userId
     * @param array $data
     * @return mixed
     * @throws Exception
     */
    public function update($userId, array $data)
    {
        try {
            $user = $this->userRepository->findById($userId, $userId);

            if (!$user) {
                throw new Exception('Usuário não encontrado.');
            }

            if (isset($data['password'])) {
                if (strlen($data['password']) < 100) {
                    throw new Exception('Invalid password format. Password must be encrypted.');
                }

                $decryptedPassword = $this->securityService->decryptPassword($data['password']);

                if (!$decryptedPassword) {
                    throw new Exception('Failed to decrypt password.');
                }

                $data['password'] = Hash::make($data['password']);
            }

            return $this->userRepository->update($userId, $data, $userId);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode() ?: 500);
        }
    }

    /**
     * @param $userId
     * @return mixed
     * @throws Exception
     */
    public function delete($userId)
    {
        try {
            $account = $this->accountRepository->findByUserId($userId, $userId);
            if ($account && $account->balance != 0) {
                throw new Exception('Não é possível excluir o perfil com saldo na conta.');
            }

            return $this->userRepository->delete($userId, $userId);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode() ?: 500);
        }
    }
}
