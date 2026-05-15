<?php

declare(strict_types=1);

// Autoloader shim for package: php-abraflexi-datatables

require_once '/usr/share/php/EaseTWB5WidgetsAbraFlexi/autoload.php';

spl_autoload_register(function (string $class): void {
    $prefix = 'AbraFlexi\\ui\\DataTables\\';
    if (str_starts_with($class, $prefix)) {
        $file = '/usr/share/php/AbraFlexiUiDataTables/' . str_replace('\\', '/', substr($class, \strlen($prefix))) . '.php';
        if (file_exists($file)) {
            require $file;
        }
    }
});

require_once '/usr/share/php/Composer/InstalledVersions.php';

(function (): void {
    $versions = [];
    foreach (\Composer\InstalledVersions::getAllRawData() as $d) {
        $versions = array_merge($versions, $d['versions'] ?? []);
    }
    $name    = 'unknown';
    $version = '0.0.0';
    $versions[$name] = ['pretty_version' => $version, 'version' => $version,
        'reference' => null, 'type' => 'library', 'install_path' => __DIR__,
        'aliases' => [], 'dev_requirement' => false];
    \Composer\InstalledVersions::reload([
        'root' => ['name' => $name, 'pretty_version' => $version, 'version' => $version,
            'reference' => null, 'type' => 'library', 'install_path' => __DIR__,
            'aliases' => [], 'dev' => false],
        'versions' => $versions,
    ]);
})();
