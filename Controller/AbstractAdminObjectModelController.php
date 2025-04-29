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

abstract class AbstractAdminObjectModelController extends AbstractAdminController
{
    abstract public function getObjectModelClassName(): string;

    abstract public function getListColumns(): array;

    abstract public function getFormFields(): array;

    public function __construct()
    {
        parent::__construct();
        
        $this->className = $this->getObjectModelClassName();
        $this->table = $this->getTable();
        $this->identifier = $this->getIdentifier();
        $this->lang = $this->isMultilang();

        $this->setupMultishop();
    }

    public function initContent()
    {
        $this->prepareList();
        $this->prepareForm();

        parent::initContent();
    }

    protected function setupListActions()
    {
        $this->addRowAction('edit');
        $this->addRowAction('delete');
    }

    public function prepareList()
    {
        $this->list_id = 'position';
        $this->position_identifier = 'position';
        $this->_defaultOrderBy = 'position';
        $this->_defaultOrderWay = 'ASC';

        $this->fields_list = $this->getListColumns();

        $this->setupListActions();
    }

    public function prepareForm()
    {
        $this->fields_form = $this->getFormFields();
    }
    
    protected function getTable(): string
    {
        $reflection = new \ReflectionClass($this->getObjectModelClassName());

        return (string) $reflection->getStaticPropertyValue('definition')['table'];
    }

    protected function getIdentifier(): string
    {
        $reflection = new \ReflectionClass($this->getObjectModelClassName());

        return (string) $reflection->getStaticPropertyValue('definition')['primary'];
    }

    protected function isMultilang(): bool
    {
        $reflection = new \ReflectionClass($this->getObjectModelClassName());
        
        try {
            $definition = $reflection->getStaticPropertyValue('definition');
            return isset($definition['multilang']) ? (bool) $definition['multilang'] : false;
        } catch (\ReflectionException $e) {
            return false;
        }
    }

    protected function getCurrentIdentifier(): int
    {
        return (int) \Tools::getValue($this->identifier, 0);
    }

    protected function setupMultishop()
    {
        if (!\Shop::isFeatureActive()) {
            return;
        }

        $joinType = \Shop::getContext() === \Shop::CONTEXT_SHOP ? 'INNER' : 'LEFT';

        $this->_join = $joinType . ' JOIN `' . _DB_PREFIX_ . $this->table . '_shop` shop ON (a.' . $this->identifier . ' = shop.' . $this->identifier . ' AND shop.id_shop = ' . $this->context->shop->id . ')';
    }

    public function ajaxProcessUpdatePositions()
    {
        $way = (int) \Tools::getValue('way');
        $itemId = (int) \Tools::getValue('id');
        $positions = \Tools::getValue($this->getObjectModelClassNameInSnakeCase());

        foreach ($positions as $position => $value) {
            $pos = explode('_', $value);

            $objectId = (int) $pos[2];

            if ($objectId === $itemId) {
                $objectClassName = $this->getObjectModelClassName();

                $item = new $objectClassName($objectId);

                if (!\Validate::isLoadedObject($item)) {
                    echo '{
                        "hasError" : true, 
                        "errors" : 
                        "This item (' . (int) $objectId . ') can t be loaded"
                    }';
                    exit;
                }

                $result = $this->updatePosition($way, $position, $objectId);

                if ($result) {
                    echo 'ok position ' . (int) $position . ' for item ' . (int) $pos[1] . '\r\n';
                } else {
                    echo '{
                        "hasError" : true, 
                        "errors" : 
                        "Can not update item ' . (int) $objectId . ' to position ' . (int) $position . ' "
                    }';
                }

                break;
            }
        }
    }

    protected function updatePosition(int $way, int $position, int $objectId): bool
    {
        $query = new \DbQuery();
        $query->select('position, ' . $this->getIdentifier())
            ->from($this->getTable())
            ->where($this->getIdentifier() . ' = ' . (int) $objectId);

        $result = \Db::getInstance()->getRow($query);

        if (!$result) {
            return false;
        }

        $currentPosition = (int) $result['position'];

        if ($way === 0) {
            if ($currentPosition === $position) {
                return true;
            }

            $sql = '
                UPDATE `' . _DB_PREFIX_ . $this->getTable() . '` 
                SET position = position + 1
                WHERE position >= ' . (int) $position . ' 
                AND position < ' . (int) $currentPosition . ' 
                AND ' . $this->getIdentifier() . ' != ' . (int) $objectId;

            return (bool) \Db::getInstance()->execute($sql) && \Db::getInstance()->execute(
                '
                UPDATE `' . _DB_PREFIX_ . $this->getTable() . '`
                SET position = ' . (int) $position . '
                WHERE ' . $this->getIdentifier() . ' = ' . (int) $objectId
            );
        }

        $sql = '
            UPDATE `' . _DB_PREFIX_ . $this->getTable() . '`
            SET position = position - 1 
            WHERE position > ' . (int) $currentPosition . '
            AND position <= ' . (int) $position . '
            AND ' . $this->getIdentifier() . ' != ' . (int) $objectId;

        return (bool) \Db::getInstance()->execute($sql) && \Db::getInstance()->execute(
            '
                UPDATE `' . _DB_PREFIX_ . $this->getTable() . '`
                SET position = ' . (int) $position . '
                WHERE ' . $this->getIdentifier() . ' = ' . (int) $objectId
        );
    }

    private function getObjectModelClassNameInSnakeCase(): string
    {
        $reflection = new \ReflectionClass($this->getObjectModelClassName());

        $className = $reflection->getShortName();

        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $className));
    }
}
