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

  'MODULE_' . $modulname . '_ENABLE_MICRODATA_FIX_TITLE' => 'Activate Microdata Fix?',
  'MODULE_' . $modulname . '_ENABLE_MICRODATA_FIX_DESC'  => '<i>Removes the Microdata attributes from the shop using jQuery, in case the Microdata schema is still present in the template used. Duplicate structured data (JSON-LD and Microdata) is not ideal as it can lead to inconsistencies.',

  'MODULE_' . $modulname . '_ENABLE_CUSTOM_JSON_TITLE' => 'Automatically detect & integrate custom JSON-LD from texts?',
  'MODULE_' . $modulname . '_ENABLE_CUSTOM_JSON_DESC'  => 'If activated, the module searches product and content pages for embedded &lt;script type="application/ld+json"&gt;&mldr;&lt;/script&gt; blocks, removes them from the text, and correctly integrates them into the module\'s central JSON-LD.<br><br><strong>Note:</strong> This function is only necessary if structured data is embedded in the editor. With very large texts or high-traffic shops, it can cause slightly increased server load.',

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

  'MODULE_' . $modulname . '_WEBSITE_DESCRIPTION_TITLE' => 'Description of company/website',
  'MODULE_' . $modulname . '_WEBSITE_DESCRIPTION_DESC'  => 'The description is used in WebSite, Organization and LocalBusiness markups. If empty, the standard meta description is used.',

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

  'MODULE_' . $modulname . '_ENABLE_SHIPPING_DETAILS_TITLE' => 'Output ShippingDetails in JSON-LD?',
  'MODULE_' . $modulname . '_ENABLE_SHIPPING_DETAILS_DESC'  => 'If "Yes", the shipping information configured below will be output as <code>shippingDetails</code> in the Offers.',

  'MODULE_' . $modulname . '_SHIPPING_CONFIG_TITLE' => 'Shipping configuration for shippingDetails',
  'MODULE_' . $modulname . '_SHIPPING_CONFIG_DESC'  => '<code>country|label|price|currency|handlingMin|handlingMax|transitMin|transitMax|minValue|maxValue</code>

<p><strong>Fields:</strong></p>
<ul>
<li><b>country</b>: Country codes (ISO2), separated by commas
  &nbsp;&nbsp;e.g. <code>DE</code> or <code>DE,AT,CH</code></li>
<li><b>label</b>: Name of the shipping method
  &nbsp;&nbsp;e.g. <code>DHL Standard</code>, <code>International Shipping</code></li>
<li><b>price</b>: Shipping costs<br>
  &nbsp;&nbsp;&ndash; <code>0.00</code> for free<br>
  &nbsp;&nbsp;&ndash; <code>free</code> is automatically converted to <code>0.00</code></li>
<li><b>currency</b>: Currency, e.g. <code>EUR</code></li>
<li><b>handlingMin / handlingMax</b>: Handling time in days
  &nbsp;&nbsp;&rarr; e.g. <code>0|1</code> = between 0 and 1 day</li>
<li><b>transitMin / transitMax</b>: Transit time in days
  &nbsp;&nbsp;&rarr; e.g. <code>1|3</code> = delivery between 1 and 3 days</li>
<li><b>minValue</b> (optional): Minimum value of goods from which this shipping rule applies</li>
<li><b>maxValue</b> (optional): Maximum value of goods up to which the rule applies</li>
</ul>
<p>If minValue/maxValue are left empty &rarr; applies to all values of goods.</p>
<p><strong>Examples:</strong></p>
<ol>
<li>Germany & Austria, Standard shipping €4.90
  <code>DE,AT|Standard Shipping|4.90|EUR|0|1|1|3</code></li>
<li>Germany, Free shipping from €150
  <code>DE|DHL Standard from 150 EUR|0.00|EUR|0|1|1|3|150|</code></li>
<li>Switzerland, International shipping €9.90, without value of goods limitation
  <code>CH|International Shipping|9.90|EUR|0|1|2|5</code></li>
