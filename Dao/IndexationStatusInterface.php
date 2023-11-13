<?php

namespace Piwik\Plugins\IndexationViewer\Dao;

use Piwik\Period;
use Piwik\Plugins\IndexationViewer\Model\IndexationStatus as IndexationStatusModel;
use Piwik\Segment;

interface IndexationStatusInterface
{
    /**
     * Find All Indexations labeled as `done` in archive.
     *
     * @param int $siteId Site id
     * @param Period $period The Period to search for archives status in tables.
     * @param string $segment
     * @param int $status
     * @param int $page
     * @param int $pageSize
     * @return array<IndexationStatusModel>
     */
    public function findAll(
        int $siteId,
        Period $period,
        Segment $segment,
        int $status,
        int $page,
        int $pageSize
    ): array;
}
