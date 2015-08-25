<?php

use zibo\test\Suite;

include 'bootstrap.php';

date_default_timezone_set('Europe/Brussels');

$test = new Suite();
$test->run();

exit;
