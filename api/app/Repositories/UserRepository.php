<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    /**
     * @param $id
     * @param $authUserId
     * @return mixed
     */
    public function findById($id, $authUserId)
    {
        return User::where('id', $id)->where('id', $authUserId)->first();
    }

    /**
     * @param $email
     * @return mixed
     */
    public function findByEmail($email)
    {
        return User::where('email', $email)->first();
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        return User::create($data);
    }

    /**
     * @param $id
     * @param array $data
     * @param $authUserId
     * @return mixed
     */
    public function update($id, array $data, $authUserId)
    {
        $user = $this->findById($id, $authUserId);
        if ($user) {
            $user->update($data);
        }
        return $user;
    }

    /**
     * @param $id
     * @param $authUserId
     * @return mixed
     */
    public function delete($id, $authUserId)
    {
        $user = $this->findById($id, $authUserId);
        if ($user) {
            $user->delete();
        }
        return $user;
    }
}
