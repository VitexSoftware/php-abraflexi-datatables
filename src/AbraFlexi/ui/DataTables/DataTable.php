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

namespace AbraFlexi\ui\DataTables;

/**
 * AbraFlexi evidence rendered as a Bootstrap 5 DataTable.
 *
 * Assets default to Debian package libjs-jquery-datatables
 * ({@see self::JS_ROOT}).
 *
 * @author Vítězslav Dvořák <info@vitexsoftware.cz>
 */
class DataTable extends \Ease\Html\TableTag
{
    /**
     * Debian jquery-datatables web path (javascript-common Alias).
     */
    public const JS_ROOT = '/javascript/jquery-datatables';

    /**
     * Where to get/put data.
     */
    public string $ajax2db = 'abraflexidata.php';

    /**
     * Enable Editor ?
     */
    public bool $rw = false;

    /**
     * Buttons to render on top of the datatable.
     *
     * @var list<string>
     */
    public array $buttons = [];

    /**
     * Buttons to show by default.
     *
     * Empty by default: Debian libjs-jquery-datatables does not ship the
     * Buttons extension. Pass ['buttons' => ['reload', …]] only when you also
     * load dataTables.buttons (+ bootstrap5) yourself (or via CDN).
     *
     * @var list<string>
     */
    public array $defaultButtons = [];

    /**
     * @var list<array<string, mixed>>
     */
    public array $columns = [];

    /**
     * Add footer columns.
     */
    public bool $showFooter = false;

    public \AbraFlexi\RO $engine;

    public $rndId;

    /**
     * Additional Style for DataTable.
     */
    public string $css = '';

    /**
     * Additional JavaScript for DataTable.
     */
    public string $js = '';

    /**
     * DataTables language JSON URL (Czech by default).
     */
    public string $languageUrl = 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/cs.json';

    /**
     * Raw column info from engine (getOnlineColumnsInfo / custom).
     *
     * @var array<string, array<string, mixed>>
     */
    protected array $columnDefs = [];

    /**
     * @param \AbraFlexi\RO        $engine
     * @param array<string, mixed> $properties e.g. ['buttons' => ['reload']|false]
     */
    public function __construct(\AbraFlexi\RO $engine, array $properties = [])
    {
        $this->engine = $engine;
        $this->ajax2db = $this->dataSourceURI($engine);
        $this->columnDefs = $engine->getOnlineColumnsInfo() ?? [];

        parent::__construct(
            null,
            ['class' => 'display', 'style' => 'width: 100%'],
        );

        $gridTagID = $this->setTagId($engine->getObjectName());
        $this->columns = $this->prepareColumns($this->columnDefs);

        \Ease\Part::jQueryze();
        $this->includeJavaScript(self::JS_ROOT.'/jquery.dataTables.min.js');
        $this->includeJavaScript(self::JS_ROOT.'/dataTables.bootstrap5.min.js');
        $this->includeCss(self::JS_ROOT.'/css/dataTables.bootstrap5.min.css');

        $this->setTagClass('table table-striped table-bordered');

        if (\array_key_exists('buttons', $properties)) {
            if ($properties['buttons'] === false) {
                $this->defaultButtons = [];
            } else {
                $this->defaultButtons = [];
                foreach ($properties['buttons'] as $function) {
                    $this->addButton((string) $function);
                }
            }
        }

        foreach ($this->defaultButtons as $button) {
            $this->addButton($button);
        }

        // Register reload helper only when Buttons extension is expected.
        if ($this->buttons !== []) {
            $this->addJavaScript(<<<'EOD'
$.fn.dataTable.ext.buttons = $.fn.dataTable.ext.buttons || {};
$.fn.dataTable.ext.buttons.reload = {
    text: '
EOD._('Reload').<<<'EOD'
',
    action: function ( e, dt, node, config ) {
        dt.ajax.reload();
    }
};
EOD);
        }

        unset($gridTagID);
    }

