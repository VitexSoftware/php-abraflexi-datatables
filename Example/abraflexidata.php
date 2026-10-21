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

header('Content-Type: application/json; charset=utf-8');

$class = \Ease\WebPage::getRequestValue('class');

if ($class === null || $class === '' || !class_exists($class)) {
    http_response_code(400);
    echo json_encode(['data' => [], 'error' => 'Missing or unknown class']);

    exit;
}

if (!is_a($class, \AbraFlexi\RO::class, true)) {
    http_response_code(403);
    echo json_encode(['data' => [], 'error' => 'Class must extend AbraFlexi\\RO']);

    exit;
}

/** @var \AbraFlexi\RO $engine */
$engine = new $class();

$filter = \Ease\WebPage::getRequestValue('filter');
if (\is_string($filter) && $filter !== '') {
    $engine->filter = $filter;
}

unset($_REQUEST['class'], $_REQUEST['_'], $_REQUEST['filter']);

$conditions = array_filter(
    $_REQUEST,
    static fn ($v, $k) => $v !== '' && $v !== null && !str_starts_with((string) $k, '_'),
    \ARRAY_FILTER_USE_BOTH,
);

if (method_exists($engine, 'getAllForDataTable')) {
    echo json_encode($engine->getAllForDataTable($conditions), \JSON_UNESCAPED_UNICODE);

    exit;
}

$dataRaw = $engine->getColumnsFromAbraFlexi('*', $conditions) ?: [];

echo json_encode([
    'data' => $dataRaw,
    'recordsTotal' => \count($dataRaw),
    'recordsFiltered' => \count($dataRaw),
], \JSON_UNESCAPED_UNICODE);
