<?php

namespace Piwik\Plugins\IndexationViewer\Dto;

use Piwik\Plugins\IndexationViewer\Model\SegmentOption as SegmentOptionModel;

final class SegmentOption extends AbstractDto
{
    protected $name;
    protected $definition;

    public static function fromModel(SegmentOptionModel $model): self
    {
        $dto = new self();

        $dto->name = $model->getName();
        $dto->definition = $model->getDefinition();

        return $dto;
    }

    /**
     * @param array<SegmentOptionModel> $modelList
     * @return array<self>
     */
    public static function fromModelList(array $modelList): array
    {
        return array_map(function (SegmentOptionModel $model) {
            return self::fromModel($model);
        }, $modelList);
    }
}
