<?php
namespace PHPDataTables\Adapter;

/**
 * Interface AdapterInterface
 * @package PHPDataTables\DataTables\Adapter
 */
interface AdapterInterface
{
    /**
     * Set query limits
     *
     * @param int $offset
     * @param int $limit
     *
     * @return void
     */
    public function setLimits(int $offset, int $limit);

    /**
     * Set query orders
     *
     * @param array $orders
     *
     * @return void
     */
    public function setOrders(array $orders);

    /**
     * Set search, search any!!! condition
     *
     * @param array $search
     *
     * @return void
     */
    public function setSearch(array $search);

    /**
     * Set column value filters
     *
     * @param array $filters
     *
     * @return void
     */
    public function setFilters(array $filters);

    /**
     * Return total count
     *
     * @param string $tableName
     *
     * @return int
     */
    public function getTotalCount(string $tableName): int;

    /**
     * Get data
     *
     * @param string $tableName
     * @param array $columnNames
     *
     * @return array
     */
    public function getData(string $tableName, array $columnNames): array;
}
