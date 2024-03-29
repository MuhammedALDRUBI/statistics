<?php

namespace Statistics\DataProcessors\Traits;

use Statistics\DataProcessors\DataProcessor;
use Statistics\DateProcessors\DateProcessor;
use DataResourceInstructors\OperationContainers\OperationGroups\OperationGroup;

trait DataProcessorSingleInstanceMethods
{
    protected static array $instances = [];
    public function setInstanceProps(array $dataToProcess , OperationGroup $operationGroup  , ?DateProcessor $dateProcessor = null) : DataProcessor
    {
        $this->dataToProcess = $dataToProcess;
        $this->dateProcessor = $dateProcessor;
        $this->processedData = [];
        return $this->setOperationGroup($operationGroup);
    }

    /**
     * @return DataProcessor
     */
    protected static function createInstance(): DataProcessor
    {
        return new static();
    }

    /**
     * @return DataProcessor
     *
     * WHEN YOU NEED TO PROCESS THE STATISTICS RESULT DATA .... Don't Use 'setInstanceProps' method to pass the needed processing parameters
     * Otherwise the result of data processing will be an empty array always
     */
    final public static function Singleton() : DataProcessor
    {
        $className = static::class;

        if (!array_key_exists($className , self::$instances))
        {
            self::$instances[$className] = static::createInstance();
        }
        return self::$instances[$className];
    }
}
