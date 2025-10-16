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

use PrestaShopBundle\Entity\Repository\TabRepository;

if(!defined('_PS_VERSION_')) {
    exit;
}

class TabManager implements TabManagerInterface
{
    /**
     * @var \Module
     */
    protected $module;

    public function __construct(\Module $module) 
    {
        $this->module = $module;
    }

    /**
     * @param string $controllerClassName
     * @param string|array $tabName
     * @param string $tabParent
     * @param bool $shouldBeVisibleInMenu
     * @return bool
     */
    public function installTab($controllerClassName, $tabName, $tabParent, $shouldBeVisibleInMenu): bool
    {
        if ($this->getIdByControllerClassName($controllerClassName)) {
            return true;
        }

        $tab = new \Tab();
        $tab->id_parent = (int) $this->getIdByControllerClassName($tabParent);
        $tab->name = [];

        if (is_array($tabName)) {
            $tab->name = $tabName;
        } else {
            foreach (\Language::getLanguages(true, false, true) as $langId) {
                $tab->name[(int) $langId] = $tabName;
            }
        }

        $tab->class_name = $controllerClassName;
        $tab->module = $this->module->name;
        $tab->active = $shouldBeVisibleInMenu;

        return (bool) $tab->add();
    }

    /**
     * @param string $controllerClassName
     * @return bool
     */
    public function uninstallTab($controllerClassName): bool
    {
        $tabId = (int) $this->getIdByControllerClassName($controllerClassName);

        $tab = new \Tab((int) $tabId);
        return (bool) $tab->delete();
    }

    public function getIdByControllerClassName($controllerClassName): int
    {
        /**
         * @var TabRepository $tabRepository
         */
        $tabRepository = $this->module->get('prestashop.core.admin.tab.repository');

        return (int) $tabRepository->findOneIdByClassName($controllerClassName);
    }
}