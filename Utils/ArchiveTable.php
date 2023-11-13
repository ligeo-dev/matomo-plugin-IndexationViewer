<?php

namespace Piwik\Plugins\IndexationViewer\Utils;

use Piwik\Common;
use Piwik\DataAccess\ArchiveTableCreator;
use Piwik\Period;
use Piwik\Period\Range;

final class ArchiveTable
{
    /**
     * @param Period $period
     * @return array<string>
     * @see ArchiveTableCreator::getNumericTable
     */
    public static function getNames(Period $period): array
    {
        $beginDate = $period->getDateStart();
        $endDate = $period->getDateEnd();
        $range = new Range('month', "{$beginDate},{$endDate}");

        return array_map(function (Period\Month $month) {
            $date = $month->getDateStart();
            $tableNamePrefix = "archive_".ArchiveTableCreator::NUMERIC_TABLE;
            $archiveTableName = $tableNamePrefix."_".ArchiveTableCreator::getTableMonthFromDate($date);

            return Common::prefixTable($archiveTableName);
        }, $range->getSubperiods());
    }

    /**
     * @param string $tableName
     * @return bool
     * @see ArchiveTableCreator::createArchiveTablesIfAbsent()
     */
    public static function exists(string $tableName): bool
    {
        if (is_null(ArchiveTableCreator::$tablesAlreadyInstalled)) {
            ArchiveTableCreator::refreshTableList();
        }

        return in_array($tableName, ArchiveTableCreator::$tablesAlreadyInstalled ?? []);
    }
}
