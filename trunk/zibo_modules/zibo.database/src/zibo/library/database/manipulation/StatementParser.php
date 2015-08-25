<?php

namespace zibo\library\database\manipulation;

use zibo\library\database\manipulation\statement\Statement;

/**
 * Parser to translate Statement objects into sql
 */
interface StatementParser {

    /**
     * Parse a Statement object to sql
     * @param Statement $statement to parse
     * @return string sql of the given Statement object
     */
    public function parseStatement(Statement $statement);

}