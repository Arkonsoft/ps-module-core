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

namespace Arkonsoft\PsModule\Core\Module;

if(!defined('_PS_VERSION_')) {
    exit;
}

interface ModuleCategory {
    const ADMINISTRATION = 'administration';
    const ADVERTISING_MARKETING = 'advertising_marketing';
    const ANALYTICS_STATS = 'analytics_stats';
    const BILLING_INVOICING = 'billing_invoicing';
    const CHECKOUT = 'checkout';
    const CONTENT_MANAGEMENT = 'content_management';
    const DASHBOARD = 'dashboard';
    const EMAILING = 'emailing';
    const EXPORT = 'export';
    const FRONT_OFFICE_FEATURES = 'front_office_features';
    const I18N_LOCALIZATION = 'i18n_localization';
    const MARKET_PLACE = 'market_place';
    const MERCHANDIZING = 'merchandizing';
    const MIGRATION_TOOLS = 'migration_tools';
    const MOBILE = 'mobile';
    const OTHERS = 'others';
    const PAYMENTS_GATEWAYS = 'payments_gateways';
    const PAYMENT_SECURITY = 'payment_security';
    const PRICING_PROMOTION = 'pricing_promotion';
    const QUICK_BULK_UPDATE = 'quick_bulk_update';
    const SEARCH_FILTER = 'search_filter';
    const SEO = 'seo';
    const SHIPPING_LOGISTICS = 'shipping_logistics';
    const SLIDESHOWS = 'slideshows';
    const SMART_SHOPPING = 'smart_shopping';
    const SOCIAL_NETWORKS = 'social_networks';
}
