<?php
namespace PHPDataTables\Adapter;

use Doctrine\DBAL\Connection;

/**
 * Class DoctrineDBAL
 * @package PHPDataTables\DataTables\Adapter
 */
class DoctrineDBAL implements AdapterInterface
{
    /**
     * @var Connection
     */
    private $queryBuilder;

    /**
     * Doctrine DBAL adapter constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->queryBuilder = $connection->createQueryBuilder();
    }

    /**
     * Set limits
     *
     * @param int $offset
     * @param int $limit
     *
     * @return void
     */
    public function setLimits(int $offset, int $limit)
    {
        $this->queryBuilder->setFirstResult($offset);
        $this->queryBuilder->setMaxResults($limit);
    }

    /**
     * Set query orders
     *
     * @param array $orders
     *
     * @return void
     */
    public function setOrders(array $orders)
    {
        foreach ($orders as $column => $direction) {
            $this->queryBuilder->addOrderBy($column, $direction);
        }
    }

    /**
     * Set search, search any!!! condition
     *
     * @param array $search
     *
     * @return void
     */
    public function setSearch(array $search)
    {
        $expr = $this->queryBuilder->expr();
        $conditions = $this->prepareConditions($search);

        if (count($conditions) > 0) {
            $this->queryBuilder->where(call_user_func_array(
                [$expr, 'orX'],
                $conditions
            ));
        }
    }

    /**
     * Set column value filters
     *
     * @param array $filters
     *
     * @return void
     */
    public function setFilters(array $filters)
    {
        $expr = $this->queryBuilder->expr();
        $conditions = $this->prepareConditions($filters);

        if (count($conditions) > 0) {
            $this->queryBuilder->andWhere(call_user_func_array(
                [$expr, 'andX'],
                $conditions
            ));
        }
    }

    /**
     * @param array $conditions
     * @return array
     */
    private function prepareConditions(array $conditions): array
    {
        $expr = $this->queryBuilder->expr();
        $prepared = [];

        foreach ($conditions as $column => $data) {
            if (isset($data['exactly']) && ($data['exactly'] == true)) {
                $prepared[] = $expr->eq($column, $expr->literal($data['value']));
            } else {
                $prepared[] = $expr->like($column, $expr->literal('%' . $data['value'] . '%'));
            }
        }

        return $prepared;
    }

    /**
     * Return total count
     *
     * @param string $tableName
     *
     * @return int
     */
    public function getTotalCount(string $tableName): int
    {
        $queryBuilder = clone $this->queryBuilder;
        $count = $queryBuilder->select('COUNT(*)')
            ->from($tableName)
            ->execute()
            ->fetchColumn();

        return (int)$count;
    }

    /**
     * Get data
     *
     * @param string $tableName
     * @param array $columnNames
     *
     * @return array
     */
    public function getData(string $tableName, array $columnNames): array
    {
        return $this->queryBuilder->select($columnNames)
            ->from($tableName)
            ->execute()
            ->fetchAll();
    }
}
