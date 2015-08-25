<?php

namespace zibo\orm\scaffold\view;

use zibo\core\Zibo;

use zibo\library\i18n\I18n;
use zibo\library\smarty\view\SmartyView;

use zibo\orm\Module;

/**
 * View for the history of a orm data object
 */
class LogView extends SmartyView {

    /**
     * Path to the template of this view
     * @var string
     */
    const TEMPLATE = 'orm/scaffold/log';

    /**
     * Path to the javascript of this view
     * @var string
     */
    const SCRIPT = 'web/scripts/orm/log.js';

    /**
     * Path to the CSS style of this scripts
     * @var string
     */
    const STYLE = 'web/styles/orm/log.css';

    /**
     * Translation key for the hide button
     * @var string
     */
    const TRANSLATION_HIDE = 'orm.button.history.hide';

    /**
     * Translation key for the show button
     * @var string
     */
    const TRANSLATION_SHOW = 'orm.button.history.show';

    /**
     * Constructs a new log view
     * @param string $modelName Name of the model
     * @param int $id Primary key of the data
     * @return null
     */
    public function __construct($modelName, $id) {
        parent::__construct(self::TEMPLATE);

        $request = Zibo::getInstance()->getRequest();
        $dataUrl = $request->getBaseUrl() . '/' . Module::ROUTE_LOG_AJAX . '/' . $modelName . '/' . $id;

        $translator = I18n::getInstance()->getTranslator();
        $labelShow = $translator->translate(self::TRANSLATION_SHOW);
        $labelHide = $translator->translate(self::TRANSLATION_HIDE);

        $this->addStyle(self::STYLE);
        $this->addJavascript(self::SCRIPT);

        $this->addInlineJavascript('ziboOrmInitializeLog("' . $labelShow . '", "' . $labelHide . '", "' . $dataUrl . '");');
    }

}