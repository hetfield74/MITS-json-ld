<?php
/**
 * --------------------------------------------------------------
 * File: mits_json_ld.php
 * Date: 18.03.2019
 * Time: 15:28
 *
 * Author: Hetfield
 * Copyright: (c) 2019 - MerZ IT-SerVice
 * Web: https://www.merz-it-service.de
 * Contact: info@merz-it-service.de
 * --------------------------------------------------------------
 */

$modulname = strtoupper("mits_json_ld");

$lang_array = array(
  'MODULE_' . $modulname . '_TITLE'        => 'MITS JSON-LD for modified eCommerce Shopsoftware <span style="white-space:nowrap;">© by <span style="padding:2px;background:#ffe;color:#6a9;font-weight:bold;">Hetfield (MerZ IT-SerVice)</span></span>',
  'MODULE_' . $modulname . '_DESCRIPTION'  => '
    <a href="https://www.merz-it-service.de/" target="_blank">
      <img src="' . (ENABLE_SSL === true ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG . DIR_WS_IMAGES . 'merz-it-service.png" border="0" alt="MerZ IT-SerVice" style="display:block;max-width:100%;height:auto;" />
    </a><br />
    <p style="font-size: larger">This module extends your modified eCommerce shop with the JSON-LD markups recommended by Google.</p>
    <p>This module supports markups for the following types:</p>
    <ul style="font-size: larger">
      <li>WebSite <small>name, alternateName, description, url and logo</small></li>
      <li>Organization <small>name, alternateName, description, url, logo and ContactPoints for customer service, technical support, billing support, sales</small></li>
      <li>LocalBusiness <small>name, image, url, telephone, address, geo, sameAs</small></li>
      <li>ContactPage <small>name, url, description</small></li>
      <li>Breadcrumb</li>
      <li>Product <small>name, image, description, brand, priceCurrency, priceValidUntil (fixed 1 month), price, url, itemCondition, availability, mpn, sku, gtin13 and reviews</small></li>
      <li>Review (Rating and aggregateRating) <small>ratingValue, worstRating, bestRating, author, datePublished, reviewBody</small></li>
      <li>Sitelink Searchbox</li>
    </ul>
    <p style="font-size: larger">This module can, of course, be expanded and adapted as required. For individual customization requests, please contact us directly.<br />
    <div style="text-align:center;">
      <small>The latest version of the module is always available on Github!</small><br />
      <a style="background:#6a9;color:#444" target="_blank" href="https://github.com/hetfield74/MITS-json-ld" class="button" onclick="this.blur();">MITS JSON-LD on Github</a>
    </div>
    <p>If you have questions, problems or requests for this module or any other concerns regarding modified eCommerce shopsoftware, simply contact us:</p> 
    <div style="text-align:center;"><a style="background:#6a9;color:#444" target="_blank" href="https://www.merz-it-service.de/Kontakt.html" class="button" onclick="this.blur();">Contact page on MerZ-IT-SerVice.de</strong></a></div>  
',
  'MODULE_' . $modulname . '_STATUS_TITLE' => 'Activate module?',
  'MODULE_' . $modulname . '_STATUS_DESC'  => 'Activate the MITS JSON-LD module in the shop?',

  'MODULE_' . $modulname . '_SHOW_BREADCRUMB_TITLE' => 'Activate breadcrumb?',
  'MODULE_' . $modulname . '_SHOW_BREADCRUMB_DESC'  => 'Activate JSON-LD markup for the breadcrumb?',

  'MODULE_' . $modulname . '_SHOW_PRODUCT_TITLE' => 'Activate products?',
  'MODULE_' . $modulname . '_SHOW_PRODUCT_DESC'  => 'Activate JSON-LD markup for products on the product detail page?',

  'MODULE_' . $modulname . '_ENABLE_ATTRIBUTES_TITLE' => 'Output product attributes in JSON-LD',
  'MODULE_' . $modulname . '_ENABLE_ATTRIBUTES_DESC'  => 'Should product attributes (variants) be output as offers?',

  'MODULE_' . $modulname . '_ENABLE_TAGS_TITLE' => 'Output product properties in JSON-LD',
  'MODULE_' . $modulname . '_ENABLE_TAGS_DESC'  => 'Should product properties (product tags) be output in additionalProperty?',

  'MODULE_' . $modulname . '_MAX_OFFERS_TITLE' => 'Max. number of offers',
  'MODULE_' . $modulname . '_MAX_OFFERS_DESC'  => 'Prevents memory issues with many attributes. Default: 100.',

  'MODULE_' . $modulname . '_SHOW_PRODUCT_REVIEWS_TITLE' => 'Activate reviews for products?',
  'MODULE_' . $modulname . '_SHOW_PRODUCT_REVIEWS_DESC'  => 'Activate JSON-LD markup for reviews on the product detail page? Only effective when product markup is activated.',

  'MODULE_' . $modulname . '_SHOW_PRODUCT_REVIEWS_INFO_TITLE' => 'Activate review detail page?',
  'MODULE_' . $modulname . '_SHOW_PRODUCT_REVIEWS_INFO_DESC'  => 'Activate JSON-LD markup for the detail page of a review?',

  'MODULE_' . $modulname . '_SHOW_SEARCHFIELD_TITLE' => 'Activate Sitelinks Searchbox?',
  'MODULE_' . $modulname . '_SHOW_SEARCHFIELD_DESC'  => 'Activate JSON-LD markup for the Sitelinks Searchbox?',

  'MODULE_' . $modulname . '_SHOW_WEBSITE_TITLE' => 'Activate WebSite?',
  'MODULE_' . $modulname . '_SHOW_WEBSITE_DESC'  => 'Activate JSON-LD markup for WebSite?',

  'MODULE_' . $modulname . '_SHOW_LOGO_TITLE' => 'Activate logo?',
  'MODULE_' . $modulname . '_SHOW_LOGO_DESC'  => 'The logo is used in the markups for WebSite, Organization and LocalBusiness.',

  'MODULE_' . $modulname . '_LOGOFILE_TITLE' => 'Logo',
  'MODULE_' . $modulname . '_LOGOFILE_DESC'  => 'Your company logo, filename without path. File must be in the template\'s img folder (default: logo.gif).',

  'MODULE_' . $modulname . '_SHOW_ORGANISTATION_TITLE' => 'Activate Organization?',
  'MODULE_' . $modulname . '_SHOW_ORGANISTATION_DESC'  => 'Should JSON-LD markup for Organization be activated?',

  'MODULE_' . $modulname . '_SHOW_CONTACT_TITLE' => 'Activate ContactPage?',
  'MODULE_' . $modulname . '_SHOW_CONTACT_DESC'  => 'Activate JSON-LD markup for ContactPage?',

  'MODULE_' . $modulname . '_SHOW_LOCATION_TITLE' => 'Activate LocalBusiness?',
  'MODULE_' . $modulname . '_SHOW_LOCATION_DESC'  => 'Activate JSON-LD markup for LocalBusiness?',

  'MODULE_' . $modulname . '_NAME_TITLE' => 'Name of company/website',
  'MODULE_' . $modulname . '_NAME_DESC'  => 'The name is used in WebSite, Organization and LocalBusiness markups.',

  'MODULE_' . $modulname . '_ALTERNATE_NAME_TITLE' => 'Alternative name of company/website',
  'MODULE_' . $modulname . '_ALTERNATE_NAME_DESC'  => 'The alternative name is used in WebSite, Organization and LocalBusiness markups.',

  'MODULE_' . $modulname . '_DESCRIPTION_TITLE' => 'Description of company/website',
  'MODULE_' . $modulname . '_DESCRIPTION_DESC'  => 'The description is used in WebSite, Organization and LocalBusiness markups. If empty, the standard meta description is used.',

  'MODULE_' . $modulname . '_EMAIL_TITLE' => 'Company/website email address',
  'MODULE_' . $modulname . '_EMAIL_DESC'  => 'This email address is used in WebSite, Organization and LocalBusiness markups.',

  'MODULE_' . $modulname . '_TELEPHONE_DEFAULT_TITLE' => 'Phone number',
  'MODULE_' . $modulname . '_TELEPHONE_DEFAULT_DESC'  => 'This number is used as the main phone number for Organization and LocalBusiness. Format: +49-2722-631363',

  'MODULE_' . $modulname . '_TELEPHONE_SERVICE_TITLE' => 'Customer service phone number',
  'MODULE_' . $modulname . '_TELEPHONE_SERVICE_DESC'  => 'Phone number for customer service. Format: +49-2722-631363',

  'MODULE_' . $modulname . '_TELEPHONE_TECHNICAL_TITLE' => 'Technical support phone number',
  'MODULE_' . $modulname . '_TELEPHONE_TECHNICAL_DESC'  => 'Phone number for technical support. Format: +49-2722-631363',

  'MODULE_' . $modulname . '_TELEPHONE_BILLING_TITLE' => 'Billing phone number',
  'MODULE_' . $modulname . '_TELEPHONE_BILLING_DESC'  => 'Phone number for billing inquiries. Format: +49-2722-631363',

  'MODULE_' . $modulname . '_TELEPHONE_SALES_TITLE' => 'Sales phone number',
  'MODULE_' . $modulname . '_TELEPHONE_SALES_DESC'  => 'Phone number for sales. Format: +49-2722-631363',

  'MODULE_' . $modulname . '_FAX_TITLE' => 'Fax number',
  'MODULE_' . $modulname . '_FAX_DESC'  => 'Company fax number. Format: +49-2722-631400',

  'MODULE_' . $modulname . '_SOCIAL_MEDIA_TITLE' => 'Social media profiles',
  'MODULE_' . $modulname . '_SOCIAL_MEDIA_DESC'  => 'Enter the full URLs to your social media profiles. Separate multiple URLs with commas.',

  'MODULE_' . $modulname . '_LOCATION_STREETADDRESS_TITLE' => 'Street/house number',
  'MODULE_' . $modulname . '_LOCATION_STREETADDRESS_DESC'  => 'Street and house number for LocalBusiness markup.',

  'MODULE_' . $modulname . '_LOCATION_ADDRESSLOCALITY_TITLE' => 'City',
  'MODULE_' . $modulname . '_LOCATION_ADDRESSLOCALITY_DESC'  => 'City for LocalBusiness markup.',

  'MODULE_' . $modulname . '_LOCATION_POSTALCODE_TITLE' => 'Postal code',
  'MODULE_' . $modulname . '_LOCATION_POSTALCODE_DESC'  => 'Postal code for LocalBusiness markup.',

  'MODULE_' . $modulname . '_LOCATION_ADDRESSCOUNTRY_TITLE' => 'Country',
  'MODULE_' . $modulname . '_LOCATION_ADDRESSCOUNTRY_DESC'  => 'Country as ISO-2 code (e.g. DE).',

  'MODULE_' . $modulname . '_LOCATION_GEO_LATITUDE_TITLE' => 'GEO latitude',
  'MODULE_' . $modulname . '_LOCATION_GEO_LATITUDE_DESC'  => 'Optional GEO latitude. Leave empty for no value.',

  'MODULE_' . $modulname . '_LOCATION_GEO_LONGITUDE_TITLE' => 'GEO longitude',
  'MODULE_' . $modulname . '_LOCATION_GEO_LONGITUDE_DESC'  => 'Optional GEO longitude. Leave empty for no value.',

  'MODULE_' . $modulname . '_UPDATE_AVAILABLE_TITLE' => '<span style="font-weight:bold;color:#900;background:#ff6;padding:2px;border:1px solid #900;">Please update the module!</span>',
  'MODULE_' . $modulname . '_UPDATE_AVAILABLE_DESC'  => '',
  'MODULE_' . $modulname . '_UPDATE_FINISHED'        => 'The MITS JSON-LD module has been updated.',
  'MODULE_' . $modulname . '_UPDATE_ERROR'           => 'Error',
  'MODULE_' . $modulname . '_UPDATE_MODUL'           => 'Update module',
  'MODULE_' . $modulname . '_DELETE_MODUL'           => 'Remove MITS JSON-LD completely from server',
  'MODULE_' . $modulname . '_CONFIRM_DELETE_MODUL'   => 'Do you really want to delete the MITS JSON-LD module and all files from the server?',
  'MODULE_' . $modulname . '_DELETE_FINISHED'        => 'The MITS JSON-LD module has been removed from the server.',
);

foreach ($lang_array as $key => $val) {
    defined($key) || define($key, $val);
}