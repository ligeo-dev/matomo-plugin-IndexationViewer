<?php

namespace Piwik\Plugins\IndexationViewer;

use Exception;
use Piwik\Period\Factory;
use Piwik\Piwik;
use Piwik\Plugin\SettingsProvider;
use Piwik\Plugins\API\API as BaseAPI;
use Piwik\Plugins\API\ProcessedReport;
use Piwik\Plugins\IndexationViewer\Dao\IndexationStatus as IndexationStatusDao;
use Piwik\Plugins\IndexationViewer\Dao\IndexationStatusInterface;
use Piwik\Plugins\IndexationViewer\Dto\IndexationStatus as IndexationStatusDto;
use Piwik\Plugins\IndexationViewer\Dto\SegmentOption as SegmentOptionDto;
use Piwik\Plugins\IndexationViewer\Model\SegmentOption as SegmentOptionModel;
use Piwik\Plugins\IndexationViewer\Utils\ArchiveStatus;
use Piwik\Plugins\SegmentEditor;
use Piwik\Plugins\UsersManager\UserPreferences;
use Piwik\Segment;

final class API extends BaseAPI
{
    /** @var IndexationStatusInterface */
    private $indexationStatusDao;

    /** @var UserPreferences */
    private $userPreferences;

    public function __construct(
        SettingsProvider $settingsProvider,
        ProcessedReport $processedReport,
        IndexationStatusDao $indexationStatusDao,
        UserPreferences $userPreferences
    ) {
        parent::__construct($settingsProvider, $processedReport);
        $this->indexationStatusDao = $indexationStatusDao;
        $this->userPreferences = $userPreferences;
    }

    // Public methods -------------------------------------------------------------------------------------------------

    /**
     * @param int|null $idSite
     * @param string|null $date
     * @param string|null $period
     * @param string|null $segment
     * @param string|null $status
     * @param int|null $page
     * @param int|null $limitPerPage
     * @return array<IndexationStatusDto>
     * @throws Exception If user does not have superuser access.
     */
    public function getIndexationStatuses(
        ?int $idSite = null,
        ?string $date = 'today',
        ?string $period = 'day',
        ?string $segment = '',
        ?string $status = '',
        ?int $page = 1,
        ?int $limitPerPage = 15
    ): array {
        Piwik::checkUserHasSuperUserAccess();

        try {
            $idSite = $idSite ?? intval($this->userPreferences->getDefaultWebsiteId());
            $segment = new Segment($segment, [$idSite]);
            $period = Factory::build($period, $date);
            $status = ArchiveStatus::resolve($status);
        } catch (Exception $e) {
            return [];
        }

        $data = $this->indexationStatusDao->findAll($idSite, $period, $segment, $status, $page, $limitPerPage);

        return IndexationStatusDto::fromModelList($data);
    }

    /**
     * @param int|null $idSite
     * @return array<SegmentOptionDto>
     * @throws Exception If user does not have superuser access.
     */
    public function getSegmentOptions(?int $idSite = null): array
    {
        Piwik::checkUserHasSuperUserAccess();

        $segmentOptions = array_map(function (array $data): SegmentOptionModel {
            return new SegmentOptionModel($data);
        }, SegmentEditor\API::getInstance()->getAll($idSite ?? false));

        // Add default segment to the list.
        $defaultSegment = new SegmentOptionModel([
            'definition' => '',
            'name' => Piwik::translate('IndexationViewer_DefaultSegment'),
        ]);

        array_unshift($segmentOptions, $defaultSegment);

        return SegmentOptionDto::fromModelList($segmentOptions);
    }
}
