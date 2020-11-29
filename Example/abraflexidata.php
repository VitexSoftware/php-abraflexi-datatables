<?php
require_once __DIR__.'/init.php';

header('Content-Type: application/json');
require_once './init.php';



$class = $oPage->getRequestValue('class');


/**
 * @var Engine Data Source
 */
$engine = new $class;

unset($_REQUEST['class']);
unset($_REQUEST['_']);

$dataRaw = $engine->getColumnsFromAbraFlexi('*',$_REQUEST);

echo json_encode(['data' => $dataRaw, 'recordsTotal' => count($dataRaw)]);
