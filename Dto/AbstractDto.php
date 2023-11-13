<?php

namespace Piwik\Plugins\IndexationViewer\Dto;

use JsonSerializable;
use Piwik\Plugins\IndexationViewer\Utils\StringUtils;

abstract class AbstractDto implements JsonSerializable
{
    public function jsonSerialize(): array
    {
        $data = [];

        foreach (get_object_vars($this) as $name => $value) {
            $data[StringUtils::snakeCase($name)] = $value;
        }

        return $data;
    }
}
