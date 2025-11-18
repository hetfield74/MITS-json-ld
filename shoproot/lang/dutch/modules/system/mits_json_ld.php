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
  'MODULE_' . $modulname . '_TITLE'        => 'MITS JSON+LD voor modified eCommerce Shopsoftware <span style="white-space:nowrap;">© door <span style="padding:2px;background:#ffe;color:#6a9;font-weight:bold;">Hetfield (MerZ IT-SerVice)</span></span>',
  'MODULE_' . $modulname . '_DESCRIPTION'  => '
    <a href="https://www.merz-it-service.de/" target="_blank">
      <img src="' . (ENABLE_SSL === true ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG . DIR_WS_IMAGES . 'merz-it-service.png" border="0" alt="MerZ IT-SerVice" style="display:block;max-width:100%;height:auto;" />
    </a><br />
    <p style="font-size: larger">Met deze module breidt u uw modified eCommerce Shopsoftware uit met de door Google aanbevolen JSON+LD-markeringen.</p>
    <p>De module ondersteunt markeringen voor de volgende typen:</p>
    <ul style="font-size: larger">
      <li>WebSite <small>name, alternateName, description, url en logo</small></li>
      <li>Organization <small>name, alternateName, description, url, logo en ContactPoints voor customer service, technical support, billing support, sales</small></li>
      <li>LocalBusiness <small>name, image, url, telephone, address, geo, sameAs</small></li>
      <li>ContactPage <small>name, url, description</small></li>
      <li>Breadcrumb</li>
      <li>Product <small>name, image, description, brand, priceCurrency, priceValidUntil (vast: 1 maand), price, url, itemCondition, availability, mpn, sku, gtin13 en reviews</small></li>
      <li>Review <small>ratingValue, worstRating, bestRating, author, datePublished, reviewBody</small></li>
      <li>Sitelink Searchbox</li>
    </ul>
    <p style="font-size: larger">Uitbreidingen of aanpassingen zijn uiteraard mogelijk. Voor individuele wensen kunt u direct contact met ons opnemen.<br />
    <div style="text-align:center;">
      <small>Op Github vindt u altijd de meest recente versie van de module!</small><br />
      <a style="background:#6a9;color:#444" target="_blank" href="https://github.com/hetfield74/MITS-json-ld" class="button" onclick="this.blur();">MITS JSON+LD op Github</a>
    </div>
    <p>Bij vragen, problemen of verzoeken omtrent deze module of andere zaken rond modified eCommerce Shopsoftware, neem gerust contact met ons op:</p> 
    <div style="text-align:center;"><a style="background:#6a9;color:#444" target="_blank" href="https://www.merz-it-service.de/Kontakt.html" class="button" onclick="this.blur();">Contactpagina op MerZ-IT-SerVice.de</a></div>  
