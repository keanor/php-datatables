# PHP DataTables Server-Side

Example, for users table:

1) Create datatable class:

```php
<?php
/**
 * Created by PhpStorm.
 * User: keanor
 * Date: 17.02.17
 * Time: 11:39
 */
namespace Administration\DataTable;

use PHPDataTables\AbstractDataTable;

/**
 * Class UserDataTable
 * 
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
                'search_type' => 'exactly',
                'label' => 'ID',
            ],
        ]);
        $this->addColumn([
            'name' => 'role',
            'options' => [
                'label' => 'Роль',
            ],
        ]);
        $this->addColumn([
            'name' => 'phone',
            'options' => [
                'search_type' => 'fulltext',
                'label' => 'Телефон',
            ],
        ]);
        $this->addColumn([
            'name' => 'last_name',
            'options' => [
                'label' => 'Фамилия',
            ],
        ]);
        $this->addColumn([
            'name' => 'first_name',
            'options' => [
                'label' => 'Имя',
            ],
        ]);
        $this->addColumn([
            'name' => 'second_name',
            'options' => [
                'label' => 'Отчество',
            ],
        ]);
    }
}
```

2) Inject DataTableView into page with table:

2.1) Create DataTable in controller:

```php
// ...

        $adapter = new DoctrineDBAL($this->connection);
        $usersDataTable = new UserDataTable($adapter);

        $dataTableView = new DataTableView(
            '#usersTable', // HTML ID Attribute
            '/administration/user/data', // ajax URL
            $usersDataTable,
            [ // html tag <table> attributes
                'id' => 'usersTable',
                'class' => 'table table-striped table-bordered',
                'cellspacing' => '0',
                'width' => '100%',
            ]
        );

// ... invoke renderer or include view
```

2.2) Invoke DataTableView in template

For native php:
```php
// render table
echo $dataTableView->renderHtml();

// render js
echo '<script type="text/javascript">';
echo $dataTableView->renderJs();
echo '</script>';
```

For twig:
```twig
{% block content %}
    {{ dataTableView.renderHtml() | raw }}
{% endblock %}
{% block inline %}
    <script type="text/javascript">
    {{ dataTableView.renderJs() | raw }}
    </script>
{% endblock %}
```

3) Create data request handler

In controller:
```php
        $adapter = new DoctrineDBAL($this->connection);
        $table = new UserDataTable($adapter);
        $data = $table->getData($this->getRequest());
        // echo json_encode($data);
        return new JsonModel($data);
```

Propose pull requests!