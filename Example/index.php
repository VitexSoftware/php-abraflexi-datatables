<?php

use AbraFlexi\ui\DataTables\DataTable;
use Ease\TWB5\WebPage;

require_once '../vendor/autoload.php';


\Ease\Shared::init(['ABRAFLEXI_URL', 'ABRAFLEXI_LOGIN', 'ABRAFLEXI_PASSWORD', 'ABRAFLEXI_COMPANY'], '../.env');

\Ease\WebPage::singleton(new WebPage(_('AbraFlexi x DataTables')));

\Ease\Part::jQueryze();

\Ease\WebPage::singleton()->addToMain(new DataTable(new \AbraFlexi\Adresar()));

\Ease\WebPage::singleton()->draw();
