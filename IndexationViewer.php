<?php

namespace Piwik\Plugins\IndexationViewer;

use Piwik\Plugin;

class IndexationViewer extends Plugin
{
    public function registerEvents(): array
    {
        return [
            'Translate.getClientSideTranslationKeys' => 'getClientSideTranslationKeys',
        ];
    }

    public function getClientSideTranslationKeys(&$result)
    {
        $result[] = 'IndexationViewer_IndexTitle';
        $result[] = 'IndexationViewer_Status';
        $result[] = 'IndexationViewer_SelectSegment';
        $result[] = 'IndexationViewer_SelectStatus';
        $result[] = 'IndexationViewer_AnyStatus';
        $result[] = 'IndexationViewer_StatusDoneOK';
        $result[] = 'IndexationViewer_StatusDoneError';
        $result[] = 'IndexationViewer_StatusDoneOkTemporary';
        $result[] = 'IndexationViewer_StatusDoneInvalidated';
        $result[] = 'IndexationViewer_SearchHelp';
        $result[] = 'IndexationViewer_SearchPattern';
        $result[] = "IndexationViewer_Period";
        $result[] = "IndexationViewer_PeriodBeginDate";
        $result[] = "IndexationViewer_PeriodEndDate";
        $result[] = "IndexationViewer_IndexedAt";
    }
}
