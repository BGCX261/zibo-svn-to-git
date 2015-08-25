<?php

namespace joppa\view\widget;

use zibo\core\Zibo;

use zibo\library\smarty\view\SmartyView;

/**
 * View of the messages widget
 */
class MessagesWidgetView extends SmartyView {

	/**
	 * Path to the template of this view
	 * @var string
	 */
	const TEMPLATE = 'joppa/widget/messages/messages';

    /**
     * Construct this view
     * @return null
     */
	public function __construct() {
		parent::__construct(self::TEMPLATE);

		Zibo::getInstance()->registerEventListener(Zibo::EVENT_PRE_RESPONSE, array($this, "preResponse"), 77);
	}

	/**
	 * Hook to grab all the messages from the view which is going to be displayed
	 * @return null
	 */
	public function preResponse() {
		$response = Zibo::getInstance()->getResponse();
        $view = $response->getView();

        if (!($view instanceof SmartyView)) {
            return;
        }

        $messages = $view->get('_messages');
        $this->set('_messages', $messages);
	}

}