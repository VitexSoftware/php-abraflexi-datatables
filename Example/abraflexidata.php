<?php

require_once '../vendor/autoload.php';


\Ease\Shared::init(['ABRAFLEXI_URL', 'ABRAFLEXI_LOGIN', 'ABRAFLEXI_PASSWORD', 'ABRAFLEXI_COMPANY'], '../.env');

header('Content-Type: application/json');

$class = \Ease\WebPage::getRequestValue('class');


/**
 * @var \AbraFlexi\RO Data Source
 */
$engine = new $class;

unset($_REQUEST['class']);
unset($_REQUEST['_']);

$dataRaw = $engine->getColumnsFromAbraFlexi('*',$_REQUEST);

echo json_encode(['data' => $dataRaw, 'recordsTotal' => count($dataRaw)]);
