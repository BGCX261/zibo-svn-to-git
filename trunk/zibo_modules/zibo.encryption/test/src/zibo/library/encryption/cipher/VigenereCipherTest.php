<?php

namespace zibo\library\encryption\cipher;

use zibo\test\BaseTestCase;

class VigenereCipherTest extends BaseTestCase {

    private $cipher;

    public function setUp() {
        $this->cipher = new VigenereCipher();
    }

    /**
     * @dataProvider providerCipher
     */
    public function testEncrypt($plainText, $cipherText, $key) {
        $result = $this->cipher->encrypt($plainText, $key);

        $this->assertEquals($cipherText, $result);
    }

    /**
     * @dataProvider providerCipher
     */
    public function testDecrypt($plainText, $cipherText, $key) {
        $result = $this->cipher->decrypt($cipherText, $key);

        $this->assertEquals($plainText, $result);
    }

    public function providerCipher() {
        return array(
            array('test string', '5C xbJxKSrE', 'MySecretKey'),
            array('test string', 'j9in0mjl9h7', '15'),
        );
    }

}