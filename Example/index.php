<?php
require_once __DIR__ . '/init.php';

$engine = new \FlexiPeeHP\Adresar();
$tabler = new FlexiPeeHP\ui\DataTables\DataTable($engine);

$oPage->addItem( $tabler );

$oPage->draw();

