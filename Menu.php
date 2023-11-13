<?php

namespace Piwik\Plugins\IndexationViewer;

use Piwik\Menu\MenuAdmin;
use Piwik\Piwik;
use Piwik\Plugin\Menu as BaseMenu;

final class Menu extends BaseMenu
{
    public function configureAdminMenu(MenuAdmin $menu): void
    {
        if (Piwik::hasUserSuperUserAccess()) {
            $menu->addDiagnosticItem('IndexationViewer_IndexationViewer', $this->urlForDefaultAction());
        }
    }
}
