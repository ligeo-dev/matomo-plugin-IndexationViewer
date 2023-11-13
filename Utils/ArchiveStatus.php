<?php

namespace Piwik\Plugins\IndexationViewer\Utils;

use Piwik\DataAccess\ArchiveWriter;
use Piwik\Piwik;

final class ArchiveStatus
{
    /**
     * Get Archive Status ID from label.
     * @param string|null $statusLabel
     * @return int
     */
    public static function resolve(?string $statusLabel): int
    {
        switch ($statusLabel) {
            case 'DONE_OK':
                return ArchiveWriter::DONE_OK;
            case 'DONE_ERROR':
                return ArchiveWriter::DONE_ERROR;
            case 'DONE_INVALIDATED':
                return ArchiveWriter::DONE_INVALIDATED;
            default:
                return 0;
        }
    }

    /**
     * Get Archive Statue translation from ID.
     * @param int $statusId
     * @return string
     */
    public static function translate(int $statusId): string
    {
        switch ($statusId) {
            case ArchiveWriter::DONE_OK;
                return Piwik::translate('IndexationViewer_StatusDoneOK');
            case ArchiveWriter::DONE_ERROR;
                return Piwik::translate('IndexationViewer_StatusDoneError');
            case ArchiveWriter::DONE_INVALIDATED;
                return Piwik::translate('IndexationViewer_StatusDoneInvalidated');
            default:
                return Piwik::translate('IndexationViewer_StatusUnknown');
        }
    }
}
