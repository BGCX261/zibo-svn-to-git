<?php

namespace joppa\controller\widget;

use joppa\view\widget\MessagesWidgetView;

use zibo\library\widget\controller\AbstractWidget;

/**
 * Widget to display the information, warning and error messages
 */
class MessagesWidget extends AbstractWidget {

    /**
     * Relative path to the icon of this widget
     * @var string
     */
	const ICON = 'web/images/joppa/widget/messages.png';

	/**
	 * Translation key for the name of this widget
	 * @var string
	 */
    const TRANSLATION_NAME = 'joppa.widget.messages.name';

    /**
     * Construct this widget
     * @return null
     */
    public function __construct() {
        parent::__construct(self::TRANSLATION_NAME);
    }

    /**
     * Sets a messages view to the response
     * @return null
     */
    public function indexAction() {
    	$view = new MessagesWidgetView();
    	$this->response->setView($view);
    }

}