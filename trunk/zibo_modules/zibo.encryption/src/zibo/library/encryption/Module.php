<?php

namespace zibo\library\encryption;

/**
 * Class with the encryption module constants
 */
class Module {

    /**
     * Configuration key for the ciphers
     * @var string
     */
    const CONFIG_CIPHER = 'encryption.cipher';

    /**
     * Configuration key for the hash algorithms
     * @var string
     */
    const CONFIG_HASH_ALGORITHM = 'encryption.hash';

    /**
     * Class name of the interface of the cipher
     * @var string
     */
    const INTERFACE_CIPHER = 'zibo\\library\\encryption\\cipher\\Cipher';

    /**
     * Class name of the interface of the hash algorithm
     * @var string
     */
    const INTERFACE_HASH = 'zibo\\library\\encryption\\hash\\Hash';

}