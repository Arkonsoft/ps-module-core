<?php

class Module
{
    public function __construct()
    {
    }
}

class ModuleAdminController
{
    public function __construct()
    {
    }

    protected function initContent()
    {
    }

    protected function postProcess()
    {
    }

    protected function processAdd()
    {
    }

    protected function processDelete()
    {
    }

    protected function processSave()
    {
    }
}

class Context
{
    /** @return self */
    public static function getContext()
    {
        return new self();
    }
}

class Shop
{
    public const CONTEXT_SHOP = 1;

    /** @return bool */
    public static function isFeatureActive()
    {
        return false;
    }

    /** @return int */
    public static function getContext()
    {
        return 0;
    }

    /** @return self */
    public static function getShop()
    {
        return new self();
    }

    /** @return array */
    public static function getShops()
    {
        return [];
    }
}

class Tools
{
    /** @return array */
    public static function getAllValues()
    {
        return [];
    }

    /** @return mixed */
    public static function getValue()
    {
        return null;
    }

    /** @return bool */
    public static function isSubmit()
    {
        return false;
    }

    /** @return void */
    public static function redirectAdmin()
    {
    }
}

class DbQuery
{
}

class Db
{
    /** @return self */
    public static function getInstance()
    {
        return new self();
    }
}

class Validate
{
    /** @return bool */
    public static function isLoadedObject()
    {
        return false;
    }
}

class Configuration
{
    /** @return void */
    public static function loadConfiguration()
    {
    }

    /** @return mixed */
    public static function get()
    {
        return null;
    }

    /** @return bool */
    public static function updateValue()
    {
        return false;
    }
}

class HelperForm
{
}

class Language
{
    /** @return array */
    public static function getLanguages()
    {
        return [];
    }
}

class ImageManager
{
    /** @return bool */
    public static function resize()
    {
        return false;
    }
}

class Tab
{
    public $id_parent;

    public $name;

    public $class_name;

    public $module;

    public $active;

    public function __construct($id = null)
    {
    }

    /** @return bool */
    public function add()
    {
        return true;
    }

    /** @return bool */
    public function delete()
    {
        return true;
    }
}

/** @return string */
function pSQL()
{
    return '';
}
