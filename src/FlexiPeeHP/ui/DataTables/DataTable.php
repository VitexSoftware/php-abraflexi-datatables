<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FlexiPeeHP\ui\DataTables;

/**
 * Description of DataTable
 *
 * @author Vítězslav Dvořák <info@vitexsoftware.cz>
 */
class DataTable extends \Ease\Html\TableTag
{
    /**
     * Where to get/put data
     * @var string 
     */
    public $ajax2db = 'flexibeedata.php';

    /**
     * Enable Editor ?
     * @var boolean 
     */
    public $rw = false;

    /**
     * Buttons to render on top of the datatable
     * @var array 
     */
    public $buttons = null;

    /**
     * Buttons to show by default
     * @var array 
     */
    public $defaultButtons = ['reload', 'copy', 'excel', 'print', 'pdf', 'pageLength',
        'colvis'];

    /**
     *
     * @var array 
     */
    public $columns = null;

    /**
     * Add footer columns
     * @var boolean 
     */
    public $showFooter = false;

    /**
     *
     * @var \DBFinance\Engine
     */
    public $engine = null;

    /**
     *
     * @var handle 
     */
    public $rndId;

    /**
     * 
     * @param \FlexiPeeHP\FlexiBeeRW $engine
     * @param array $properties
     */
    public function __construct($engine = null, $properties = array())
    {
        $this->engine     = $engine;
        $this->ajax2db    = $this->dataSourceURI($engine);
        $this->columnDefs = $engine->getOnlineColumnsInfo();

        parent::__construct(null,
            ['class' => 'display', 'style' => 'width: 100%']);

        $gridTagID     = $this->setTagId($engine->getObjectName());
        $this->columns = $this->prepareColumns($this->columnDefs);

        \Ease\JQuery\Part::jQueryze();

//        $this->includeJavaScript('assets/datatables.js');
//        $this->includeCss('assets/datatables.css');

        $this->includeJavaScript('js/jquery.dataTables.min.js');
        $this->includeJavaScript('js/dataTables.bootstrap.min.js');
//        $this->includeJavaScript('assets/Select-1.2.6/js/dataTables.select.min.js');
        $this->includeCss('css/dataTables.bootstrap.min.css');
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
////        $this->includeCss('https://nightly.datatables.net/rowgroup/css/rowGroup.dataTables.css');
////        $this->includeJavaScript('https://nightly.datatables.net/rowgroup/js/dataTables.rowGroup.js');
//
//        $this->includeJavaScript('assets/moment-with-locales.js');
//        $this->includeJavaScript('//cdn.datatables.net/plug-ins/1.10.19/sorting/datetime-moment.js');


        $this->addJavaScript('$.fn.dataTable.ext.buttons.reload = {
    text: \''._('Reload').'\',
    action: function ( e, dt, node, config ) {
        dt.ajax.reload();
    }
};');


        $this->addJavaScript('
$("#gridFilter'.$gridTagID.'").hide( );
$.fn.dataTable.ext.buttons.filter'.$gridTagID.' = {
    text: \''._('Filter').'\',
    action: function ( e, dt, node, config ) {
        $("#gridFilter'.$gridTagID.'").appendTo( $("#'.$gridTagID.'_filter") );            
        $("#gridFilter'.$gridTagID.'").toggle();
    }
};');

        $this->defaultButtons[] = 'filter'.$gridTagID;

        if (array_key_exists('buttons', $properties)) {
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

        $this->includeJavaScript('js/savy.js');
        $this->includeJavaScript('js/advancedfilter.js');
    }

    public static function getDateColumns($columns)
    {
        $dateColumns = [];
        foreach ($columns as $columnInfo) {
            if ($columnInfo['type'] == 'date') {
                $dateColumns[$columnInfo['name']] = $columnInfo['label'];
            }
        }
        return $dateColumns;
    }

    /**
     * Convert Ease Columns to DataTables Format
     */
    public function prepareColumns($easeColumns)
    {
        foreach ($easeColumns as $columnRaw) {
            $column['label'] = $columnRaw['title'];
            $column['name'] = $columnRaw['propertyName'];
            switch ($columnRaw['type']) {
                case 'string':
                    $column['type'] = 'text';
                    break;
                default :
                    $this->addStatusMessage('Uknown column '.$columnRaw['name'].' type '.$columnRaw['type']);
                    $column['type'] = 'text';
                    break;
            }
            $dataTablesColumns[] = $column;
        }
        return $dataTablesColumns;
    }

    public function finalize()
    {
        $this->addRowHeaderColumns(self::columnsToHeader($this->columns));
//        $this->addItem(new FilterDialog($this->getTagID(), $this->columns));
        $this->addJavascript($this->javaScript($this->columns));
        if ($this->showFooter) {
            $this->addFooter();
        }

        parent::finalize();
    }

    public static function getUri()
    {
        $uri = parent::getUri();
        return substr($uri, -1) == '/' ? $uri.'index.php' : $uri;
    }

    /**
     * Prepare DataSource URI 
     * 
     * @param \DBFinance\Engine $engine
     * 
     * @return string Data Source URI
     */
    public function dataSourceURI($engine)
    {
        $conds = ['class' => get_class($engine)];
        if (!is_null($engine->filter)) {
            $conds = array_merge($engine->filter, $conds);
        }
        return $this->ajax2db.'?'.http_build_query($conds);
    }

    /**
     * Add TOP button
     * @param string $function create|edit|remove
     */
    public function addButton($function)
    {
        $this->buttons[] = '{extend: "'.$function.'"}';
    }

    public function addCustomButton($caption,
                                    $callFunction = "alert( 'Button activated' );")
    {
        $this->buttons[] = '{
                text: \''.$caption.'\',
                action: function ( e, dt, node, config ) {
                    '.$callFunction.'
                }            
}';
    }

    public function preTableCode()
    {
        
    }

    public function tableCode()
    {
        
    }

    /**
     * 
     * @param arrays $columns
     * 
     * @return string
     */
    public function javaScript($columns)
    {
        $tableID = $this->getTagID();
        return $this->preTableCode().'
//    $.fn.dataTable.moment(\'DD. MM. YYYY\');            
//    $.fn.dataTable.moment(\'YYYY-MM-DD HH:mm:ss\');            
    var '.$tableID.' = $(\'#'.$tableID.'\').DataTable( {
        '.$this->footerCallback().'
        dom: "Bfrtip",
        colReorder: true,
        stateSave: true,
        responsive: true,
        "processing": false,
        "serverSide": false,
        "lengthMenu": [[10, 25, 50, 100, 200, 500, -1], [10, 25, 50, 100 ,200 ,500 , "'._('All pages').'"]],
        "language": {
                "url": "assets/i18n/Czech.lang"
        },
        '.$this->tableCode($tableID).'
        ajax: "'.$this->ajax2db.'",
        '.$this->columnDefs().'
        columns: [
            '.self::getColumnsScript($columns).'
        ],
        select: true
        '.( $this->buttons ? ',        buttons: [ '.\Ease\JQuery\Part::partPropertiesToString($this->buttons).']'
                : '').'
    } );

