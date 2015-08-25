<?php

namespace zibo\library\encryption\cipher;

/**
 * Interface to implement a cipher to encrypt and decrypt data using an encryption key
 */
interface Cipher {

    /**
     * Encrypts the plain text with the provided encryption key.
     * @param string $plainText The plain text
     * @param string $key The encryption key
     * @return string Cipher text
     */
    public function encrypt($clearText, $key = null);

    /**
     * Decrypts the cipher text with the provided encryption key.
     * @param string $cipherText The chipher text
     * @param string $key The encryption key
     * @return string Plain text
     */
    public function decrypt($encryptedText, $key = null);

}