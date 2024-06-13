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

declare(strict_types=1);

namespace Arkonsoft\PsModule\Core\Tab;

if(!defined('_PS_VERSION_')) {
    exit;
}

interface TabDictionary
{
    const ACCESS = 'AdminAccess';
    const ADDONS_CATALOG = 'AdminAddonsCatalog';
    const ADDRESSES = 'AdminAddresses';
    const ADVANCED_PARAMETERS = 'AdminAdvancedParameters';
    const ATTACHMENTS = 'AdminAttachments';
    const ATTRIBUTES_GROUPS = 'AdminAttributesGroups';
    const BACKUP = 'AdminBackup';
    const CARRIERS = 'AdminCarriers';
    const CART_RULES = 'AdminCartRules';
    const CARTS = 'AdminCarts';
    const CATALOG = 'AdminCatalog';
    const CATEGORIES = 'AdminCategories';
    const CMS_CONTENT = 'AdminCmsContent';
    const CONFIGURE = 'CONFIGURE';
    const CONTACTS = 'AdminContacts';
    const COUNTRIES = 'AdminCountries';
    const CURRENCIES = 'AdminCurrencies';
    const CUSTOMER_PREFERENCES = 'AdminCustomerPreferences';
    const CUSTOMER_THREADS = 'AdminCustomerThreads';
    const CUSTOMERS = 'AdminCustomers';
    const DASHBOARD = 'AdminDashboard';
    const DEFAULT = 'DEFAULT';
    const DELIVERY_SLIP = 'AdminDeliverySlip';
    const EMAILS = 'AdminEmails';
    const EMPLOYEES = 'AdminEmployees';
    const FEATURES = 'AdminFeatures';
    const GENDERS = 'AdminGenders';
    const GEOLOCATION = 'AdminGeolocation';
    const GROUPS = 'AdminGroups';
    const IMAGES = 'AdminImages';
    const IMPORT = 'AdminImport';
    const IMPROVE = 'IMPROVE';
    const INFORMATION = 'AdminInformation';
    const INTERNATIONAL = 'AdminInternational';
    const INVOICES = 'AdminInvoices';
    const LANGUAGES = 'AdminLanguages';
    const LOCALIZATION = 'AdminLocalization';
    const LOGS = 'AdminLogs';
    const MAINTENANCE = 'AdminMaintenance';
    const MANUFACTURERS = 'AdminManufacturers';
    const META = 'AdminMeta';
    const MODULES = 'AdminModules';
    const MODULES_POSITIONS = 'AdminModulesPositions';
    const MODULES_SF = 'AdminModulesSf';
    const ORDER_MESSAGE = 'AdminOrderMessage';
    const ORDER_PREFERENCES = 'AdminOrderPreferences';
    const ORDERS = 'AdminOrders';
    const OUTSTANDING = 'AdminOutstanding';
    const PARENT_ATTRIBUTES_GROUPS = 'AdminParentAttributesGroups';
    const PARENT_CART_RULES = 'AdminParentCartRules';
    const PARENT_COUNTRIES = 'AdminParentCountries';
    const PARENT_CUSTOMER = 'AdminParentCustomer';
    const PARENT_CUSTOMER_PREFERENCES = 'AdminParentCustomerPreferences';
    const PARENT_CUSTOMER_THREADS = 'AdminParentCustomerThreads';
    const PARENT_EMPLOYEES = 'AdminParentEmployees';
    const PARENT_LOCALIZATION = 'AdminParentLocalization';
    const PARENT_MANUFACTURERS = 'AdminParentManufacturers';
    const PARENT_META = 'AdminParentMeta';
    const PARENT_MODULES_SF = 'AdminParentModulesSf';
    const PARENT_ORDER_PREFERENCES = 'AdminParentOrderPreferences';
    const PARENT_ORDERS = 'AdminParentOrders';
    const PARENT_PAYMENT = 'AdminParentPayment';
    const PARENT_PREFERENCES = 'AdminParentPreferences';
    const PARENT_REQUEST_SQL = 'AdminParentRequestSql';
    const PARENT_SEARCH_CONF = 'AdminParentSearchConf';
    const PARENT_SHIPPING = 'AdminParentShipping';
    const PARENT_STOCK_MANAGEMENT = 'AdminParentStockManagement';
    const PARENT_STORES = 'AdminParentStores';
    const PARENT_TAXES = 'AdminParentTaxes';
    const PARENT_THEMES = 'AdminParentThemes';
    const PAYMENT = 'AdminPayment';
    const PAYMENT_PREFERENCES = 'AdminPaymentPreferences';
    const PERFORMANCE = 'AdminPerformance';
    const PREFERENCES = 'AdminPreferences';
    const PRODUCTS = 'AdminProducts';
    const PROFILES = 'AdminProfiles';
    const QUICK_ACCESSES = 'AdminQuickAccesses';
    const REFERRERS = 'AdminReferrers';
    const REQUEST_SQL = 'AdminRequestSql';
    const RETURN = 'AdminReturn';
    const SEARCH_CONF = 'AdminSearchConf';
    const SEARCH_ENGINES = 'AdminSearchEngines';
    const SELL = 'SELL';
    const SHIPPING = 'AdminShipping';
    const SHOP_GROUP = 'AdminShopGroup';
    const SHOP_PARAMETERS = 'ShopParameters';
    const SHOP_URL = 'AdminShopUrl';
    const SLIP = 'AdminSlip';
    const SPECIFIC_PRICE_RULE = 'AdminSpecificPriceRule';
    const STATES = 'AdminStates';
    const STATS = 'AdminStats';
    const STATUSES = 'AdminStatuses';
    const STOCK = 'AdminStock';
    const STOCK_CONFIGURATION = 'AdminStockConfiguration';
    const STOCK_COVER = 'AdminStockCover';
    const STOCK_INSTANT_STATE = 'AdminStockInstantState';
    const STOCK_MANAGEMENT = 'AdminStockManagement';
    const STOCK_MVT = 'AdminStockMvt';
    const STORES = 'AdminStores';
    const SUPPLIERS = 'AdminSuppliers';
    const SUPPLY_ORDERS = 'AdminSupplyOrders';
    const TAGS = 'AdminTags';
    const TAX_RULES_GROUP = 'AdminTaxRulesGroup';
    const TAXES = 'AdminTaxes';
    const THEMES = 'AdminThemes';
    const THEMES_CATALOG = 'AdminThemesCatalog';
    const TRACKING = 'AdminTracking';
    const TRANSLATIONS = 'AdminTranslations';
    const WAREHOUSES = 'AdminWarehouses';
    const WEBSERVICE = 'AdminWebservice';
    const ZONES = 'AdminZones';
}
