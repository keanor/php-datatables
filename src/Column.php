<?php
/**
 * Created by PhpStorm.
 * User: keanor
 * Date: 16.02.17
 * Time: 17:47
 */
namespace PHPDataTables;

/**
 * Class Column
 * @package PHPDataTables\DataTables
 */
class Column
{
    const OPTION_SEARCH_TYPE = 'search_type';
    const OPTION_ALLOW_SEARCH = 'allow_search_fulltext';
    const OPTION_ALLOW_ORDER = 'allow_order';
    const OPTION_LABEL = 'label';

    const SEARCH_TYPE_FULLTEXT = 'fulltext';
    const SEARCH_TYPE_EXACTLY = 'exactly';

    /**
     * @var string
     */
    private $jsName;

    /**
     * @var string
     */
    private $dbName;

    /**
     * @var array
     */
    private $options;

    /**
     * Column constructor.
     *
     * @param string $jsName
     * @param string $dbName
     * @param array $options
     */
    public function __construct($jsName, $dbName, array $options = [])
    {
        $this->jsName = $jsName;
        $this->dbName = $dbName;
        $this->options = $options;
    }

    /**
     * @return string
     */
    public function getJsName(): string
    {
        return $this->jsName;
    }

    /**
     * @return string
     */
    public function getDbName(): string
    {
        return $this->dbName;
    }

    /**
     * @return bool
     */
    public function isExactlySearch(): bool
    {
        return ($this->getOption(self::OPTION_SEARCH_TYPE) === self::SEARCH_TYPE_EXACTLY);
    }

    /**
     * @return bool
     */
    public function isAllowSearch(): bool
    {
        return (bool)$this->getOption(self::OPTION_ALLOW_SEARCH, false);
    }

    /**
     * @return bool
     */
    public function isAllowOrder(): bool
    {
        return (bool)$this->getOption(self::OPTION_ALLOW_ORDER, false);
    }

    /**
     * Get option by name
     *
     * @param string $key (see self::OPTION_*)
     * @param mixed $default
     *
     * @return mixed
     */
    public function getOption($key, $default = null)
    {
        if (isset($this->options[$key])) {
            return $this->options[$key];
        }

        return $default;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->getOption('label', $this->jsName);
    }
}
