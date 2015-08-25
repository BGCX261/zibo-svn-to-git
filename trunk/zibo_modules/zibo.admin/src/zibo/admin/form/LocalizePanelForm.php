<?php

namespace zibo\admin\form;

use zibo\admin\controller\LocalizeController;
use zibo\admin\Module;

use zibo\core\Request;
use zibo\core\Zibo;

use zibo\library\html\form\field\FieldFactory;
use zibo\library\html\form\Form;

/**
 * Form to select the locale of the content which is edited
 */
class LocalizePanelForm extends Form {

    /**
     * Name of the form
     * @var string
     */
    const NAME = 'formLocalize';

    /**
     * Name of the locale field
     * @var string
     */
    const FIELD_LOCALE = 'locale';

    /**
     * Constructs a new localize panel form
     * @return null
     */
    public function __construct() {
        $request = Zibo::getInstance()->getRequest();
        $factory = FieldFactory::getInstance();

        parent::__construct($request->getBaseUrl() . Request::QUERY_SEPARATOR . Module::ROUTE_LOCALIZE, self::NAME);

        $this->removeFromClass(Form::STYLE_FORM);

        $localeCode = LocalizeController::getLocale();
        $localeField = $factory->createField(FieldFactory::TYPE_LOCALE, self::FIELD_LOCALE, $localeCode);

        $this->addField($localeField);
    }

    /**
     * Gets the submitted locale
     * @return string Code of the locale
     */
    public function getLocaleCode() {
        return $this->getValue(self::FIELD_LOCALE);
    }

}