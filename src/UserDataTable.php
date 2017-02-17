<?php
/**
 * Created by PhpStorm.
 * User: keanor
 * Date: 16.02.17
 * Time: 17:21
 */
namespace PHPDataTables\DataTables;

/**
 * Class UserDataTable
 * @package PHPDataTables\DataTables
 */
class UserDataTable extends AbstractDataTable
{
    /**
     * @return string
     */
    public function getTableName(): string
    {
        return 'users';
    }

    /**
     * Initialize table
     */
    public function init()
    {
        $this->addColumn([
            'name' => 'id',
            'options' => [
                'search_type' => 'exactly'
            ],
        ]);
        $this->addColumn([
            'name' => 'id',
            'options' => [
                'search_type' => 'fulltext'
            ],
        ]);
        $this->addColumn([
            'name' => 'first_name',
        ]);
        $this->addColumn([
            'name' => 'second_name',
        ]);
        $this->addColumn([
            'name' => 'last_name',
        ]);
    }
}
