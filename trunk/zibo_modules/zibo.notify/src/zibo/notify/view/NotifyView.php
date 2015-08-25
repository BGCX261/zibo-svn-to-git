<?php

namespace zibo\notify\view;

use zibo\core\View;
use zibo\core\Zibo;

use zibo\library\i18n\I18n;
use zibo\library\smarty\view\SmartyView;

/**
 * View for the notify container
 */
class NotifyView extends SmartyView {

    /**
     * Configuration key to enable the shutdown message
     * @var string
     */
    const CONFIG_SHUTDOWN = 'notify.message.shutdown.enable';

    /**
     * Configuration key to limit the shutdown message to external actions and the logout button only
     * @var string
     */
    const CONFIG_SHUTDOWN_LIMIT = 'notify.message.shutdown.limit';

    /**
     * Default value for the flag of the shutdown message
     * @var boolean
     */
    const DEFAULT_SHUTDOWN = false;

    /**
     * Default value for the flag of the shutdown limit
     * @var boolean
     */
    const DEFAULT_SHUTDOWN_LIMIT = false;

    /**
     * Translation key for the shutdown alert message
     * @var string
     */
    const TRANSLATION_SHUTDOWN = 'notify.label.shutdown';

    /**
     * The views of the notifications
     * @var array
     */
    private $notifications;

    /**
     * Constructs a new notify view
     * @return null
     */
    public function __construct() {
        parent::__construct('notify/notify');

        $this->notifications = array();

        $this->addStyle('web/styles/notify/notify.css');
        $this->addJavascript('web/scripts/notify/notify.js');

        $zibo = Zibo::getInstance();

        if ($zibo->getConfigValue(self::CONFIG_SHUTDOWN, self::DEFAULT_SHUTDOWN)) {
            $limit = $zibo->getConfigValue(self::CONFIG_SHUTDOWN_LIMIT, self::DEFAULT_SHUTDOWN_LIMIT);

            $translator = I18n::getInstance()->getTranslator();
            $message = $translator->translate(self::TRANSLATION_SHUTDOWN);

            $this->addInlineJavascript('ziboNotifyInitialize("' . $message . '", ' . ($limit ? 'true' : 'false') . ');');
        } else {
            $this->addInlineJavascript('ziboNotifyInitialize();');
        }
    }

    /**
     * Adds a notification to the notify container
     * @param zibo\core\View $notificationView View of the notification
     * @return null
     */
    public function addNotification(View $notificationView) {
        $this->notifications[] = $notificationView;
    }

    /**
     * Renders the notify container
     * @param boolean $return Flag to see if the output should be printed or returned
     * @return null|string
     */
    public function render($return = true) {
        $renderedNotificationViews = array();
        foreach ($this->notifications as $notificationView) {
            $renderedNotificationViews[] = $this->renderView($notificationView);
        }

        $this->set('notifications', $renderedNotificationViews);

        return parent::render($return);
    }

}