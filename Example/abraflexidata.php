<?php

declare(strict_types=1);

/**
 * This file is part of the AbraFlexiDataTables package
 *
 * https://github.com/VitexSoftware/php-abraflexi-datatables
 *
 * (c) Vítězslav Dvořák <http://vitexsoftware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once '../vendor/autoload.php';

\Ease\Shared::init(['ABRAFLEXI_URL', 'ABRAFLEXI_LOGIN', 'ABRAFLEXI_PASSWORD', 'ABRAFLEXI_COMPANY'], '../.env');

header('Content-Type: application/json');

$class = \Ease\WebPage::getRequestValue('class');

/**
 * @var \AbraFlexi\RO Data Source
 */
$engine = new $class();

unset($_REQUEST['class'], $_REQUEST['_']);

$dataRaw = $engine->getColumnsFromAbraFlexi('*', $_REQUEST);

echo json_encode(['data' => $dataRaw, 'recordsTotal' => \count($dataRaw)]);
