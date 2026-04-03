<?php
/**
 * NOTICE OF LICENSE
 * 
 * This file is licensed under the Software License Agreement.
 *
 * With the purchase or the installation of the software in your application
 * you accept the license agreement.
 *
 * You must not modify, adapt or create derivative works of this source code
 *
 * @author Arkonsoft
 * @copyright 2024 Arkonsoft
 */

namespace Arkonsoft\PsModule\Core\Tab;

if(!defined('_PS_VERSION_')) {
    exit;
}

interface TabManagerInterface
{

    /**
     * @param string $controllerClassName
     * @return int
     */
    public function getIdByControllerClassName($controllerClassName): int;

    /**
     * @param string $controllerClassName
     * @param string|array $tabName
     * @param string $tabParent
     * @param bool $shouldBeVisibleInMenu
     * @return bool
     */
    public function installTab($controllerClassName, $tabName, $tabParent, $shouldBeVisibleInMenu): bool;

    /**
     * Removes a single admin tab. Returns true if the tab no longer exists after the call (including when it was already absent).
     *
     * @param string $controllerClassName
     * @return bool
     */
    public function uninstallTab($controllerClassName): bool;

    /**
     * Removes all given tabs in reverse installation order (deepest / child tabs first, then parents).
     * Pass the same list and order as for installation.
     *
     * @param TabConfiguration[] $tabConfigurations
     * @return bool False if any single uninstall failed; true when the list is empty or every step succeeded
     */
    public function uninstallTabsInReverseInstallOrder(array $tabConfigurations): bool;
}