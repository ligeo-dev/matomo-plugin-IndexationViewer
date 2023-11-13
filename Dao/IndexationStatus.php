<?php

namespace Piwik\Plugins\IndexationViewer\Dao;

use Exception;
use Piwik\Db;
use Piwik\Period;
use Piwik\Plugins\IndexationViewer\Model\IndexationStatus as IndexationStatusModel;
use Piwik\Plugins\IndexationViewer\Utils\ArchiveTable;
use Piwik\Plugins\VisitFrequency\API as VisitFrequencyAPI;
use Piwik\Segment;
use Piwik\Segment\SegmentExpression;

final class IndexationStatus implements IndexationStatusInterface
{
    // Public methods -------------------------------------------------------------------------------------------------

    public function findAll(
        int $siteId,
        Period $period,
        Segment $segment,
        int $status,
        int $page,
        int $pageSize
    ): array {
        [
            'query' => $query,
            'parameters' => $parameters,
        ] = $this->buildFindAllQuery($siteId, $period, $segment, $status, $pageSize, ($page - 1) * $pageSize);

        try {
            $indexationStatuses = Db::fetchAll($query, $parameters);
        } catch (Exception $e) {
            // Nothing to do.
        }

        return array_map(function (array $row) {
            return new IndexationStatusModel($row);
        }, $indexationStatuses ?? []);
    }

    // Private methods ------------------------------------------------------------------------------------------------

    /**
     * @param int $siteId
     * @param Period $period
     * @param Segment $segment
     * @param int $status
     * @param int $limit
     * @param int $offset
     * @return array{0: string, 1: array}
     */
    private function buildFindAllQuery(
        int $siteId,
        Period $period,
        Segment $segment,
        int $status,
        int $limit,
        int $offset
    ): array {
        [
            'query' => $subQueriesUnion,
            'parameters' => $parameters,
        ] = $this->buildFindAllSubQueriesUnion($siteId, $period, $segment, $status, $limit + $offset);

        return [
            'query' => "{$subQueriesUnion} ORDER BY ts_archived DESC, idarchive LIMIT {$limit} OFFSET {$offset};",
            'parameters' => $parameters,
        ];
    }


    /**
     * @param int $siteId
     * @param Period $period
     * @param Segment $segment
     * @param int $status
     * @param $limit
     * @return array{query: string, parameters: array}
     */
    private function buildFindAllSubQueriesUnion(
        int $siteId,
        Period $period,
        Segment $segment,
        int $status,
        $limit
    ): array {
        $subQueries = [];
        $parameters = [];

        foreach (ArchiveTable::getNames($period) as $archiveTableName) {
            if (!ArchiveTable::exists($archiveTableName)) {
                continue;
            }

            [
                'query' => $subQuery,
                'parameters' => $subParameters,
            ] = $this->buildFindAllSubQuery($archiveTableName, $siteId, $period, $segment, $status, $limit);

            $subQueries[] = $subQuery;
            array_push($parameters, ...$subParameters);
        }

        return [
            'query' => join(' UNION ALL ', $subQueries),
            'parameters' => $parameters,
        ];
    }

    /**
     * @param string $tableName
     * @param int $siteId
     * @param Period $period
     * @param Segment $segment
     * @param int $status
     * @param int $limit
     * @return array{query: string, parameters: array}
     */
    private function buildFindAllSubQuery(
        string $tableName,
        int $siteId,
        Period $period,
        Segment $segment,
        int $status,
        int $limit
    ): array {
        $parameters = [];

        // Filter by Site.
        $subQuerySelectWhereList[] = "idsite = ?";
        $parameters[] = $siteId;

        // Filter by name (i.e. segment).
        $subQuerySelectWhereNameList = [];
        if (!empty($segment->getHash())) {
            $subQuerySelectWhereNameList[] = "name LIKE ?";
            $parameters[] = "%done{$segment->getHash()}%";
        } else {
            $subQuerySelectWhereNameList[] = "name = ?";
            $parameters[] = "done";
        }

        $visitTypes = [VisitFrequencyAPI::NEW_VISITOR_SEGMENT, VisitFrequencyAPI::RETURNING_VISITOR_SEGMENT];
        foreach ($visitTypes as $visitorTypeSegment) {
            try {
                $combinedSegment = new Segment(
                    Segment::combine($segment->getString(), SegmentExpression::AND_DELIMITER, $visitorTypeSegment),
                    [$siteId]
                );
            } catch (Exception $e) {
                continue;
            }

            $subQuerySelectWhereNameList[] = "name LIKE ?";
            $parameters[] = "%done{$combinedSegment->getHash()}%";
        }

        if (!empty($subQuerySelectWhereNameList)) {
            $subQuerySelectWhereName = join(' OR ', $subQuerySelectWhereNameList);
            $subQuerySelectWhereList[] = "({$subQuerySelectWhereName})";
        }

        // Filter by Period and dates.
        $subQuerySelectWhereList[] = "period = ?";
        $parameters[] = $period->getId();

        $subQuerySelectWhereList[] = "date1 >= ?";
        $parameters[] = $period->getDateStart()->toString();

        $subQuerySelectWhereList[] = "date2 <= ?";
        $parameters[] = $period->getDateEnd()->toString();

        // Filter by status.
        if (!empty($status)) {
            $subQuerySelectWhereList[] = "value = ?";
            $parameters[] = $status;
        }

        $subQuerySelectWhere = join(' AND ', $subQuerySelectWhereList);
        $subQueryOrderAndLimit = "ORDER BY ts_archived DESC, idarchive LIMIT {$limit}";

        return [
            'query' => "(SELECT * FROM {$tableName} WHERE {$subQuerySelectWhere} {$subQueryOrderAndLimit})",
            'parameters' => $parameters,
        ];
    }
}
