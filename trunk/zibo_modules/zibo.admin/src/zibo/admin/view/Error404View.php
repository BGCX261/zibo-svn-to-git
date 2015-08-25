<?php

namespace zibo\admin\view;

/**
 * View for a HTTP 404 error page
 */
class Error404View extends BaseView {

    /**
     * Path to the template of this view
     * @var string
     */
    const TEMPLATE = 'admin/error/404';

    /**
     * Constructs a new 404 view
     * @return null
     */
    public function __construct() {
        parent::__construct(self::TEMPLATE);
    }

}