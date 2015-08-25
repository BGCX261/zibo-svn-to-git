<?php

namespace zibo\library\encryption\cipher;

/**
 * Cipher implementation of the Vigenère method
 */
class VigenereCipher implements Cipher {

    /**
     * Default alfabet
     * @var string
     */
    const ALFABET = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789 ';

    /**
     * The alfabet used for encryption and decryption
     * @var string
     */
    private $alfabet;

    /**
     * Constructs a new Vigenère cipher
     * @param string $alfabet String with all the characters used for encryption and decription. Default is a string with the following characters: a-z, A-Z, 0-9 and a space
     * @return null
     */
    public function __construct($alfabet = null) {
        if ($alfabet === null) {
            $alfabet = self::ALFABET;
        }

        $this->alfabet = $alfabet;
    }

    /**
     * Encrypts the plain text with the provided encryption key.
     * @param string $plainText The plain text
     * @param string $key The encryption key
     * @return string Cipher text
     */
    public function encrypt($plainText, $key = null) {
        $alfabetLength = strlen($this->alfabet);
        $plainTextLength = strlen($plainText);
        $keyLength = strlen($key);

        $cipherText = $plainText;

        $counterKey = 0;
        for ($i = 0; $i < $plainTextLength; $i++) {
            $currentLetter = substr($plainText, $i, 1);
            $alfabetPosition = strpos($this->alfabet, $currentLetter);
            if ($alfabetPosition === false) {
                continue;
            }

            $movePosition = strpos($this->alfabet, $key[$counterKey % $keyLength]);
            if ($movePosition === false) {
                continue;
            }

            $newIndex = ($alfabetPosition + $movePosition) % $alfabetLength;
            $cipherText[$i] = substr($this->alfabet, $newIndex, 1);
            $counterKey++;
        }

        return $cipherText;
    }

    /**
     * Decrypts the cipher text with the provided encryption key.
     * @param string $cipherText The chipher text
     * @param string $key The encryption key
     * @return string Plain text
     */
    public function decrypt($cipherText, $key = null) {
        // create an inverse key
        $inverseKey = $key;
        $inverseKeyLength = strlen($key);

        $alfabetLength = strlen($this->alfabet);
        for ($i = 0; $i < $inverseKeyLength; $i++) {
            $alfabetPosition = strpos($this->alfabet, $inverseKey[$i]);
            if ($alfabetPosition === false) {
                continue;
            }

            $inverseIndex = ($alfabetLength - $alfabetPosition) % $alfabetLength;
            $inverseKey[$i] = substr($this->alfabet, $inverseIndex, 1);
        }

        // encrypt with the inverse key and return it
        return $this->encrypt($cipherText, $inverseKey);
    }

}