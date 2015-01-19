<?php

require_once 'config/config.inc.php';
require_once 'data_adapter.php';

$pdo = new LocalPDO();

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$eda = new Externals\DB_Email_Templates\DataAdapter($pdo, 'email_templates');

$eda->assertDBIsOK();

$obj = $eda->getRandomByCategory(1);

($obj) && $eda->touch($obj->getId());


var_dump($obj);


