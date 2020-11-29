<?php

use AbraFlexi\ui\DataTables\DataTable;

require_once __DIR__ . '/init.php';

$engine = new \AbraFlexi\Adresar();
$tabler = new DataTable($engine);

$oPage->addItem($tabler);

$oPage->draw();

