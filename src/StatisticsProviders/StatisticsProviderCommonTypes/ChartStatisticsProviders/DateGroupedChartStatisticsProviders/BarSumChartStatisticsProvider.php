<?php

namespace Statistics\StatisticsProviders\StatisticsProviderCommonTypes\ChartStatisticsProviders\DateGroupedChartStatisticsProviders;

use DataResourceInstructors\OperationComponents\Columns\AggregationColumn;
use DataResourceInstructors\OperationComponents\Columns\Column;
use DataResourceInstructors\OperationContainers\OperationGroups\OperationGroup;
use DataResourceInstructors\OperationTypes\AggregationOperation;
use DataResourceInstructors\OperationTypes\SumOperation;
use Statistics\DataProcessors\DataProcessor;
use Statistics\DataProcessors\DBFetchedDataProcessors\ChartDataProcessors\DateGroupedChartDataProcessor;
use Statistics\DataResources\DBFetcherDataResources\ChartDataResources\DateGroupedChartDataResource\DateGroupedChartDataResourceTypes\DateGroupedSumChartDataResource;
use Statistics\DateProcessors\NeededDateProcessorDeterminers\DateGroupedDateProcessorDeterminer;
use Statistics\DateProcessors\NeededDateProcessorDeterminers\NeededDateProcessorDeterminer;
use Statistics\Interfaces\ModelInterfaces\StatisticsProviderModel;
use Statistics\Interfaces\StatisticsProvidersInterfaces\HasDefaultAdvancedOperations;
use Statistics\Interfaces\StatisticsProvidersInterfaces\NeedsModelClass;
use Statistics\StatisticsProviders\StatisticsProviderDecorator;


abstract class BarSumChartStatisticsProvider extends StatisticsProviderDecorator implements HasDefaultAdvancedOperations , NeedsModelClass
{
    public function __construct(?StatisticsProviderDecorator $statisticsProvider = null)
    {
        $this->setValidModel($this->getModelClass());
        parent::__construct($statisticsProvider);
    }

    /**
     * @return AggregationColumn
     * Array of AggregationColumn objects
     */
    abstract protected function getSumColumn() : AggregationColumn;

    public function getStatisticsTypeName(): string
    {
        return "barSumChart";
    }
    protected function getDataResourceOrdersByPriorityClasses()  :array
    {
        return [DateGroupedSumChartDataResource::class];
    }

    protected function getDataProcessorInstance(): DataProcessor
    {
        return DateGroupedChartDataProcessor::Singleton();
    }

    protected function getNeededDateProcessorDeterminerInstance(): NeededDateProcessorDeterminer
    {
        return DateGroupedDateProcessorDeterminer::Singleton();
    }

    protected function getDateColumnDefaultName() : string
    {
        return "created_at";
    }
    protected function getDateColumnName() : string
    {
        if($this->model instanceof StatisticsProviderModel)
        {
            return $this->model->getStatisticDateColumnName();
        }
        return $this->getDateColumnDefaultName();
    }

    protected function getDateColumn() : Column
    {
        return Column::create( $this->getDateColumnName() )->setResultProcessingColumnAlias("DateColumn");
    }

    protected function getSumOperation() : AggregationOperation
    {
        return SumOperation::create()->addAggregationColumn( $this->getSumColumn() );
    }

    protected function getSumOperationGroup() : OperationGroup
    {
        $dateColumn = $this->getDateColumn();
        return OperationGroup::create($this->model->getTable())
                             ->enableDateSensitivity($dateColumn)
                             ->addOperation( $this->getSumOperation() );
    }

    public function getDefaultAdvancedOperations() : array
    {
        return [
            $this->getSumOperationGroup()
        ];
    }

}
