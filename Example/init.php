<?php

/**
 * FlexiBee datatables
 *
 * @author     Vítězslav Dvořák <info@vitexsofware.cz>
 * @copyright  (G) 2018-2020 Vitex Software
 */

require_once '../vendor/autoload.php';
$shared = \Ease\Shared::instanced();
$shared->loadConfig('./client.json', true);
//$localer = new \Ease\Locale('cs_CZ', '../i18n', 'flexibee-datatables');
$oPage = new \Ease\TWB4\WebPage();
