<?php

namespace Piwik\Plugins\IndexationViewer\Utils;

use Piwik\Piwik;

final class Period
{
    /**
     * Get Period label from ID.
     * @param int $periodId
     * @return string
     */
    public static function label(int $periodId): string
    {
        return array_flip(Piwik::$idPeriods)[$periodId];
    }
}
