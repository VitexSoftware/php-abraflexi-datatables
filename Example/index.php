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

use AbraFlexi\ui\DataTables\DataTable;
use Ease\TWB5\WebPage;

require_once '../vendor/autoload.php';

\Ease\Shared::init(['ABRAFLEXI_URL', 'ABRAFLEXI_LOGIN', 'ABRAFLEXI_PASSWORD', 'ABRAFLEXI_COMPANY'], '../.env');

\Ease\WebPage::singleton(new WebPage(_('AbraFlexi x DataTables')));

\Ease\Part::jQueryze();

\Ease\WebPage::singleton()->addToMain(new DataTable(new \AbraFlexi\Adresar()));

\Ease\WebPage::singleton()->draw();
