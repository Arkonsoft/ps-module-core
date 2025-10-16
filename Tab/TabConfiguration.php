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
 * @copyright 2025 Arkonsoft
 * @license Commercial - The terms of the license are subject to a proprietary agreement between the author (Arkonsoft) and the licensee
 */

namespace Arkonsoft\PsModule\Core\Tab;

if (!defined('_PS_VERSION_')) {
    exit;
}

class TabConfiguration
{
    /**
     * @var string
     */
    private $controllerClassName;

    /**
     * @var string
     */
    private $tabName;

    /**
     * @var string
     */
    private $tabParent;

    /**
     * @var bool
     */
    private $shouldBeVisibleInMenu;

    public function __construct(
        $controllerClassName,
        $tabName,
        $tabParent,
        $shouldBeVisibleInMenu
    ) {
        $this->controllerClassName = $controllerClassName;
        $this->tabName = $tabName;
        $this->tabParent = $tabParent;
        $this->shouldBeVisibleInMenu = $shouldBeVisibleInMenu;
    }

    /**
     * @return string
     */
    public function getControllerClassName()
    {
        return (string) $this->controllerClassName;
    }

    /**
     * @return string
     */
    public function getTabName()
    {
        return (string) $this->tabName;
    }
    
    /**
     * @return string
     */
    public function getTabParent()
    {
        return (string) $this->tabParent;
    }

    /**
     * @return bool
     */
    public function getShouldBeVisibleInMenu()
    {
        return (bool) $this->shouldBeVisibleInMenu;
    }
}
