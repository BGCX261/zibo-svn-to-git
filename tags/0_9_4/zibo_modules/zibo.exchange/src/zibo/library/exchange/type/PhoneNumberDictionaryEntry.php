<?php

namespace zibo\library\exchange\type;

class PhoneNumberDictionaryEntry extends Entry {

    const KEY_ASSISTANT_PHONE = 'AssistantPhone';

    const KEY_BUSINESS_FAX = 'BusinessFax';

    const KEY_BUSINESS_PHONE = 'BusinessPhone';

    const KEY_BUSINESS_PHONE2 = 'BusinessPhone2';

    const KEY_CALLBACK = 'Callback';

    const KEY_CAR_PHONE = 'CarPhone';

    const KEY_COMPANY_MAIN_PHONE = 'CompanyMainPhone';

    const KEY_HOME_FAX = 'HomeFax';

    const KEY_HOME_PHONE = 'HomePhone';

    const KEY_HOME_PHONE2 = 'HomePhone2';

    const KEY_ISDN = 'Isdn';

    const KEY_MOBILE_PHONE = 'MobilePhone';

    const KEY_OTHER_FAX = 'OtherFax';

    const KEY_OTHER_TELEPHONE = 'OtherTelephone';

    const KEY_PAGER = 'Pager';

    const KEY_PRIMARY_PHONE = 'PrimaryPhone';

    const KEY_RADIO_PHONE = 'RadioPhone';

    const KEY_TELEX = 'Telex';

    const KEY_TTY_TDD_PHONE = 'TtyTddPhone';

    public function setKey($key) {
        $keys = array(
            self::KEY_ASSISTANT_PHONE,
            self::KEY_BUSINESS_FAX,
            self::KEY_BUSINESS_PHONE,
            self::KEY_BUSINESS_PHONE2,
            self::KEY_CALLBACK,
            self::KEY_CAR_PHONE,
            self::KEY_COMPANY_MAIN_PHONE,
            self::KEY_HOME_FAX,
            self::KEY_HOME_PHONE,
            self::KEY_HOME_PHONE2,
            self::KEY_ISDN,
            self::KEY_MOBILE_PHONE,
            self::KEY_OTHER_FAX,
            self::KEY_OTHER_TELEPHONE,
            self::KEY_PAGER,
            self::KEY_PRIMARY_PHONE,
            self::KEY_RADIO_PHONE,
            self::KEY_TELEX,
            self::KEY_TTY_TDD_PHONE,
        );

        if (!in_array($key, $keys)) {
            throw new ZiboException('Provided key is invalid, try one of the constants');
        }

        $this->Key = $key;
    }

}