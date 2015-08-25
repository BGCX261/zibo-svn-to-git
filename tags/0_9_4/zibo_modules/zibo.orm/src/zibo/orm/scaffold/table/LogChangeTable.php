<?php

namespace zibo\orm\scaffold\table;

use zibo\library\i18n\I18n;

use zibo\library\orm\ModelManager;
use zibo\library\html\table\decorator\StaticDecorator;
use zibo\library\html\table\decorator\ZebraDecorator;
use zibo\library\html\table\SimpleTable;

use zibo\orm\scaffold\table\decorator\LogValueDecorator;

/**
 * Table for the changes of a logged action
 */
class LogChangeTable extends SimpleTable {

    /**
     * Translation key for the name of the field
     * @var string
     */
    const TRANSLATION_FIELD = 'orm.label.field';

    /**
     * Translation key for the old value of the field
     * @var string
     */
    const TRANSLATION_VALUE_OLD = 'orm.label.value.old';

    /**
     * Translation key for the new value of the field
     * @var string
     */
    const TRANSLATION_VALUE_NEW = 'orm.label.value.new';

    /**
     * Constructs a new log change table
     * @param array $changes
     */
    public function __construct(array $changes) {
        parent::__construct($changes);

        $translator = I18n::getInstance()->getTranslator();
        $this->addDecorator(new ZebraDecorator(new LogValueDecorator('fieldName')), new StaticDecorator($translator->translate('orm.label.field')));
        $this->addDecorator(new LogValueDecorator('oldValue'), new StaticDecorator($translator->translate('orm.label.value.old')));
        $this->addDecorator(new LogValueDecorator('newValue'), new StaticDecorator($translator->translate('orm.label.value.new')));
    }

}