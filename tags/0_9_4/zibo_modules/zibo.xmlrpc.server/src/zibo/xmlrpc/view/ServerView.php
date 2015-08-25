<?php

namespace zibo\xmlrpc\view;

use zibo\admin\view\BaseView;

class ServerView extends BaseView {

    public function __construct($url, array $methods) {
    	parent::__construct('xmlrpc/server');

    	$this->set('serverUrl', $url);
    	$this->set('serverMethods', $methods);

    	$this->addStyle('web/styles/xmlrpc/server.css');
    }

}
