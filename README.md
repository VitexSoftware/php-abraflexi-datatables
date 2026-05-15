![Package Logo](abraflexi-datatables.svg?raw=true "Project Logo")

php-abraflexi-datatables
========================

PHP library for displaying [AbraFlexi](https://www.abraflexi.eu/) evidence data in interactive [DataTables](https://datatables.net/) powered by Bootstrap 5.

[![Latest Stable Version](https://poser.pugx.org/vitexsoftware/datatables-abraflexi/v/stable)](https://packagist.org/packages/vitexsoftware/datatables-abraflexi)
[![License](https://poser.pugx.org/vitexsoftware/datatables-abraflexi/license)](https://packagist.org/packages/vitexsoftware/datatables-abraflexi)

Features
--------

- Seamless integration of DataTables with AbraFlexi REST API
- Bootstrap 5 widget support via [VitexSoftware EASE framework](https://github.com/VitexSoftware/ease-framework)
- Server-side and client-side rendering modes
- Debian/Ubuntu packaging with AppStream metadata

Installation
------------

**Via Composer:**

```shell
composer require vitexsoftware/datatables-abraflexi
```

**Via Debian/Ubuntu package:**

```shell
sudo apt install php-abraflexi-datatables
```

Requirements
------------

| Dependency | Version |
|---|---|
| PHP | >= 8.0 |
| vitexsoftware/abraflexi-bricks | ^1.5 |
| vitexsoftware/ease-bootstrap5-widgets-abraflexi | ^1.4 |

Configuration
-------------

Set AbraFlexi connection parameters as environment variables or in your application bootstrap:

```
ABRAFLEXI_URL=https://demo.flexibee.eu:5434
ABRAFLEXI_LOGIN=winstrom
ABRAFLEXI_PASSWORD=winstrom
ABRAFLEXI_COMPANY=demo_de
```

Usage
-----

```php
use AbraFlexi\ui\DataTables\RWTableau;

$table = new RWTableau('adresar');
$table->show();
```

Example
-------

Run `make` to update the build autoloader and deploy assets, then open the [Example](Example) directory to see AbraFlexi evidence `adresar` rendered as a DataTable.

![Screenshot](screnshot.png?raw=true "Screenshot of Examples")

License
-------

MIT — see [LICENSE](LICENSE).
