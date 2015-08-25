<?php

namespace zibo\admin\view\system;

use zibo\admin\view\BaseView;

/**
 * PhpInfo admin view
 */
class PhpInfoView extends BaseView {

    /**
     * Path to the template of this view
     * @var string
     */
    const TEMPLATE = 'admin/system/phpinfo';

    /**
     * Path to the CSS of this view
     * @var string
     */
    const STYLE_PHP_INFO = 'web/styles/admin/phpinfo.css';

    /**
     * Constructs a new PhpInfo view
     * @return null
     */
    public function __construct() {
        parent::__construct(self::TEMPLATE);

        $this->addStyle(self::STYLE_PHP_INFO);
    }

    /**
     * Renders this view
     * @param boolean $return flag to return or output the rendered view
     * @return null|string
     */
    public function render($return = true) {
        $phpinfo = $this->getPhpInfoHtml();

        $this->set('phpinfo', $phpinfo);

        return parent::render($return);
    }

    /**
     * Gets the HTML of the phpinfo function
     * @return string
     */
    private function getPhpInfoHtml() {
        ob_start();
        phpinfo();
        $phpInfo = ob_get_clean();

        $phpInfo = preg_replace('/<style type="text\\/css">([\w|\W]*)<\\/style>/', '', $phpInfo);
        $phpInfo = preg_replace('/(<a href="(.*)"><img border="0" src="(.*)" alt="(.*) (L|l)ogo" \\/><\\/a>)/', '<a href="$2">$4</a>', $phpInfo);

        return $phpInfo;
    }

}