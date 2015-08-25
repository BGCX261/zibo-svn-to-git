<?php

namespace zibo\library\exchange\type;

/**
 * The Entry element represents a single e-mail address for a contact.
 */
class EmailAddressDictionaryEntry extends Entry {

    /**
     * Key for email address 1
     * @var string
     */
    const KEY_EMAIL_ADDRESS_1 = 'EmailAddress1';

    /**
     * Key for email address 2
     * @var string
     */
    const KEY_EMAIL_ADDRESS_2 = 'EmailAddress2';

    /**
     * Key for email address 3
     * @var string
     */
    const KEY_EMAIL_ADDRESS_3 = 'EmailAddress3';

    /**
     * Sets the key of this entry
     * @param string $key
     * @return null
     * @throws InvalidArgumentException when the provided key is not a valid email address key
     */
    public function setKey($key) {
        $keys = array(
            self::KEY_EMAIL_ADDRESS_1,
            self::KEY_EMAIL_ADDRESS_2,
            self::KEY_EMAIL_ADDRESS_3,
        );

        if (!in_array($key, $keys)) {
            throw new InvalidArgumentException('Provided key is invalid, try one of the constants');
        }

        $this->Key = $key;
    }

}