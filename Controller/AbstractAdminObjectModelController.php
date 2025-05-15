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

use Arkonsoft\PsModule\Core\ObjectModel\ObjectModelImageManager;
use Db;
use DbQuery;

abstract class AbstractAdminObjectModelController extends AbstractAdminController
{
    /**
     * @var ObjectModelImageManager
     */
    public $objectModelImageManager;

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
        $this->position_identifier = $this->getPositionIdentifier();
        $this->objectModelImageManager = new ObjectModelImageManager(
            $this->module->name,
        );

        $this->setupMultishop();
    }

    public function initContent()
    {
        $this->prepareList();
        $this->prepareForm();

        parent::initContent();
    }

    public function postProcess()
    {
        parent::postProcess();

        $this->processImagesDelete();
    }

    protected function setupListActions()
    {
        $this->addRowAction('edit');
        $this->addRowAction('delete');
    }

    public function prepareList()
    {
        $this->list_id = 'position';
        $this->position_identifier = $this->getPositionIdentifier();
        $this->_defaultOrderBy = $this->getPositionIdentifier();
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

    protected function getPositionIdentifier(): string
    {
        return 'position';
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

    public function processAdd()
    {
        if(empty(\Tools::getValue($this->getPositionIdentifier()))) {
            $_POST[$this->getPositionIdentifier()] = $this->getNextPosition();
        }

        return parent::processAdd();
    }

    public function processDelete()
    {
        $result = parent::processDelete();
        
        if ($result) {
            $this->normalizePositions();
        }
        
        return $result;
    }

    protected function normalizePositions(): void
    {
        $query = new \DbQuery();
        $query->select($this->getIdentifier() . ', ' . $this->getPositionIdentifier())
            ->from($this->getTable())
            ->orderBy($this->getPositionIdentifier() . ' ASC');

        $items = \Db::getInstance()->executeS($query);
        
        if (!$items) {
            return;
        }

        $position = 0;
        foreach ($items as $item) {
            $sql = '
                UPDATE `' . _DB_PREFIX_ . $this->getTable() . '`
                SET ' . $this->getPositionIdentifier() . ' = ' . (int) $position . '
                WHERE ' . $this->getIdentifier() . ' = ' . (int) $item[$this->getIdentifier()];
                
            \Db::getInstance()->execute($sql);
            $position++;
        }
    }

    protected function getNextPosition(): int
    {
        $query = new \DbQuery();
        $query->select('COUNT(*) as count')
            ->from($this->getTable());

        $result = \Db::getInstance()->getRow($query);
        
        if ((int) $result['count'] === 0) {
            return 0;
        }

        $query = new \DbQuery();
        $query->select('MAX(' . $this->getPositionIdentifier() . ')')
            ->from($this->getTable());
            
        $maxPosition = (int) \Db::getInstance()->getValue($query);

        return $maxPosition + 1;
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
        $query->select($this->getPositionIdentifier() . ', ' . $this->getIdentifier())
            ->from($this->getTable())
            ->where($this->getIdentifier() . ' = ' . (int) $objectId);

        $result = \Db::getInstance()->getRow($query);

        if (!$result) {
            return false;
        }

        $currentPosition = (int) $result[$this->getPositionIdentifier()];

        if ($way === 0) {
            if ($currentPosition === $position) {
                return true;
            }

            $sql = '
                UPDATE `' . _DB_PREFIX_ . $this->getTable() . '` l
                SET ' . $this->getPositionIdentifier() . ' = ' . $this->getPositionIdentifier() . ' + 1
                WHERE ' . $this->getPositionIdentifier() . ' >= ' . (int) $position . ' 
                AND ' . $this->getPositionIdentifier() . ' < ' . (int) $currentPosition . ' 
                AND ' . $this->getIdentifier() . ' != ' . (int) $objectId;

            return (bool) \Db::getInstance()->execute($sql) && \Db::getInstance()->execute(
                '
                UPDATE `' . _DB_PREFIX_ . $this->getTable() . '`
                SET ' . $this->getPositionIdentifier() . ' = ' . (int) $position . '
                WHERE ' . $this->getIdentifier() . ' = ' . (int) $objectId
            );
        }

        $sql = '
            UPDATE `' . _DB_PREFIX_ . $this->getTable() . '`
            SET ' . $this->getPositionIdentifier() . ' = ' . $this->getPositionIdentifier() . ' - 1 
            WHERE ' . $this->getPositionIdentifier() . ' > ' . (int) $currentPosition . '
            AND ' . $this->getPositionIdentifier() . ' <= ' . (int) $position . '
            AND ' . $this->getIdentifier() . ' != ' . (int) $objectId;

        return (bool) \Db::getInstance()->execute($sql) && \Db::getInstance()->execute(
            '
                UPDATE `' . _DB_PREFIX_ . $this->getTable() . '`
                SET ' . $this->getPositionIdentifier() . ' = ' . (int) $position . '
                WHERE ' . $this->getIdentifier() . ' = ' . (int) $objectId
        );
    }

    private function getObjectModelClassNameInSnakeCase(): string
    {
        $reflection = new \ReflectionClass($this->getObjectModelClassName());

        $className = $reflection->getShortName();

        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $className));
    }

    protected function getImagePreviewHtml(string $type, string $extension, string $widthInPx = '200px'): string
    {
        if(!\Validate::isLoadedObject($this->object)) {
            return '';
        }

        return $this->objectModelImageManager->getThumbnailHtml((int) $this->object->id, $type, $extension, $widthInPx);
    }

    protected function getDeleteImageUrl(string $type): string|false
    {
        if(!\Validate::isLoadedObject($this->object)) {
            return '';
        }

        return $this->context->link->getAdminLink(\Tools::getValue('controller')) . '&' . $this->identifier . '=' . $this->object->id . '&deleteObjectImage=1&type=' . $type;
    }

    protected function processImagesDelete(): void
    {
        if (!\Tools::isSubmit('deleteObjectImage')) {
            return;
        }

        $type = \Tools::getValue('type');

        $object = $this->loadObject();

        if (!$object) {
            return;
        }

        $id = (int) $object->id;

        $this->objectModelImageManager->deleteImage($id, $type);

        // Redirect back to the edit page after deletion
        \Tools::redirectAdmin($this->context->link->getAdminLink(\Tools::getValue('controller')) . '&' . $this->identifier . '=' . $id . '&update' . $this->table);
    }
}