    /**
     * @param list<array<string, mixed>> $columns
     *
     * @return array<string, string>
     */
    public static function getDateColumns(array $columns): array
    {
        $dateColumns = [];

        foreach ($columns as $columnInfo) {
            if (($columnInfo['type'] ?? '') === 'date') {
                $dateColumns[$columnInfo['name']] = $columnInfo['label'] ?? $columnInfo['name'];
            }
        }

        return $dateColumns;
    }

    /**
     * Convert AbraFlexi properties.json columns to DataTables format.
     *
     * @param array<string, array<string, mixed>> $easeColumns
     *
     * @return list<array<string, mixed>>
     */
    public function prepareColumns(array $easeColumns): array
    {
        $dataTablesColumns = [];

        foreach ($easeColumns as $columnRaw) {
            $column = [
                'label' => (string) ($columnRaw['title'] ?? $columnRaw['name'] ?? $columnRaw['propertyName'] ?? ''),
                'name' => (string) ($columnRaw['propertyName'] ?? $columnRaw['name'] ?? ''),
                'type' => 'text',
            ];

            if ($column['name'] === '') {
                continue;
            }

            $abraflexiType = strtolower((string) ($columnRaw['type'] ?? 'string'));

            switch ($abraflexiType) {
                case 'string':
                case 'text':
                case 'integer':
                case 'numeric':
                case 'logic':
                case 'select':
                case 'relation':
                case 'date':
                case 'datetime':
                case 'timestamp':
                    $column['type'] = \in_array($abraflexiType, ['date', 'datetime', 'timestamp'], true)
                        ? 'date'
                        : 'text';

                    break;
                default:
                    $column['type'] = 'text';

                    break;
            }

            $dataTablesColumns[] = $column;
        }

        return $dataTablesColumns;
    }

    public static function getUri()
    {
        $uri = parent::getUri();

        return substr($uri, -1) === '/' ? $uri.'index.php' : $uri;
    }

    /**
     * Prepare DataSource URI.
     *
     * @param \AbraFlexi\RO $engine
     *
     * @return string Data Source URI
     */
    public function dataSourceURI($engine): string
    {
        $conds = ['class' => $engine::class];

        // AbraFlexi\RO::$filter is a ?string (FlexiBee @filter expression), not an array.
        if (\is_string($engine->filter) && $engine->filter !== '') {
            $conds['filter'] = $engine->filter;
        }

        return $this->ajax2db.'?'.http_build_query($conds);
    }

    /**
     * Add TOP button.
     *
     * @param string $function create|edit|remove|reload|pageLength|…
     */
    public function addButton($function): void
    {
        $this->buttons[] = '{extend: "'.$function.'"}';
    }

    public function addCustomButton(
        $caption,
        $callFunction = "alert( 'Button activated' );",
    ): void {
        $this->buttons[] = <<<'EOD'
{
                text: '
EOD.$caption.<<<'EOD'
',
                action: function ( e, dt, node, config ) {

EOD.$callFunction.<<<'EOD'

                }
}
EOD;
    }

    /**
     * Hook before DataTable JS options (must return string — concatenated into JS).
     */
    public function preTableCode(): string
    {
        return '';
    }

    /**
     * Extra DataTable options snippet (must return string — concatenated into JS).
     *
     * @param string|null $tableID
     */
    public function tableCode($tableID = null): string
    {
        return '';
    }

    /**
     * Hook after DataTable init (must return string — concatenated into JS).
     */
    public function postTableCode(): string
    {
        return '';
    }

    /**
     * columnDefs JS snippet (must return string — concatenated into JS).
     */
    public function columnDefs(): string
    {
        return '';
    }

