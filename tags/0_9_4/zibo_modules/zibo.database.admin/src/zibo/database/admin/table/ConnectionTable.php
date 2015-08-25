<?php

namespace zibo\database\admin\table;

use zibo\database\admin\table\decorator\ConnectionDecorator;
use zibo\database\admin\table\decorator\ExportActionDecorator;
use zibo\database\admin\table\decorator\OptionDecorator;
use zibo\database\admin\table\decorator\StatusDecorator;

use zibo\library\html\table\decorator\ZebraDecorator;
use zibo\library\html\table\ExtendedTable;

/**
 * Table for database connections
 */
class ConnectionTable extends ExtendedTable {

    /**
     * Name of the table
     * @var string
     */
    const NAME = 'tableConnections';

    /**
     * Constructs a new connection table
     * @param array $connections Array with Connection instances
     * @param string $formAction URL where the form of the table will point to
     * @param string $connectionAction URL where the name of a connection will point to
     * @param string $exportAction URL to the export action
     * @param string $defaultConnection Name of the default connection
     * @return null
     */
	public function __construct(array $connections, $formAction, $connectionAction = null, $exportAction = null, $defaultConnection = null) {
		parent::__construct($connections, $formAction, self::NAME);

		$this->addDecorator(new OptionDecorator());
		$this->addDecorator(new ZebraDecorator(new ConnectionDecorator($connectionAction, $defaultConnection)));
		$this->addDecorator(new StatusDecorator());
		$this->addDecorator(new ExportActionDecorator($exportAction));
	}

}