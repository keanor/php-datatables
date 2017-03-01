<?php
namespace PHPDataTables\Action;

abstract class AbstractActionType
{
    abstract public function setOptions(array $options);

    abstract public function getOptions():array;

    abstract public function injectData(array &$items);
}
