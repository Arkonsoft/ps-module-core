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
 */

declare(strict_types=1);

namespace Arkonsoft\PsModule\Core\Controller;

abstract class AbstractAdminController extends \ModuleAdminController
{
    public function __construct()
    {
        parent::__construct();

        $this->bootstrap = true;
        $this->context = \Context::getContext();
        $this->context->controller = $this;
    }

    public function initContent()
    {
        if ($this->isShopContextRequired() && \Shop::isFeatureActive() && \Shop::getContext() !== \Shop::CONTEXT_SHOP) {
            $this->displayShopContextWarning();

            return;
        }

        if ($this->isShopContextRequired() && empty($this->getActionName()) && !$this->ajax) {
            $this->displayShopContextInfo();
        }

        parent::initContent();
    }

    public function isShopContextRequired(): bool
    {
        return false;
    }

    public function displayShopContextWarning()
    {
        $this->informations[] = 'Wybierz sklep, aby kontynuować: ' . $this->getShopLinksHtml();
    }

    public function displayShopContextInfo()
    {
        $currentShop = \Shop::getShop((int) $this->context->shop->id);

        if (!is_array($currentShop) || empty($currentShop)) {
            return;
        }

        $this->informations[] = 'Wprowadzasz zmiany dla sklepu: <b>' . $currentShop['name'] . '</b>';
    }

    protected function getShopLinksHtml(): string
    {
        $shopLinks = [];

        foreach (\Shop::getShops(true) as $shop) {
            $shopLinks[] = $this->generateShopLink($shop);
        }

        return implode(', ', $shopLinks);
    }

    protected function generateShopLink(array $shop): string
    {
        $shopLink = $this->getControllerActionUrl([
            'setShopContext' => 's-' . $shop['id_shop'],
        ]);

        return '<a href="' . $shopLink . '"><b>' . $shop['name'] . '</b></a>';
    }

    protected function getControllerActionUrl(array $params = []): string
    {
        return $this->context->link->getAdminLink(str_replace('Controller', '', get_class($this)), true, [], $params);
    }

    protected function getActionName(): string
    {
        $values = \Tools::getAllValues();

        $filtered = array_filter(array_keys($values), function ($key) {
            return str_contains($key, $this->table);
        });

        if (empty($filtered)) {
            return '';
        }

        $actionKey = reset($filtered);

        $actionName = str_replace($this->table, '', $actionKey);

        return $actionName;
    }
}
