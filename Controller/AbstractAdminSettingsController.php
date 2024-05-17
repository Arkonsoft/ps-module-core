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
 * @copyright 2023 Arkonsoft
 */

declare(strict_types=1);

namespace Arkonsoft\PsModule\Core\Controller;

use Configuration;
use Context;
use HelperForm;
use Language;
use ModuleAdminController;
use Tools;

if (!defined('_PS_VERSION_')) {
    exit;
}

abstract class AbstractAdminSettingsController extends ModuleAdminController
{

    protected $forms = [];

    abstract public function prepareOptions();

    public function __construct()
    {
        $this->context = Context::getContext();
        $this->context->controller = $this;
        $this->bootstrap = true;

        parent::__construct();

        $this->prepareOptions();
    }

    public function initContent()
    {
        $this->content .= $this->renderForm();

        parent::initContent();
    }

    public function postProcess()
    {
        parent::postProcess();

        $this->dispatchAction();
    }

    public function dispatchAction()
    {
        if (Tools::isSubmit('submitOptions')) {
            $this->saveForm();

            return;
        }
    }

    public function renderForm()
    {
        $helper = new HelperForm();
        $helper->default_form_language = $this->context->language->id;
        $helper->submit_action = 'submitOptions';
        $helper->languages = $this->context->controller->getLanguages();
        $helper = $this->loadFormValues($helper);

        return $helper->generateForm($this->forms);
    }

    public function loadFormValues(HelperForm $helper)
    {
        foreach ($this->forms as &$form) {
            if (empty($form['form']['input'])) {
                continue;
            }

            foreach ($form['form']['input'] as &$field) {
                if (empty($field['name'])) {
                    continue;
                }

                $key = $field['name'];

                if ($field['type'] == 'categories') {
                    $field['tree']['selected_categories'] = $this->loadCategoryField($key);
                    continue;
                }

                if (!empty($field['lang'])) {
                    foreach (Language::getLanguages(true, false, true) as $id_lang) {
                        $helper->fields_value[$key][$id_lang] = $this->loadField((string) $key, (int) $id_lang, !empty($field['multiple']));
                    }
                } else {
                    $helper->fields_value[$key] = $this->loadField((string) $key, 0, !empty($field['multiple']));
                }
            }
        }

        return $helper;
    }

    public function saveForm()
    {
        $forms = $this->forms;

        foreach ($forms as $form) {
            if (empty($form['form']['input'])) {
                continue;
            }

            foreach ($form['form']['input'] as $field) {
                if (empty($field['name']) || empty($field['type'])) {
                    continue;
                }

                $key = $field['name'];

                if ($field['type'] == 'categories') {
                    $this->saveCategoryField($key);
                    continue;
                }

                if (empty($field['lang'])) {
                    $this->saveField(
                        $key,
                        $this->isHTMLAllowed($field['type']),
                        !empty($field['multiple'])
                    );
                    continue;
                }

                $this->saveLangField(
                    $key,
                    $this->isHTMLAllowed($field['type']),
                    !empty($field['multiple'])
                );
            }
        }
    }

    protected function loadField(string $name, int $id_lang = 0, bool $multiple = false)
    {
        if (empty($name)) {
            return false;
        }

        if (empty($id_lang)) {
            $id_lang = null;
        }

        if ($multiple) {
            $name = str_replace('[]', '', $name);
        }

        $value = Configuration::get($name, $id_lang);

        if ($multiple) {
            if (empty($value)) {
                return [];
            }

            $value = explode(',', $value);
        }

        return $value;
    }

    protected function loadCategoryField(string $name)
    {
        if (empty($name)) {
            return false;
        }

        $values = Configuration::get($name, null, null, null, '');

        $values = explode(',', $values);

        return $values;
    }

    protected function saveField(string $name, bool $html = false, bool $multiple = false)
    {
        if (empty($name)) {
            return false;
        }

        if ($multiple) {
            $name = str_replace('[]', '', $name);
        }

        $value = Tools::getValue($name);

        if ($multiple) {
            $name = str_replace('[]', '', $name);

            if (is_array($value)) {
                $value = implode(',', $value);
            }
        }

        return Configuration::updateValue($name, $value, $html);
    }

    protected function saveLangField(string $name, bool $html = false, bool $multiple = false)
    {
        if (empty($name)) {
            return false;
        }

        if ($multiple) {
            $name = str_replace('[]', '', $name);
        }

        $languages = Language::getLanguages(true, false, true);

        $values = [];

        foreach ($languages as $id_lang) {
            $value = Tools::getValue($name . '_' . $id_lang);

            if ($multiple && is_array($value)) {
                $value = implode(',', $value);
            }

            $values[$id_lang] = $value;
        }

        return Configuration::updateValue($name, $values, $html);
    }

    public function saveCategoryField(string $name)
    {
        if (empty($name)) {
            return false;
        }

        $values = Tools::getValue($name);

        if (!is_array($values)) {
            $values = [];
        }

        if (!empty($values) && $this->isNestedArray($values)) {
            $values = array_merge(...$values);
        }

        $values = implode(',', $values);

        return Configuration::updateValue($name, $values);
    }


    protected function isHTMLAllowed(string $fieldType): bool
    {
        $allowedTypes = ['textarea'];

        return in_array($fieldType, $allowedTypes);
    }

    public function isNestedArray(array $array): bool
    {
        $nestedArrays = array_filter($array, 'is_array');

        return count($nestedArrays) == count($array);
    }
}