    /**
     * @param list<array<string, mixed>> $columns
     */
    public function javaScript($columns): string
    {
        $tableID = $this->getTagID();
        $langUrl = $this->languageUrl;
        $dom = $this->buttons !== [] ? 'Bfrtip' : 'lfrtip';
        $buttonsJs = $this->buttons !== []
            ? ', buttons: [ '.\Ease\Part::partPropertiesToString($this->buttons).']'
            : '';

        return $this->preTableCode().<<<EOD

    var {$tableID} = $('#{$tableID}').DataTable({
{$this->footerCallback()}
        dom: "{$dom}",
        stateSave: true,
        responsive: true,
        processing: true,
        serverSide: false,
        lengthMenu: [[10, 25, 50, 100, 200, 500, -1], [10, 25, 50, 100, 200, 500, "
EOD._('All pages').<<<EOD
"]],
        language: { url: "{$langUrl}" },
{$this->tableCode($tableID)}
        ajax: {
            url: "{$this->ajax2db}",
            dataSrc: "data"
        },
{$this->columnDefs()}
        columns: [
EOD.self::getColumnsScript($columns).<<<EOD

        ]
{$buttonsJs}
    });

{$this->postTableCode()}
    $('.tablefilter').change(function () { {$tableID}.draw(); });

EOD;
    }

    /**
     * @param list<array<string, mixed>> $columns
     */
    public static function columnIndexNames($columns, $of): string
    {
        $colsCode[] = 'var tableColumnIndex = [];';

        foreach (\Ease\Functions::reindexArrayBy($columns, 'name') as $colName => $columnInfo) {
            $colsCode[] = "tableColumnIndex['".$colName."'] = ".$of.".column('".$colName.":name').index();";
        }

        return implode("\n", $colsCode);
    }

    /**
     * Gives You Columns JS.
     *
     * @param list<array<string, mixed>> $columns
     */
    public static function getColumnsScript(array $columns): string
    {
        $parts = [];

        foreach ($columns as $properties) {
            $name = $properties['name'];
            unset($properties['name'], $properties['label']);
            $properties['data'] = $name;
            $parts[] = '{'.\Ease\Part::partPropertiesToString($properties).'}';
        }

        return implode(", \n", $parts);
    }

    /**
     * Engine columns to Table Header columns format.
     *
     * @param list<array<string, mixed>> $columns
     *
     * @return array<string, string>
     */
    public static function columnsToHeader(array $columns): array
    {
        $header = [];

        foreach ($columns as $properties) {
            if (\array_key_exists('hidden', $properties) && ($properties['hidden'] === true)) {
                continue;
            }

            if (isset($properties['label'])) {
                $header[$properties['name']] = (string) $properties['label'];
            } else {
                $header[$properties['name']] = (string) $properties['name'];
            }
        }

        return $header;
    }

    /**
     * Define footer Callback code.
     *
     * @param string|null $initialContent
     */
    public function footerCallback($initialContent = null): string
    {
        if (empty($initialContent)) {
            return '';
        }

        return <<<EOD

        "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ? i : 0;
            };
{$initialContent}
        },

EOD;
    }

    public function addFooter(): void
    {
        $columns = [];
        $head = $this->tHead?->getContents();
        $firstRow = \is_array($head) ? current($head) : null;
        if (\is_object($firstRow) && method_exists($firstRow, 'getContents')) {
            foreach ($firstRow->getContents() as $column) {
                $columns[] = '';
            }
        }

        unset($columns['id']);
        if ($columns !== []) {
            $this->addRowFooterColumns($columns);
        }
    }

    /**
     * Include required assets in page.
     */
    public function finalize(): void
    {
        if ($this->css !== '') {
            $this->includeCss($this->css);
        }

        if ($this->js !== '') {
            $this->includeJavaScript($this->js);
        }

        $this->addRowHeaderColumns(self::columnsToHeader($this->columns));
        $this->addJavaScript($this->javaScript($this->columns));

        if ($this->showFooter) {
            $this->addFooter();
        }

        parent::finalize();
    }
}