<li>EU shipping with minimum and maximum value
  <code>EU|EU Shipping|12.90|EUR|0|2|3|7|50|200</code></li>
</ol>
<p><strong>Notes:</strong></p>
<ul>
<li>Each line generates its own <em>OfferShippingDetails</em> structure.</li>
<li>If multiple countries are specified, the system automatically creates individual entries per country.</li>
<li>minValue/maxValue are optional &ndash; if the fields are empty, no restrictions apply.</li>
</ul>',

  'MODULE_' . $modulname . '_ENABLE_RETURNS_TITLE' => 'Output Return Policy (hasMerchantReturnPolicy)?',
  'MODULE_' . $modulname . '_ENABLE_RETURNS_DESC'  => 'If "Yes", the return policy configured below will be output as <code>hasMerchantReturnPolicy</code> in the Offers.',

  'MODULE_' . $modulname . '_RETURN_POLICY_CONFIG_TITLE' => 'Return Policy Configuration',
  'MODULE_' . $modulname . '_RETURN_POLICY_CONFIG_DESC'  => 'Enter one return rule per line. Format:<br>
<code>country|minDays|maxDays|category|feeType|method</code>
<p><strong>Fields:</strong></p>
<ul>
<li><b>country</b>: Country codes (ISO2), separated by commas &ndash; e.g. <code>DE</code> or <code>DE,AT,CH</code></li>
<li><b>minDays</b>: Minimum return period in days</li>
<li><b>maxDays</b>: Maximum return period in days (can be identical to minDays)</li>
<li><b>category</b>: Return policy type<br>
 &nbsp;&nbsp;&bull; <code>finite</code> &ndash; returns possible and time-limited<br>
 &nbsp;&nbsp;&bull; <code>unlimited</code> &ndash; returns possible with no time limit<br>
 &nbsp;&nbsp;&bull; <code>not_permitted</code> &ndash; returns not allowed</li>
<li><b>feeType</b>: Who bears the return shipping costs?<br>
 &nbsp;&nbsp;&bull; <code>free</code> &ndash; Merchant bears the costs (FreeReturn)<br>
 &nbsp;&nbsp;&bull; <code>buyer</code> &ndash; Customer bears the costs<br>
 &nbsp;&nbsp;&bull; <code>seller</code> &ndash; Merchant bears the costs</li>
<li><b>method</b>: Return method<br>
 &nbsp;&nbsp;&bull; <code>mail</code> &ndash; Return by mail/shipping<br>
 &nbsp;&nbsp;&bull; <code>store</code> &ndash; Return in physical store<br>
 &nbsp;&nbsp;&bull; <code>both</code> &ndash; Return by mail or in store<br>
 &nbsp;&nbsp;&bull; <code>none</code> &ndash; No return possible</li>
</ul>
<p><strong>Examples:</strong></p>
<ol>
<li>Germany, 14–30 days return period, free of charge, by mail:<br>
<code>DE|14|30|finite|free|mail</code></li>
<li>Austria & Switzerland, 14–30 days, customer pays return shipping:<br>
<code>AT,CH|14|30|finite|buyer|mail</code></li>
<li>No returns for specific countries:<br>
<code>US|0|0|not_permitted|buyer|none</code></li>
</ol>',

  'MODULE_' . $modulname . '_PRICEVALID_DEFAULT_DAYS_TITLE' => 'Default validity for priceValidUntil (days)',
  'MODULE_' . $modulname . '_PRICEVALID_DEFAULT_DAYS_DESC'  => 'If no expiry date from a special price is available, <code>priceValidUntil</code> is set to this number of days into the future. 0 = do not set <code>priceValidUntil</code>.',

  'MODULE_' . $modulname . '_UPDATE_AVAILABLE_TITLE' => '<span style="font-weight:bold;color:#900;background:#ff6;border-radius:3px;padding:2px;border:1px solid #900;">Please update the module!</span>',
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