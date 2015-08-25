<?php

namespace zibo\database\admin\view;

use zibo\admin\view\BaseView;

use zibo\database\admin\form\ConnectionForm;

/**
 * View for the connection form
 */
class ConnectionFormView extends BaseView {

    /**
     * Path to the template of this view
     * @var string
     */
    const TEMPLATE = 'database/admin/connection.form';

    /**
     * Constructs a new connection form view
     * @param zibo\database\admin\form\ConnectionForm $form
     * @param string $title
     * @return null
     */
	public function __construct(ConnectionForm $form, $title) {
		parent::__construct(self::TEMPLATE);

		$this->set('form', $form);
		$this->set('title', $title);

		$this->setPageTitle($title, true);
	}

}