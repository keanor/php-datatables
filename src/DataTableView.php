<?php
/**
 * Created by PhpStorm.
 * User: keanor
 * Date: 17.02.17
 * Time: 12:36
 */
namespace PHPDataTables;
use Zend\Json\Json;

/**
 * Class ViewHelper
 * @package PHPDataTables
 */
class DataTableView
{
    /**
     * @var string
     */
    private $jsId;

    /**
     * @var string
     */
    private $serverSideUrl;

    /**
     * @var AbstractDataTable
     */
    private $dataTable;

    /**
     * @var array
     */
    private $htmlTableAttributes;

    /**
     * ViewHelper constructor.
     * @param $jsId
     * @param $serverSideUrl
     * @param $dataTable
     * @param $htmlTableAttributes
     */
    public function __construct($jsId, $serverSideUrl, $dataTable, $htmlTableAttributes)
    {
        $this->jsId = $jsId;
        $this->serverSideUrl = $serverSideUrl;
        $this->dataTable = $dataTable;
        $this->htmlTableAttributes = $htmlTableAttributes;
    }

    /**
     * Render html and js code for DataTables
     *
     * @return string
     */
    public function renderJs(): string
    {
        $template = <<<'JS'
$(document).ready(function() {
    $('%s').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": "%s",
        "columns": %s
    });
});
JS;
        $columns = array_map(function (Column $column) {
            return [ 'data' => $column->getJsName() ];
        }, $this->dataTable->getColumns());

        return sprintf($template, $this->jsId, $this->serverSideUrl, Json::encode($columns));
    }

    public function renderHtml(): string
    {
        $row = $this->buildRowString('th');
        $template = '
            <table %s>
                <thead><tr>%s</tr></thead>
                <tfoot><tr>%s</tr></tfoot>
            </table>
        ';

        return sprintf($template, $this->buildAttributeString(), $row, $row);
    }

    /**
     * Build array attributes to html string
     *
     * @return string
     */
    private function buildAttributeString(): string
    {
        $result = '';
        foreach ($this->htmlTableAttributes as $name => $value) {
            $result .= sprintf(' %s="%s"', $name, htmlspecialchars($value));
        }

        return $result;
    }

    /**
     * Build array columns to row string
     *
     * @param string $cellTag
     *
     * @return string
     */
    private function buildRowString($cellTag): string
    {
        $result = '';
        foreach ($this->dataTable->getColumns() as $column) {
            $result .= sprintf('<%s>%s</%s>', $cellTag, $column->getLabel(), $cellTag);
        }

        return $result;
    }
}
