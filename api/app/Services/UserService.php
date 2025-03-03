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

    /**
     * @param UserRepository $userRepository
     * @param AccountRepository $accountRepository
     */
    public function __construct(UserRepository $userRepository, AccountRepository $accountRepository)
    {
        $this->userRepository = $userRepository;
        $this->accountRepository = $accountRepository;
    }

    /**
     * @param $userId
     * @return mixed
     */
    public function getUserProfile($userId)
    {
        return $this->userRepository->findById($userId, $userId);
    }

    /**
     * @param $userId
     * @param array $data
     * @return mixed
     * @throws Exception
     */
    public function update($userId, array $data)
    {
        $user = $this->userRepository->findById($userId, $userId);

        if (!$user) {
            throw new Exception('Usuário não encontrado.');
        }

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        return $this->userRepository->update($userId, $data, $userId);
    }

    /**
     * @param $userId
     * @return mixed
     * @throws Exception
     */
    public function delete($userId)
    {
        $account = $this->accountRepository->findByUserId($userId, $userId);
        if ($account && $account->balance != 0) {
            throw new Exception('Não é possível excluir o perfil com saldo na conta.');
        }

        return $this->userRepository->delete($userId, $userId);
    }
}
