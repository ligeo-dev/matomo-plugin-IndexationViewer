<?php

namespace Piwik\Plugins\IndexationViewer\Dto;

use Piwik\Plugins\IndexationViewer\Model\IndexationStatus as IndexationStatusModel;
use Piwik\Plugins\IndexationViewer\Utils\ArchiveStatus;
use Piwik\Plugins\IndexationViewer\Utils\Period;

final class IndexationStatus extends AbstractDto
{
    protected $idArchive;
    protected $name;
    protected $idSite;
    protected $periodBeginDate;
    protected $periodEndDate;
    protected $period;
    protected $tsArchived;
    protected $value;

    public static function fromModel(IndexationStatusModel $model): self
    {
        $dto = new self();

        $dto->idArchive = $model->getIdarchive();
        $dto->name = $model->getName();
        $dto->idSite = $model->getIdsite();
        $dto->periodBeginDate = $model->getDate1();
        $dto->periodEndDate = $model->getDate2();
        $dto->period = Period::label($model->getPeriod());
        $dto->tsArchived = $model->getTsArchived();
        $dto->value = ArchiveStatus::translate($model->getValue());

        return $dto;
    }

    /**
     * @param array<IndexationStatusModel> $modelList
     * @return array<self>
     */
    public static function fromModelList(array $modelList): array
    {
        return array_map(function (IndexationStatusModel $model) {
            return self::fromModel($model);
        }, $modelList);
    }
}
