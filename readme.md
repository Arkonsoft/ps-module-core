# PS Module Core

## Introduction

Creating modules for Presta is a real horror story. This package contains basic tools to make development a little less masochistic.

## Requirements

- PHP >= 7.0

## Installation

Install package with composer:
```console
composer require arkonsoft/ps-module-core
```

## Basic usage

### AbstractModule

Inherit the AbstractModule class from your module class. Now you have access to the $this->canBeUpgraded() method, which can be used to display a warning about module updates or anything else. 

You can also use the ModuleCategory dictionary to set $this->tab, which contains the module category (it has meaning on the module list page).

```php
<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

use Arkonsoft\PsModule\Core\Module\AbstractModule;

class MyModule extends AbstractModule
{
    public function __construct()
    {
        $this->name = 'mymodule';

        // ps-module-core
        $this->tab = ModuleCategory::FRONT_OFFICE_FEATURES;
        $this->version = '1.0.0';
        $this->author = 'Firstname Lastname';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.7.0.0',
            'max' => '8.99.99',
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->trans('My module', [], 'Modules.Mymodule.Admin');
        $this->description = $this->trans('Description of my module.', [], 'Modules.Mymodule.Admin');

        $this->confirmUninstall = $this->trans('Are you sure you want to uninstall?', [], 'Modules.Mymodule.Admin');

        // ps-module-core
        if ($this->canBeUpgraded()) {
            $this->warning = $this->trans('The module %s needs to be updated. Use the "update" option in the list of modules. The module without an update may not work properly.', [ $this->name ], 'Modules.Mymodule.Admin');
        }
    }
}
```

### AbstractAdminSettingsController

Inherit from the AbstractAdminSettingsController class on your module AdminController class. Now all you need to do is define the fields of the settings form in HelperForm format and you're done! 

Data writing and reading operations happen in the abstract class so you can focus only on defining the fields.

```php
<?php

use Arkonsoft\PsModule\Core\Controller\AbstractAdminSettingsController;

if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminMyModuleSettingsController extends AbstractAdminSettingsController
{
    public function prepareOptions(): void
    {
        $form = [
            'form' => [
                'tabs' => [
                    'general' => $this->module->getTranslator()->trans('General', [], 'Modules.MyModule.Admin'),
                    'other1' => $this->module->getTranslator()->trans('Other 1', [], 'Modules.MyModule.Admin'),
                    'other2' => $this->module->getTranslator()->trans('Other 2', [], 'Modules.MyModule.Admin'),
                ],
                'legend' => [
                    'title' => $this->module->getTranslator()->trans('Settings', [], 'Modules.MyModule.Admin'),
                    'icon' => 'icon-cogs'
                ],
                'submit' => [
                    'title' => $this->module->getTranslator()->trans('Save', [], 'Modules.MyModule.Admin'),
                    'class' => 'btn btn-default pull-right'
                ],
            ],
        ];

        $form['form']['input'][] = [
            'label' => $this->module->getTranslator()->trans('Example field 1', [], 'Modules.MyModule.Admin'),
            'type' => 'text',
            'name' => $this->module->name . 'example_field_1',
            'lang' => true,
            'tab' => 'general'
        ];

        $form['form']['input'][] = [
            'label' => $this->module->getTranslator()->trans('Example field 2', [], 'Modules.MyModule.Admin'),
            'type' => 'text',
            'name' => $this->module->name . 'example_field_2',
            'lang' => true,
            'tab' => 'general'
        ];

        $this->forms[] = $form;
    }
}

```

--- 

## Definitions

### AbstractModule Class

The `AbstractModule` class extends the PrestaShop `Module` class and provides common functionalities for PrestaShop modules.

#### getDatabaseVersion Method

```php
/**
 * Returns the database version of the module
 * 
 * @return ?string
 */
protected function getDatabaseVersion()
```

This method retrieves the database version of the module from the PrestaShop database.

#### canBeUpgraded Method

```php
/**
 * Checks if the database version is lower than the specified version for potential upgrade.
 * 
 * @return bool
 */
protected function canBeUpgraded(): bool
```