    '.$this->postTableCode().'
            $(\'.tablefilter\').change( function() { '.$tableID.'.draw(); } );
';
//    $("#'.$tableID.'_filter").css(\'border\', \'1px solid red\');
//setInterval( function () { '.$tableID.'.ajax.reload( null, false ); }, 30000 );        
    }

    //    '.self::columnIndexNames($columns,$tableID).'
    public static function columnIndexNames($columns, $of)
    {
        $colsCode[] = 'var tableColumnIndex = [];';
        foreach (\Ease\Sand::reindexArrayBy($columns, 'name') as $colName => $columnInfo) {
            $colsCode[] = "tableColumnIndex['".$colName."'] = ".$of.".column('".$colName.":name').index();";
        }
        return implode("\n", $colsCode);
    }

    /**
     * Gives You Columns JS 
     * 
     * @param array $columns
     * 
     * @return string
     */
    public static function getColumnsScript($columns)
    {
        $parts = [];
        foreach ($columns as $properties) {
            $name               = $properties['name'];
            unset($properties['name']);
            $properties['data'] = $name;
            $parts[]            = '{'.\Ease\JQuery\Part::partPropertiesToString($properties).'}';
        }
        return implode(", \n", $parts);
    }

    /**
     * Engine columns to Table Header columns format
     * 
     * @param array $columns
     * 
     * @return array
     */
    public static function columnsToHeader($columns)
    {
        foreach ($columns as $properties) {
            if (array_key_exists('hidden', $properties) && ($properties['hidden']
                == true)) {
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
     * Define footer Callback code
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
            $foterCallBack = '
        "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;
 
            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === \'string\' ?
                    i.replace(/[\$,]/g, \'\')*1 :
                    typeof i === \'number\' ?
                        i : 0;
            };
            '.$initialContent.'
        },
';
        }

        return $foterCallBack;
    }

    public function addFooter()
    {
        foreach (current($this->tHead->getContents())->getContents() as $column) {
            $columns[] = '';
        }
        unset($columns['id']);
        $this->addRowFooterColumns($columns);
    }

    public function columnDefs()
    {
        
    }

    public function postTableCode()
    {
        
    }
}
