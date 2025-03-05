import * as forge from 'node-forge';

const publicKeyPem = `-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAyf4Oj13Dd22O3zLyr2fj
sr5Ajd8Eo0Uj+RviwT12SZmR7P8Bcw6jCXDa4QUyCUW6fdHgmjMO9223IzlMBFQm
hyyKFv9mbhf4+PcXoKYoHMMJjhH9+VIZnyYfdBLefUACBmBAwulMy1a6ZUjGD1ZL
pwkavYHfVEUbw1J0QRFGMjKIlXyzNpG5lvP/2t7p3eKmMCsku2mHxOIlkz0usxks
1cXac3AnTbh4xozDKPgUl3ADLQhPR2PjTr22SVZ7t4bLHzknUHXs7kiDclK/wQUm
Z/WB2uNGg/Am6h59AAjbF6cS0BogDwTN9pJb1EuqpW5w3U656zvjWCItYoBrHT8n
iwIDAQAB
-----END PUBLIC KEY-----`;

export const encryptPassword = (password: string): string => {
  const publicKey = forge.pki.publicKeyFromPem(publicKeyPem);

  const encrypted = publicKey.encrypt(password, 'RSAES-PKCS1-V1_5');

  return forge.util.encode64(encrypted);
};
