<?php

namespace zibo\database\admin\view;

use zibo\admin\view\BaseView;

use zibo\database\admin\form\ConnectionDefaultForm;
use zibo\database\admin\table\ConnectionTable;

/**
 * View for the overview of the database connections
 */
class ConnectionsView extends BaseView {

    /**
     * Path to the template of this view
     * @var string
     */
    const TEMPLATE = 'database/admin/connection.list';

    /**
     * Path to the CSS style of this view
     * @var string
     */
    const STYLE = 'web/styles/admin/database.css';

    /**
     * Constructs a new connections view
     * @param zibo\database\admin\table\ConnectionTable $table
     * @param array $protocols
     * @param string $urlAdd
     * @param zibo\database\admin\form\ConnectionDefaultForm $defaultConnectionForm
     * @return null
     */
	public function __construct(ConnectionTable $table, array $protocols, $urlAdd = null, ConnectionDefaultForm $defaultConnectionForm = null) {
		parent::__construct(self::TEMPLATE);

		$this->set('table', $table);
		$this->set('formDefault', $defaultConnectionForm);
		$this->set('protocols', $protocols);
		$this->set('urlAdd', $urlAdd);

		$this->addStyle(self::STYLE);

		$this->addJavascript(self::SCRIPT_TABLE);
	}

}