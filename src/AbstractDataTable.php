<?php
namespace PHPDataTables\DataTables;

use Zend\Http\PhpEnvironment\Request;

/**
 * Class AbstractDataTable
 * @package PHPDataTables\DataTables
 */
abstract class AbstractDataTable
{
    /**
     * DataTables configuration
     *
     * @var array
     */
    protected $configuration;

    /**
     * DataTables adapter
     *
     * @var Adapter\AdapterInterface
     */
    private $adapter;

    /**
     * DataTables column
     *
     * @var Column[]
     */
    private $columns = [];

    /**
     * AbstractDataTable constructor.
     *
     * @param Adapter\AdapterInterface $adapter
     * @param array $configuration
     */
    public function __construct(Adapter\AdapterInterface $adapter, array $configuration = [])
    {
        $this->adapter = $adapter;

        // set default configuration
        if (!isset($configuration['max_page_size'])) {
            $configuration['max_page_size'] = 1000;
        }

        if (!isset($configuration['default_page_size'])) {
            $configuration['default_page_size'] = 20;
        }

        $this->configuration = $configuration;
    }

    /**
     * Add column
     *
     * @param array $spec
     * @throws \Exception
     *
     * @return void
     */
    public function addColumn(array $spec): void
    {
        if (!isset($spec['name'])) {
            throw new \Exception('Missing required option "name"!');
        }
        $jsName = (string)$spec['name'];
        $dbName = isset($spec['dbName']) ? $spec['dbName'] : $jsName;
        $options = isset($spec['options']) ? $spec['options'] : [];

        $this->columns[] = new Column($jsName, $dbName, $options);
    }

    /**
     * Get column
     *
     * @param int $index
     *
     * @return Column
     */
    public function get($index): Column
    {
        return (isset($this->columns[$index])) ? $this->columns[$index] : null;
    }

    /**
     * @param Request|null $request
     * @return array
     */
    public function getData(Request $request = null): array
    {
        if (!$request) {
            $request = new Request();
        }

        // pagination
        $this->setLimits($request);

        // ordering
        $this->setLimits($request);

        // search
        $this->setSearch($request);

        // filtering
        $this->setFilters($request);

        // get total count
        $count = $this->getAdapter()->getTotalCount($this->getTableName());

        // get data
        $items = $this->getAdapter()->getData(
            $this->getTableName(),
            array_map(function (Column $column) {
                return $column->getDbName();
            }, $this->getColumns())
        );

        // create DataTables structure
        $result = array(
            'sEcho' => (int)$request->getQuery('sEcho'),
            'iTotalRecords' => $count,
            'iTotalDisplayRecords' => count($items),
            'aaData' => $items
        );

        return $result;
    }

    /**
     * Parse limits from request
     *
     * @param Request $request
     */
    protected function setLimits(Request $request)
    {
        $limit = (int)$request->getQuery('iDisplayStart');
        if ($limit > $this->configuration['max_page_size']) {
            $limit = $this->configuration['max_page_size'];
        }

        if ($limit == -1) {
            $limit = $this->configuration['max_page_size'];
        }

        $offset = (int)$request->getQuery('iDisplayLength');

        $this->getAdapter()->setLimits($offset, $limit);
    }

    /**
     * @param Request $request
     */
    protected function setOrders(Request $request)
    {
        $orders = [];
        if ($request->getQuery('iSortingCols')) {
            $ordersCount = (int)$request->getQuery('iSoringCols');
            for ($i = 0; $i < $ordersCount; $i++) {
                $orderCol = (int)$request->getQuery('iSortCol_' . $i);

                $column = $this->get($orderCol);
                if (($column instanceof Column) && ($column->isAllowOrder())) {
                    $direction = $request->getQuery('sSortDir_' . $i);
                    $orders[$column->getDbName()] = ($direction === 'asc') ? 'asc' : 'desc';
                }
            }

            $this->getAdapter()->setOrders($orders);
        }
    }

    /**
     * @param Request $request
     */
    protected function setSearch(Request $request)
    {
        $search = [];

        if ($request->getQuery('sSearch')) {
            $searchString = (string)$request->getQuery('sSearch');

            foreach ($this->getColumns() as $index => $column) {
                if ($column->isAllowSearch()) {
                    $search[$column->getDbName()] = [
                        'value' => $searchString,
                        'exactly' => $column->isExactlySearch()
                    ];

                    if ($request->getQuery('sSearch_' . $index)) {

                    }
                }
            }

            $this->getAdapter()->setSearch($search);
        }
    }

    /**
     * Set field filters
     *
     * @param Request $request
     */
    protected function setFilters(Request $request)
    {
        $filters = [];

        foreach ($this->getColumns() as $index => $column) {
            $searchString = $request->getQuery('sSearch_' . $index);
            if ($column->isAllowSearch() && $searchString) {
                $filters[$column->getDbName()] = [
                    'value' => $searchString,
                    'exactly' => $column->isExactlySearch()
                ];
            }
        }

        $this->getAdapter()->setFilters($filters);
    }

    /**
     * @return Column[]
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @return Adapter\AdapterInterface
     */
    public function getAdapter(): Adapter\AdapterInterface
    {
        return $this->adapter;
    }

    /**
     * @return string
     */
    abstract public function getTableName(): string;
}
