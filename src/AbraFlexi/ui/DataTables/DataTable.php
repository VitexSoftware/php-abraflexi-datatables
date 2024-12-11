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
 * Description of DataTable.
 *
 * @author Vítězslav Dvořák <info@vitexsoftware.cz>
 */
class DataTable extends \Ease\Html\TableTag
{
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
     */
    public array $buttons = [];

    /**
     * Buttons to show by default.
     *
     * @var array<string>
     */
    public array $defaultButtons = ['reload', 'copy', 'excel', 'print', 'pdf', 'pageLength',
        'colvis'];
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
    private array $columnDefs = [];

    /**
     * @param \AbraFlexi\RW $engine
     */
    public function __construct(\AbraFlexi\RO $engine, array $properties = [])
    {
        $this->engine = $engine;
        $this->ajax2db = $this->dataSourceURI($engine);
        $this->columnDefs = $engine->getOnlineColumnsInfo();

        parent::__construct(
            null,
            ['class' => 'display', 'style' => 'width: 100%'],
        );

        $gridTagID = $this->setTagId($engine->getObjectName());
        $this->columns = $this->prepareColumns($this->columnDefs);

        //        $this->includeJavaScript('assets/datatables.js');
        //        $this->includeCss('assets/datatables.css');

        $this->includeJavaScript('js/jquery.dataTables.min.js');
        $this->includeJavaScript('js/dataTables.bootstrap5.min.js');
        //        $this->includeJavaScript('assets/Select-1.2.6/js/dataTables.select.min.js');
        $this->includeCss('css/dataTables.bootstrap5.min.css');
        //        $this->includeCss('assets/Select-1.2.6/css/select.bootstrap.min.css');
        //
        //        $this->includeJavaScript('assets/ColReorder-1.5.0/js/dataTables.colReorder.min.js');
        //        $this->includeCss('assets/ColReorder-1.5.0/css/colReorder.bootstrap.min.css');
        //
        //        $this->includeJavaScript('assets/Responsive-2.2.2/js/dataTables.responsive.min.js');
        //        $this->includeJavaScript('assets/Responsive-2.2.2/js/responsive.bootstrap.min.js');
        //
        //        $this->includeJavaScript('js/selectize.min.js');
        //        $this->includeCss('css/slectize.css');
        //        $this->includeCss('css/selectize.bootstrap3.css');
        //
        //
        $this->setTagClass('table table-striped table-bordered');
        //
        //        $this->includeJavaScript('assets/Buttons-1.5.2/js/dataTables.buttons.min.js');
        //        $this->includeJavaScript('assets/Buttons-1.5.2/js/buttons.bootstrap.min.js');
        //
        //        $this->includeCss('assets/Buttons-1.5.2/css/buttons.bootstrap.min.css');
        //
        //        $this->includeJavaScript('assets/JSZip-2.5.0/jszip.min.js');
        //        $this->includeJavaScript('assets/pdfmake-0.1.36/pdfmake.min.js');
        //        $this->includeJavaScript('assets/pdfmake-0.1.36/vfs_fonts.js');
        //        $this->includeJavaScript('assets/Buttons-1.5.2/js/buttons.html5.min.js');
        //        $this->includeJavaScript('assets/Buttons-1.5.2/js/buttons.print.min.js');
        //
        //        $this->includeJavaScript('assets/Buttons-1.5.2/js/buttons.colVis.min.js');
        //
        //        $this->includeCss('assets/RowGroup-1.0.3/css/rowGroup.bootstrap.min.css');
        //        $this->includeJavaScript('assets/RowGroup-1.0.3/js/rowGroup.bootstrap.min.js');
        //        $this->includeJavaScript('assets/RowGroup-1.0.3/js/dataTables.rowGroup.min.js');
        //
        // //        $this->includeCss('https://nightly.datatables.net/rowgroup/css/rowGroup.dataTables.css');
        // //        $this->includeJavaScript('https://nightly.datatables.net/rowgroup/js/dataTables.rowGroup.js');
        //
        //        $this->includeJavaScript('assets/moment-with-locales.js');
        //        $this->includeJavaScript('//cdn.datatables.net/plug-ins/1.10.19/sorting/datetime-moment.js');

        $this->addJavaScript(<<<'EOD'
$.fn.dataTable.ext.buttons.reload = {
    text: '
EOD._('Reload').<<<'EOD'
',
    action: function ( e, dt, node, config ) {
        dt.ajax.reload();
    }
};
EOD);

        $this->addJavaScript(<<<'EOD'

$("#gridFilter
EOD.$gridTagID.<<<'EOD'
").hide( );
$.fn.dataTable.ext.buttons.filter
EOD.$gridTagID.<<<'EOD'
 = {
    text: '
EOD._('Filter').<<<'EOD'
',
    action: function ( e, dt, node, config ) {
        $("#gridFilter
EOD.$gridTagID.'").appendTo( $("#'.$gridTagID.<<<'EOD'
_filter") );
        $("#gridFilter
EOD.$gridTagID.<<<'EOD'
").toggle();
    }
};
EOD);

        $this->defaultButtons[] = 'filter'.$gridTagID;

        if (\array_key_exists('buttons', $properties)) {
            if ($properties['buttons'] === false) {
                $this->defaultButtons = [];
            } else {
                foreach ($properties['buttons'] as $function) {
                    $this->addButton($function);
                }
            }
        }

        foreach ($this->defaultButtons as $button) {
            $this->addButton($button);
        }

        //        $this->includeJavaScript('js/advancedfilter.js');
    }

    public static function getDateColumns(array $columns): array
    {
        $dateColumns = [];

        foreach ($columns as $columnInfo) {
            if ($columnInfo['type'] === 'date') {
                $dateColumns[$columnInfo['name']] = $columnInfo['label'];
            }
        }

        return $dateColumns;
    }

    /**
     * Convert Ease Columns to DataTables Format.
     *
     * @param mixed $easeColumns
     */
    public function prepareColumns(array $easeColumns): array
    {
        $dataTablesColumns = [];

        foreach ($easeColumns as $columnRaw) {
            $column['label'] = $columnRaw['title'];
            $column['name'] = $columnRaw['propertyName'];

            switch ($columnRaw['type']) {
                case 'string':
                    $column['type'] = 'text';

                    break;

                default:
                    $this->addStatusMessage('Uknown column '.$columnRaw['name'].' type '.$columnRaw['type']);
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
     * @param Engine $engine
     *
     * @return string Data Source URI
     */
    public function dataSourceURI($engine)
    {
        $conds = ['class' => \get_class($engine)];

        if (null !== $engine->filter) {
            $conds = array_merge($engine->filter, $conds);
        }

        return $this->ajax2db.'?'.http_build_query($conds);
    }

    /**
     * Add TOP button.
     *
     * @param string $function create|edit|remove
     */
    public function addButton($function): void
    {
        $this->buttons[] = '{extend: "'.$function.'"}';
    }

    public function addCustomButton(
        $caption,
        $callFunction = "alert( 'Button activated' );"
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

    public function preTableCode(): void
    {
    }

    public function tableCode(): void
    {
    }

    /**
     * @param array $columns
     *
     * @return string
     */
    public function javaScript($columns)
    {
        $tableID = $this->getTagID();

        return $this->preTableCode().<<<'EOD'

//    $.fn.dataTable.moment('DD. MM. YYYY');
//    $.fn.dataTable.moment('YYYY-MM-DD HH:mm:ss');
    var
EOD.$tableID.' = $(\'#'.$tableID.<<<'EOD'
').DataTable( {

EOD.$this->footerCallback().<<<'EOD'

        dom: "Bfrtip",
        colReorder: true,
        stateSave: true,
        responsive: true,
        "processing": false,
        "serverSide": false,
        "lengthMenu": [[10, 25, 50, 100, 200, 500, -1], [10, 25, 50, 100 ,200 ,500 , "
EOD._('All pages').<<<'EOD'
"]],
        "language": {
                "url": "assets/i18n/Czech.lang"
        },

EOD.$this->tableCode($tableID).<<<'EOD'

        ajax: "
EOD.$this->ajax2db.<<<'EOD'
",

EOD.$this->columnDefs().<<<'EOD'

        columns: [

EOD.self::getColumnsScript($columns).<<<'EOD'

        ],
        select: true

EOD.($this->buttons ? ',        buttons: [ '.\Ease\Part::partPropertiesToString($this->buttons).']' : '').<<<'EOD'

    } );


EOD.$this->postTableCode().<<<'EOD'

            $('.tablefilter').change( function() {
EOD.$tableID.<<<'EOD'
.draw(); } );

EOD;
        //    $("#'.$tableID.'_filter").css(\'border\', \'1px solid red\');
        // setInterval( function () { '.$tableID.'.ajax.reload( null, false ); }, 30000 );
    }

    //    '.self::columnIndexNames($columns,$tableID).'
    public static function columnIndexNames($columns, $of)
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
     * @return string
     */
    public static function getColumnsScript(array $columns)
    {
        $parts = [];

        foreach ($columns as $properties) {
            $name = $properties['name'];
            unset($properties['name']);
            $properties['data'] = $name;
            $parts[] = '{'.\Ease\Part::partPropertiesToString($properties).'}';
        }

        return implode(", \n", $parts);
    }

    /**
     * Engine columns to Table Header columns format.
     *
     * @param array<string, array<string, string>> $columns
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
                $header[$properties['name']] = $properties['label'];
            } else {
                $header[$properties['name']] = $properties['name'];
            }
        }

        return $header;
    }

    /**
     * Define footer Callback code.
     *
     * @param string $initialContent
     *
     * @return string
     */
    public function footerCallback($initialContent = null)
    {
        if (empty($initialContent)) {
            $foterCallBack = '';
        } else {
            $foterCallBack = <<<'EOD'

        "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;

            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };

EOD.$initialContent.<<<'EOD'

        },

EOD;
        }

        return $foterCallBack;
    }

    public function addFooter(): void
    {
        foreach (current($this->tHead->getContents())->getContents() as $column) {
            $columns[] = '';
        }

        unset($columns['id']);
        $this->addRowFooterColumns($columns);
    }

    public function columnDefs(): void
    {
    }

    public function postTableCode(): void
    {
    }

    /**
     * Include required assets in page.
     */
    public function finalize(): void
    {
        $this->includeCss($this->css);
        $this->includeJavascript($this->js);
        $this->addRowHeaderColumns(self::columnsToHeader($this->columns));
        //        $this->addItem(new FilterDialog($this->getTagID(), $this->columns));
        $this->addJavascript($this->javaScript($this->columns));

        if ($this->showFooter) {
            $this->addFooter();
        }

        parent::finalize();
    }
}
