<?php

namespace zibo\library\orm\model\data;

use zibo\library\Data as CoreData;

/**
 * Generic data container
 */
class Data extends CoreData {

    /**
     * Id of the log data
     * @var integer
     */
    public $id;

    /**
     * Code of the locale of the data
     * @var string
     */
    public $dataLocale;

    /**
     * Code of the locale of the data
     * @var array
     */
    public $dataLocales;

    /**
     * Version of the data
     * @var integer
     */
    public $version;

    /**
     * Timestamp this data was added to the model
     * @var integer
     */
    public $dateAdded;

    /**
     * Timestamp this data was last modified in the model
     * @var integer
     */
    public $dateModified;

}