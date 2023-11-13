<?php

namespace Piwik\Plugins\IndexationViewer;

use Exception;
use Piwik\Piwik;

final class Controller extends \Piwik\Plugin\Controller
{
    /**
     * @throws Exception if user does not have SuperUser access.
     */
    public function index(): string
    {
        Piwik::checkUserHasSuperUserAccess();

        return $this->renderTemplate('index');
    }
}
