<?php

namespace App\Services;

use Exception;

class SecurityService
{
    public function decryptPassword($encryptedPassword)
    {
        try {
            $privateKey = env('RSA_PRIVATE_KEY');
            $privateKeyResource = openssl_pkey_get_private($privateKey);

            if (!$privateKeyResource) {
                throw new Exception('Invalid private key.');
            }

            $decryptedPassword = '';
            openssl_private_decrypt(base64_decode($encryptedPassword), $decryptedPassword, $privateKeyResource);

            return $decryptedPassword;
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode() ?: 500);
        }
    }
}