',

  'MODULE_' . $modulname . '_STATUS_TITLE' => 'Module activeren?',
  'MODULE_' . $modulname . '_STATUS_DESC'  => 'De module MITS JSON+LD in de shop activeren?',

  'MODULE_' . $modulname . '_SHOW_BREADCRUMB_TITLE' => 'Breadcrumb activeren?',
  'MODULE_' . $modulname . '_SHOW_BREADCRUMB_DESC'  => 'JSON+LD-markering voor breadcrumbs activeren?',

  'MODULE_' . $modulname . '_SHOW_PRODUCT_TITLE' => 'Producten activeren?',
  'MODULE_' . $modulname . '_SHOW_PRODUCT_DESC'  => 'JSON+LD-markering voor producten op de productdetailpagina activeren?',

  'MODULE_' . $modulname . '_ENABLE_ATTRIBUTES_TITLE' => 'Productattributen in JSON+LD weergeven',
  'MODULE_' . $modulname . '_ENABLE_ATTRIBUTES_DESC'  => 'Moeten productattributen als Offers worden weergegeven?',

  'MODULE_' . $modulname . '_ENABLE_TAGS_TITLE' => 'Producteigenschappen in JSON+LD weergeven',
  'MODULE_' . $modulname . '_ENABLE_TAGS_DESC'  => 'Moeten producteigenschappen (tags) als additionalProperty worden weergegeven?',

  'MODULE_' . $modulname . '_MAX_OFFERS_TITLE' => 'Maximaal aantal Offers',
  'MODULE_' . $modulname . '_MAX_OFFERS_DESC'  => 'Voorkomt geheugenproblemen bij veel attributen. Standaard: 100.',

  'MODULE_' . $modulname . '_SHOW_PRODUCT_REVIEWS_TITLE' => 'Productreviews activeren?',
  'MODULE_' . $modulname . '_SHOW_PRODUCT_REVIEWS_DESC'  => 'JSON+LD-markering voor reviews op de productdetailpagina activeren? Alleen in combinatie met productmarkering.',

  'MODULE_' . $modulname . '_SHOW_PRODUCT_REVIEWS_INFO_TITLE' => 'Detailpagina voor reviews activeren?',
  'MODULE_' . $modulname . '_SHOW_PRODUCT_REVIEWS_INFO_DESC'  => 'JSON+LD-markering voor de detailpagina van een review activeren?',

  'MODULE_' . $modulname . '_SHOW_SEARCHFIELD_TITLE' => 'Sitelinks Searchbox activeren?',
  'MODULE_' . $modulname . '_SHOW_SEARCHFIELD_DESC'  => 'JSON+LD-markering voor de Sitelinks Searchbox activeren?',

  'MODULE_' . $modulname . '_SHOW_WEBSITE_TITLE' => 'WebSite activeren?',
  'MODULE_' . $modulname . '_SHOW_WEBSITE_DESC'  => 'JSON+LD-markering voor WebSite activeren?',

  'MODULE_' . $modulname . '_SHOW_LOGO_TITLE' => 'Logo activeren?',
  'MODULE_' . $modulname . '_SHOW_LOGO_DESC'  => 'Het logo wordt gebruikt bij WebSite, Organization en LocalBusiness.',

  'MODULE_' . $modulname . '_LOGOFILE_TITLE' => 'Logo',
  'MODULE_' . $modulname . '_LOGOFILE_DESC'  => 'Voer alleen de bestandsnaam in (zonder pad). Het moet in de img-map van het gebruikte template staan (standaard: logo.gif).',

  'MODULE_' . $modulname . '_SHOW_ORGANISTATION_TITLE' => 'Organization activeren?',
  'MODULE_' . $modulname . '_SHOW_ORGANISTATION_DESC'  => 'JSON+LD-markering voor Organization activeren?',

  'MODULE_' . $modulname . '_SHOW_CONTACT_TITLE' => 'ContactPage activeren?',
  'MODULE_' . $modulname . '_SHOW_CONTACT_DESC'  => 'JSON+LD-markering voor ContactPage activeren?',

  'MODULE_' . $modulname . '_SHOW_LOCATION_TITLE' => 'LocalBusiness activeren?',
  'MODULE_' . $modulname . '_SHOW_LOCATION_DESC'  => 'JSON+LD-markering voor LocalBusiness activeren?',

  'MODULE_' . $modulname . '_NAME_TITLE' => 'Naam van het bedrijf/website',
  'MODULE_' . $modulname . '_NAME_DESC'  => 'Wordt gebruikt voor WebSite, Organization en LocalBusiness.',

  'MODULE_' . $modulname . '_ALTERNATE_NAME_TITLE' => 'Alternatieve naam van het bedrijf/website',
  'MODULE_' . $modulname . '_ALTERNATE_NAME_DESC'  => 'Wordt gebruikt voor WebSite, Organization en LocalBusiness.',

  'MODULE_' . $modulname . '_DESCRIPTION_TITLE' => 'Beschrijving van het bedrijf/website',
  'MODULE_' . $modulname . '_DESCRIPTION_DESC'  => 'Wordt gebruikt voor WebSite, Organization en LocalBusiness. Indien leeg, wordt de standaard meta-description gebruikt.',

  'MODULE_' . $modulname . '_EMAIL_TITLE' => 'E-mailadres van het bedrijf/website',
  'MODULE_' . $modulname . '_EMAIL_DESC'  => 'Wordt gebruikt voor WebSite, Organization en LocalBusiness.',

  'MODULE_' . $modulname . '_TELEPHONE_DEFAULT_TITLE' => 'Telefoonnummer',
  'MODULE_' . $modulname . '_TELEPHONE_DEFAULT_DESC'  => 'Hoofdnummer voor Organization en LocalBusiness. Formaat: +49-2722-631363',

  'MODULE_' . $modulname . '_TELEPHONE_SERVICE_TITLE' => 'Klantservice telefoonnummer',
  'MODULE_' . $modulname . '_TELEPHONE_SERVICE_DESC'  => 'Telefoonnummer voor klantenservice. Formaat: +49-2722-631363',

  'MODULE_' . $modulname . '_TELEPHONE_TECHNICAL_TITLE' => 'Technische ondersteuning telefoonnummer',
  'MODULE_' . $modulname . '_TELEPHONE_TECHNICAL_DESC'  => 'Telefoonnummer voor technische ondersteuning. Formaat: +49-2722-631363',

  'MODULE_' . $modulname . '_TELEPHONE_BILLING_TITLE' => 'Facturatie telefoonnummer',
  'MODULE_' . $modulname . '_TELEPHONE_BILLING_DESC'  => 'Telefoonnummer voor factuurvragen. Formaat: +49-2722-631363',

  'MODULE_' . $modulname . '_TELEPHONE_SALES_TITLE' => 'Verkoop telefoonnummer',
  'MODULE_' . $modulname . '_TELEPHONE_SALES_DESC'  => 'Telefoonnummer voor verkoop. Formaat: +49-2722-631363',

  'MODULE_' . $modulname . '_FAX_TITLE' => 'Faxnummer',
  'MODULE_' . $modulname . '_FAX_DESC'  => 'Faxnummer van het bedrijf. Formaat: +49-2722-631400',

  'MODULE_' . $modulname . '_SOCIAL_MEDIA_TITLE' => 'Social-media profielen',
  'MODULE_' . $modulname . '_SOCIAL_MEDIA_DESC'  => 'Voer volledige URL’s in, gescheiden door komma’s.',

  'MODULE_' . $modulname . '_LOCATION_STREETADDRESS_TITLE' => 'Straat / huisnummer',
  'MODULE_' . $modulname . '_LOCATION_STREETADDRESS_DESC'  => 'Wordt gebruikt voor LocalBusiness-markering.',

  'MODULE_' . $modulname . '_LOCATION_ADDRESSLOCALITY_TITLE' => 'Plaats',
  'MODULE_' . $modulname . '_LOCATION_ADDRESSLOCALITY_DESC'  => 'Wordt gebruikt voor LocalBusiness.',

  'MODULE_' . $modulname . '_LOCATION_POSTALCODE_TITLE' => 'Postcode',
  'MODULE_' . $modulname . '_LOCATION_POSTALCODE_DESC'  => 'Postcode voor LocalBusiness.',

  'MODULE_' . $modulname . '_LOCATION_ADDRESSCOUNTRY_TITLE' => 'Land',
  'MODULE_' . $modulname . '_LOCATION_ADDRESSCOUNTRY_DESC'  => 'ISO-2 landcode (bijv. DE voor Duitsland).',

  'MODULE_' . $modulname . '_LOCATION_GEO_LATITUDE_TITLE' => 'GEO breedtegraad',
  'MODULE_' . $modulname . '_LOCATION_GEO_LATITUDE_DESC'  => 'Optioneel. Leeg laten indien niet gebruikt.',

  'MODULE_' . $modulname . '_LOCATION_GEO_LONGITUDE_TITLE' => 'GEO lengtegraad',
  'MODULE_' . $modulname . '_LOCATION_GEO_LONGITUDE_DESC'  => 'Optioneel. Leeg laten indien niet gebruikt.',

  'MODULE_' . $modulname . '_UPDATE_AVAILABLE_TITLE' => '<span style="font-weight:bold;color:#900;background:#ff6;padding:2px;border:1px solid #900;">Module-update vereist!</span>',
  'MODULE_' . $modulname . '_UPDATE_AVAILABLE_DESC'  => '',
  'MODULE_' . $modulname . '_UPDATE_FINISHED'        => 'De module MITS JSON+LD is bijgewerkt.',
  'MODULE_' . $modulname . '_UPDATE_ERROR'           => 'Fout',
  'MODULE_' . $modulname . '_UPDATE_MODUL'           => 'Module bijwerken',
  'MODULE_' . $modulname . '_DELETE_MODUL'           => 'MITS JSON+LD volledig van de server verwijderen',
  'MODULE_' . $modulname . '_CONFIRM_DELETE_MODUL'   => 'Wil je de module MITS JSON+LD en alle bestanden echt van de server verwijderen?',
  'MODULE_' . $modulname . '_DELETE_FINISHED'        => 'De module MITS JSON+LD is van de server verwijderd.',
);

foreach ($lang_array as $key => $val) {
    defined($key) || define($key, $val);
}