This method checks if the database version of the module is lower than the specified version, indicating a potential need for upgrade.

Example usage:
```php
public function __construct()
    {
        // other code

        if ($this->canBeUpgraded()) {
            $this->warning = $this->trans('The module %s needs to be updated. Use the "update" option in the list of modules. The module without an update may not work properly.', [ $this->name ], 'Modules.Mymodule.Admin');
        }
    }
```

---

### ModuleCategory Interface

The `ModuleCategory` interface defines constants representing different categories of PrestaShop modules.

#### Constants

- `ADMINISTRATION`: Modules related to administration.
- `ADVERTISING_MARKETING`: Modules related to advertising and marketing.
- `ANALYTICS_STATS`: Modules for analytics and statistics.
- `BILLING_INVOICING`: Modules for billing and invoicing.
- `CHECKOUT`: Modules related to the checkout process.
- `CONTENT_MANAGEMENT`: Modules for content management.
- `DASHBOARD`: Modules for dashboard functionalities.
- `EMAILING`: Modules related to emailing.
- `EXPORT`: Modules for exporting data.
- `FRONT_OFFICE_FEATURES`: Modules providing features for the front office.
- `I18N_LOCALIZATION`: Modules for internationalization and localization.
- `MARKET_PLACE`: Modules related to marketplaces.
- `MERCHANDIZING`: Modules for merchandizing.
- `MIGRATION_TOOLS`: Modules for migration tools.
- `MOBILE`: Modules optimized for mobile devices.
- `OTHERS`: Other miscellaneous modules.
- `PAYMENTS_GATEWAYS`: Modules for payments and gateways.
- `PAYMENT_SECURITY`: Modules for payment security.
- `PRICING_PROMOTION`: Modules for pricing and promotions.
- `QUICK_BULK_UPDATE`: Modules for quick bulk updates.
- `SEARCH_FILTER`: Modules for search and filtering.
- `SEO`: Modules for search engine optimization.
- `SHIPPING_LOGISTICS`: Modules for shipping and logistics.
- `SLIDESHOWS`: Modules for creating slideshows.
- `SMART_SHOPPING`: Modules for smart shopping.
- `SOCIAL_NETWORKS`: Modules integrating with social networks.

Example usage:
```php
public function __construct()
    {
        // other code

        $this->tab = ModuleCategory::FRONT_OFFICE_FEATURES;
    }
```

---

### AbstractAdminSettingsController Class

The `AbstractAdminSettingsController` class extends the PrestaShop `ModuleAdminController` class and provides functionalities for managing module settings in the admin panel better than native OptionsAPI.

#### Constructor

```php
public function __construct()
```

The constructor method initializes the `AbstractAdminSettingsController` class by setting up the context, bootstrapping, and calling the `prepareOptions` method.

#### prepareOptions Method

```php
abstract public function prepareOptions();
```

This abstract method is used to prepare options for the settings controller. Subclasses must implement this method.

#### initContent Method

```php
public function initContent()
```

This method initializes the content of the settings controller by rendering the form.

#### postProcess Method

```php
public function postProcess()
```

This method handles post-processing tasks after form submission, such as saving form data.

#### dispatchAction Method

```php
public function dispatchAction()
```

This method dispatches actions based on form submissions.

#### renderForm Method

```php
public function renderForm()
```

This method renders the form using the `HelperForm` class.

#### loadFormValues Method

```php
public function loadFormValues(HelperForm $helper)
```

This method loads form values from the database.

#### saveForm Method

```php
public function saveForm()
```

This method saves form data to the database.

#### Other Helper Methods

- `loadField`: Loads a field value from the database.
- `loadCategoryField`: Loads values for a category field from the database.
- `saveField`: Saves a field value to the database.
- `saveLangField`: Saves a multilingual field value to the database.
- `saveCategoryField`: Saves values for a category field to the database.
- `isHTMLAllowed`: Checks if HTML is allowed for a field type.
- `isNestedArray`: Checks if an array is nested.

---


