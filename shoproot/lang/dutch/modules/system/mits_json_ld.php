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
  'MODULE_' . $modulname . '_TITLE'        => 'MITS JSON-LD voor modified eCommerce Shopsoftware <span style="white-space:nowrap;">© door <span style="padding:2px;background:#ffe;color:#6a9;font-weight:bold;">Hetfield (MerZ IT-SerVice)</span></span>',
  'MODULE_' . $modulname . '_DESCRIPTION'  => '
    <a href="https://www.merz-it-service.de/" target="_blank">
      <img src="' . (ENABLE_SSL === true ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG . DIR_WS_IMAGES . 'merz-it-service.png" border="0" alt="MerZ IT-SerVice" style="display:block;max-width:100%;height:auto;" />
    </a><br />
    <p style="font-size: larger">Met deze module breidt u uw modified eCommerce Shopsoftware uit met de door Google aanbevolen JSON-LD-markeringen.</p>
    <p>De module ondersteunt markeringen voor de volgende typen:</p>
    <ul style="font-size: larger">
      <li>WebSite <small>name, alternateName, description, url en logo</small></li>
      <li>Organization <small>name, alternateName, description, address, url, logo, founder, foundingDate en ContactPoints voor customer service, technical support, billing support, sales</small></li>
      <li>LocalBusiness <small>name, image, description, url, telephone, address, geo, sameAs, founder, foundingDate</small></li>
      <li>WebPage <small>contentmanagerpagina&#39;s</small></li>
      <li>ContactPage <small>name, url, description</small></li>
      <li>Breadcrumb</li>
      <li>CollectionPage <small>Categorie- en zoekresultaatpagina&#39;s met ItemList</small></li>
      <li>Product <small>name, image, description, brand, priceCurrency, priceValidUntil (vast: 1 maand), price, url, itemCondition, availability, mpn, sku, gtin13 en reviews</small></li>
      <li>Review <small>ratingValue, worstRating, bestRating, author, datePublished, reviewBody</small></li>
      <li>Sitelink Searchbox</li>
    </ul>
    <p style="font-size: larger">Uitbreidingen of aanpassingen zijn uiteraard mogelijk. Voor individuele wensen kunt u direct contact met ons opnemen.<br />
    <p>Bij vragen, problemen of verzoeken omtrent deze module of andere zaken rond modified eCommerce Shopsoftware, neem gerust contact met ons op:</p> 
    <div style="text-align:center;"><a style="background:#6a9;color:#444" target="_blank" href="https://www.merz-it-service.de/Kontakt.html" class="button" onclick="this.blur();">Contactpagina op MerZ-IT-SerVice.de</a></div>  
',

  'MODULE_' . $modulname . '_STATUS_TITLE' => 'Module activeren?',
  'MODULE_' . $modulname . '_STATUS_DESC'  => 'De module MITS JSON-LD in de shop activeren?',

  'MODULE_' . $modulname . '_SHOW_BREADCRUMB_TITLE' => 'Breadcrumb activeren?',
  'MODULE_' . $modulname . '_SHOW_BREADCRUMB_DESC'  => 'JSON-LD-markering voor breadcrumbs activeren?',

  'MODULE_' . $modulname . '_SHOW_PRODUCT_TITLE' => 'Producten activeren?',
  'MODULE_' . $modulname . '_SHOW_PRODUCT_DESC'  => 'JSON-LD-markering voor producten op de productdetailpagina activeren?',

  'MODULE_' . $modulname . '_SHOW_CATEGORY_TITLE' => 'Categoriepagina&#39;s activeren?',
  'MODULE_' . $modulname . '_SHOW_CATEGORY_DESC'  => 'JSON-LD markup voor categoriepagina&#39;s als <code>CollectionPage</code> met een <code>ItemList</code> van de zichtbare productlijst uitvoeren? Alleen product-URL&#39;s worden toegevoegd, geen volledige Product-markup.',

  'MODULE_' . $modulname . '_SHOW_SEARCH_RESULTS_TITLE' => 'Zoekresultaatpagina&#39;s activeren?',
  'MODULE_' . $modulname . '_SHOW_SEARCH_RESULTS_DESC'  => 'JSON-LD markup voor de zoekresultaatpagina <code>advanced_search_result.php</code> als <code>CollectionPage</code> met <code>ItemList</code> van de zichtbare productlijst uitvoeren? Paginering wordt meegenomen; er worden alleen product-URL&#39;s in de lijst uitgevoerd, geen volledige Product-markups.',

  'MODULE_' . $modulname . '_SHOW_CONTENT_TITLE' => 'Contentpagina&#39;s activeren?',
  'MODULE_' . $modulname . '_SHOW_CONTENT_DESC'  => 'JSON-LD-markering voor algemene contentmanagerpagina&#39;s uitvoeren. Standaard is <code>WebPage</code>; als de optionele contentkolom <code>mits_jsonld_schema_type</code> bestaat en <code>Article</code> bevat, wordt <code>Article</code> gebruikt. De contactpagina blijft <code>ContactPage</code>.',

  'MODULE_' . $modulname . '_ENABLE_ATTRIBUTES_TITLE' => 'Productattributen in JSON-LD weergeven',
  'MODULE_' . $modulname . '_ENABLE_ATTRIBUTES_DESC'  => 'Moeten productattributen als Offers worden weergegeven?',

  'MODULE_' . $modulname . '_ENABLE_TAGS_TITLE' => 'Producteigenschappen in JSON-LD weergeven',
  'MODULE_' . $modulname . '_ENABLE_TAGS_DESC'  => 'Moeten producteigenschappen (tags) als additionalProperty worden weergegeven?',

  'MODULE_' . $modulname . '_MAX_OFFERS_TITLE' => 'Maximaal aantal Offers',
  'MODULE_' . $modulname . '_MAX_OFFERS_DESC'  => 'Voorkomt geheugenproblemen bij veel attributen. Standaard: 100.',

  'MODULE_' . $modulname . '_ENABLE_MICRODATA_FIX_TITLE' => 'Microdata Fix activeren?',
  'MODULE_' . $modulname . '_ENABLE_MICRODATA_FIX_DESC'  => '<i>Verwijdert met behulp van jQuery de Microdata-attributen uit de shop, voor het geval dat het Microdata-schema nog steeds aanwezig is in de gebruikte template. Dubbele gestructureerde gegevens (JSON-LD en Microdata) zijn niet ideaal omdat ze tot inconsistenties kunnen leiden.',

  'MODULE_' . $modulname . '_ENABLE_CUSTOM_JSON_TITLE' => 'Aangepaste JSON-LD automatisch uit teksten detecteren & integreren?',
  'MODULE_' . $modulname . '_ENABLE_CUSTOM_JSON_DESC'  => 'Indien geactiveerd, zoekt de module product- en inhoudspagina\'s af naar ingebedde &lt;script type="application/ld+json"&gt;&mldr;&lt;/script&gt;-blokken, verwijdert deze uit de tekst en integreert ze correct in de centrale JSON-LD van de module.<br><br><strong>Opmerking:</strong> Deze functie is alleen nodig als gestructureerde gegevens in de editor zijn ingesloten. Bij zeer grote teksten of drukbezochte shops kan dit leiden tot een licht verhoogde serverbelasting.',

  'MODULE_' . $modulname . '_JSON_ENCODING_TITLE' => 'JSON-codering voor uitvoer',
  'MODULE_' . $modulname . '_JSON_ENCODING_DESC'  => 'Bepaalt hoe strings v&oacute;&oacute;r <code>json_encode()</code> worden genormaliseerd. <code>auto</code> behoudt geldige UTF-8 en converteert anders ISO-8859-15 naar UTF-8.',

  'MODULE_' . $modulname . '_SHOW_PRODUCT_REVIEWS_TITLE' => 'Productreviews activeren?',
  'MODULE_' . $modulname . '_SHOW_PRODUCT_REVIEWS_DESC'  => 'JSON-LD-markering voor reviews op de productdetailpagina activeren? Alleen in combinatie met productmarkering.',

  'MODULE_' . $modulname . '_SHOW_PRODUCT_REVIEWS_INFO_TITLE' => 'Detailpagina voor reviews activeren?',
  'MODULE_' . $modulname . '_SHOW_PRODUCT_REVIEWS_INFO_DESC'  => 'JSON-LD-markering voor de detailpagina van een review activeren?',

  'MODULE_' . $modulname . '_SHOW_SEARCHFIELD_TITLE' => 'Sitelinks Searchbox activeren?',
  'MODULE_' . $modulname . '_SHOW_SEARCHFIELD_DESC'  => 'JSON-LD-markering voor de Sitelinks Searchbox activeren?',

  'MODULE_' . $modulname . '_SHOW_WEBSITE_TITLE' => 'WebSite activeren?',
  'MODULE_' . $modulname . '_SHOW_WEBSITE_DESC'  => 'JSON-LD-markering voor WebSite activeren?',

  'MODULE_' . $modulname . '_SHOW_LOGO_TITLE' => 'Logo activeren?',
  'MODULE_' . $modulname . '_SHOW_LOGO_DESC'  => 'Het logo wordt gebruikt bij WebSite, Organization en LocalBusiness.',

  'MODULE_' . $modulname . '_LOGOFILE_TITLE' => 'Logo',
  'MODULE_' . $modulname . '_LOGOFILE_DESC'  => 'Voer alleen de bestandsnaam in (zonder pad). Het moet in de img-map van het gebruikte template staan (standaard: logo.gif).',

  'MODULE_' . $modulname . '_SHOW_ORGANISTATION_TITLE' => 'Organization activeren?',
  'MODULE_' . $modulname . '_SHOW_ORGANISTATION_DESC'  => 'JSON-LD-markering voor Organization activeren?',

  'MODULE_' . $modulname . '_SHOW_CONTACT_TITLE' => 'ContactPage activeren?',
  'MODULE_' . $modulname . '_SHOW_CONTACT_DESC'  => 'JSON-LD-markering voor ContactPage activeren?',

  'MODULE_' . $modulname . '_SHOW_LOCATION_TITLE' => 'LocalBusiness activeren?',
  'MODULE_' . $modulname . '_SHOW_LOCATION_DESC'  => 'JSON-LD-markering voor LocalBusiness activeren?',

  'MODULE_' . $modulname . '_NAME_TITLE' => 'Naam van het bedrijf/website',
  'MODULE_' . $modulname . '_NAME_DESC'  => 'Wordt gebruikt voor WebSite, Organization en LocalBusiness.',

  'MODULE_' . $modulname . '_ALTERNATE_NAME_TITLE' => 'Alternatieve naam van het bedrijf/website',
  'MODULE_' . $modulname . '_ALTERNATE_NAME_DESC'  => 'Wordt gebruikt voor WebSite, Organization en LocalBusiness.',

  'MODULE_' . $modulname . '_WEBSITE_DESCRIPTION_TITLE' => 'Beschrijving van het bedrijf/website',
  'MODULE_' . $modulname . '_WEBSITE_DESCRIPTION_DESC'  => 'Wordt gebruikt voor WebSite, Organization en LocalBusiness. Indien leeg, wordt de standaard meta-description gebruikt.',

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


  'MODULE_' . $modulname . '_EMAIL_SERVICE_TITLE' => 'Service email address',
  'MODULE_' . $modulname . '_EMAIL_SERVICE_DESC'  => 'This email address is output as <code>email</code> for the customer service ContactPoint.',

  'MODULE_' . $modulname . '_EMAIL_TECHNICAL_TITLE' => 'Technical support email address',
  'MODULE_' . $modulname . '_EMAIL_TECHNICAL_DESC'  => 'This email address is output as <code>email</code> for the technical support ContactPoint.',

  'MODULE_' . $modulname . '_EMAIL_BILLING_TITLE' => 'Billing email address',
  'MODULE_' . $modulname . '_EMAIL_BILLING_DESC'  => 'This email address is output as <code>email</code> for the billing support ContactPoint.',

  'MODULE_' . $modulname . '_EMAIL_SALES_TITLE' => 'Sales email address',
  'MODULE_' . $modulname . '_EMAIL_SALES_DESC'  => 'This email address is output as <code>email</code> for the sales ContactPoint.',

  'MODULE_' . $modulname . '_CONTACT_OPTION_DEFAULT_TITLE' => 'ContactOption default contact',
  'MODULE_' . $modulname . '_CONTACT_OPTION_DEFAULT_DESC'  => 'Optional Schema.org <code>contactOption</code> for the default contact. Possible values: empty, <code>TollFree</code> or <code>HearingImpairedSupported</code>.',

  'MODULE_' . $modulname . '_CONTACT_OPTION_SERVICE_TITLE' => 'ContactOption customer service',
  'MODULE_' . $modulname . '_CONTACT_OPTION_SERVICE_DESC'  => 'Optional Schema.org <code>contactOption</code> for customer service.',

  'MODULE_' . $modulname . '_CONTACT_OPTION_TECHNICAL_TITLE' => 'ContactOption technical support',
  'MODULE_' . $modulname . '_CONTACT_OPTION_TECHNICAL_DESC'  => 'Optional Schema.org <code>contactOption</code> for technical support.',

  'MODULE_' . $modulname . '_CONTACT_OPTION_BILLING_TITLE' => 'ContactOption billing',
  'MODULE_' . $modulname . '_CONTACT_OPTION_BILLING_DESC'  => 'Optional Schema.org <code>contactOption</code> for billing support.',

  'MODULE_' . $modulname . '_CONTACT_OPTION_SALES_TITLE' => 'ContactOption sales',
  'MODULE_' . $modulname . '_CONTACT_OPTION_SALES_DESC'  => 'Optional Schema.org <code>contactOption</code> for sales.',

  'MODULE_' . $modulname . '_CONTACT_HOURS_AVAILABLE_TITLE' => 'Contact availability fallback / hoursAvailable',
  'MODULE_' . $modulname . '_CONTACT_HOURS_AVAILABLE_DESC'  => 'Optional fallback: one time range per line in the format <code>Mo-Fr 09:00-17:00</code>. Multiple days can be comma-separated, e.g. <code>Mo,We,Fr 10:00-14:00</code>. This value is only used when the respective ContactPoint has no specific availability configured.',

  'MODULE_' . $modulname . '_CONTACT_HOURS_DEFAULT_TITLE' => 'Availability default contact',
  'MODULE_' . $modulname . '_CONTACT_HOURS_DEFAULT_DESC'  => 'Optional: specific <code>hoursAvailable</code> entries for the default contact. Leave empty to use the fallback.',

  'MODULE_' . $modulname . '_CONTACT_HOURS_SERVICE_TITLE' => 'Availability customer service',
  'MODULE_' . $modulname . '_CONTACT_HOURS_SERVICE_DESC'  => 'Optional: specific <code>hoursAvailable</code> entries for customer service. Leave empty to use the fallback.',

  'MODULE_' . $modulname . '_CONTACT_HOURS_TECHNICAL_TITLE' => 'Availability technical support',
  'MODULE_' . $modulname . '_CONTACT_HOURS_TECHNICAL_DESC'  => 'Optional: specific <code>hoursAvailable</code> entries for technical support. Leave empty to use the fallback.',

  'MODULE_' . $modulname . '_CONTACT_HOURS_BILLING_TITLE' => 'Availability billing',
  'MODULE_' . $modulname . '_CONTACT_HOURS_BILLING_DESC'  => 'Optional: specific <code>hoursAvailable</code> entries for billing support. Leave empty to use the fallback.',

  'MODULE_' . $modulname . '_CONTACT_HOURS_SALES_TITLE' => 'Availability sales',
  'MODULE_' . $modulname . '_CONTACT_HOURS_SALES_DESC'  => 'Optional: specific <code>hoursAvailable</code> entries for sales. Leave empty to use the fallback.',

  'MODULE_' . $modulname . '_FAX_TITLE' => 'Faxnummer',
  'MODULE_' . $modulname . '_FAX_DESC'  => 'Faxnummer van het bedrijf. Formaat: +49-2722-631400',

  'MODULE_' . $modulname . '_SOCIAL_MEDIA_TITLE' => 'Social-media profielen',
  'MODULE_' . $modulname . '_SOCIAL_MEDIA_DESC'  => 'Voer volledige URL’s in, gescheiden door komma’s.',

  'MODULE_' . $modulname . '_FOUNDER_TITLE' => 'Oprichter',
  'MODULE_' . $modulname . '_FOUNDER_DESC'  => 'Naam van de oprichter. Deze waarde wordt als <code>founder</code> in de Organization- en LocalBusiness-markup uitgegeven.',

  'MODULE_' . $modulname . '_FOUNDING_DATE_TITLE' => 'Oprichtingsdatum',
  'MODULE_' . $modulname . '_FOUNDING_DATE_DESC'  => 'Oprichtingsdatum in ISO-formaat <code>YYYY-MM-DD</code>, bijv. <code>2019-03-18</code>. Deze waarde wordt als <code>foundingDate</code> in de Organization- en LocalBusiness-markup uitgegeven.',


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

  'MODULE_' . $modulname . '_ENABLE_SHIPPING_DETAILS_TITLE' => 'ShippingDetails in JSON-LD uitvoeren?',
  'MODULE_' . $modulname . '_ENABLE_SHIPPING_DETAILS_DESC'  => 'Als "Ja", wordt de hieronder geconfigureerde verzendinformatie uitgevoerd als <code>shippingDetails</code> in de Offers.',

  'MODULE_' . $modulname . '_SHIPPING_CONFIG_TITLE' => 'Verzendconfiguratie voor shippingDetails',
  'MODULE_' . $modulname . '_SHIPPING_CONFIG_DESC'  => '<code>country|label|price|currency|handlingMin|handlingMax|transitMin|transitMax|minValue|maxValue</code>

<p><strong>Velden:</strong></p>
<ul>
<li><b>country</b>: Landcodes (ISO2), gescheiden door komma\'s
  &nbsp;&nbsp;bv. <code>DE</code> of <code>DE,AT,CH</code></li>
<li><b>label</b>: Naam van de verzendmethode
  &nbsp;&nbsp;bv. <code>DHL Standard</code>, <code>Internationale Verzending</code></li>
<li><b>price</b>: Verzendkosten<br>
  &nbsp;&nbsp;&ndash; <code>0.00</code> voor gratis<br>
  &nbsp;&nbsp;&ndash; <code>free</code> wordt automatisch omgezet naar <code>0.00</code></li>
<li><b>currency</b>: Valuta, bv. <code>EUR</code></li>
<li><b>handlingMin / handlingMax</b>: Verwerkingstijd in dagen
  &nbsp;&nbsp;&rarr; bv. <code>0|1</code> = tussen 0 en 1 dag</li>
<li><b>transitMin / transitMax</b>: Levertijd in dagen
  &nbsp;&nbsp;&rarr; bv. <code>1|3</code> = levering tussen 1 en 3 dagen</li>
<li><b>minValue</b> (optioneel): Minimale goederenwaarde vanaf waar deze verzendregel van toepassing is</li>
<li><b>maxValue</b> (optioneel): Maximale goederenwaarde tot waar de regel van toepassing is</li>
</ul>
<p>Als minValue/maxValue leeg blijven &rarr; geldt voor alle goederenwaarden.</p>
<p><strong>Voorbeelden:</strong></p>
<ol>
<li>Duitsland & Oostenrijk, Standaard verzending €4,90
  <code>DE,AT|Standaard Verzending|4.90|EUR|0|1|1|3</code></li>
<li>Duitsland, Gratis verzending vanaf €150
  <code>DE|DHL Standaard vanaf 150 EUR|0.00|EUR|0|1|1|3|150|</code></li>
<li>Zwitserland, Internationale verzending €9,90, zonder beperking van goederenwaarde
  <code>CH|Internationale Verzending|9.90|EUR|0|1|2|5</code></li>
<li>EU-verzending met minimum- en maximumwaarde
  <code>EU|EU Verzending|12.90|EUR|0|2|3|7|50|200</code></li>
</ol>
<p><strong>Opmerkingen:</strong></p>
<ul>
<li>Elke regel genereert zijn eigen <em>OfferShippingDetails</em>-structuur.</li>
<li>Als er meerdere landen worden opgegeven, creëert het systeem automatisch afzonderlijke vermeldingen per land.</li>
<li>minValue/maxValue zijn optioneel &ndash; bij lege velden zijn er geen beperkingen van toepassing.</li>
</ul>',

  'MODULE_' . $modulname . '_ENABLE_RETURNS_TITLE' => 'Retourbeleid (hasMerchantReturnPolicy) uitvoeren?',
  'MODULE_' . $modulname . '_ENABLE_RETURNS_DESC'  => 'Als "Ja", wordt het hieronder geconfigureerde retourbeleid uitgevoerd als <code>hasMerchantReturnPolicy</code> in de Offers.',

  'MODULE_' . $modulname . '_RETURN_POLICY_CONFIG_TITLE' => 'Retourbeleid Configuratie',
  'MODULE_' . $modulname . '_RETURN_POLICY_CONFIG_DESC'  => 'Voer één retourregel per regel in. Formaat:<br>
<code>country|minDays|maxDays|category|feeType|method</code>
<p><strong>Velden:</strong></p>
<ul>
<li><b>country</b>: Landcodes (ISO2), gescheiden door komma\'s &ndash; bv. <code>DE</code> of <code>DE,AT,CH</code></li>
<li><b>minDays</b>: Minimale retourperiode in dagen</li>
<li><b>maxDays</b>: Maximale retourperiode in dagen (mag identiek zijn aan minDays)</li>
<li><b>category</b>: Type retourregel<br>
 &nbsp;&nbsp;&bull; <code>finite</code> &ndash; retour mogelijk en tijdelijk beperkt<br>
 &nbsp;&nbsp;&bull; <code>unlimited</code> &ndash; retour onbeperkt in tijd<br>
 &nbsp;&nbsp;&bull; <code>not_permitted</code> &ndash; retour niet toegestaan</li>
<li><b>feeType</b>: Wie draagt de retourverzendkosten?<br>
 &nbsp;&nbsp;&bull; <code>free</code> &ndash; Verkoper draagt de kosten (FreeReturn)<br>
 &nbsp;&nbsp;&bull; <code>buyer</code> &ndash; Klant draagt de kosten<br>
 &nbsp;&nbsp;&bull; <code>seller</code> &ndash; Verkoper draagt de kosten</li>
<li><b>method</b>: Retourmethode<br>
 &nbsp;&nbsp;&bull; <code>mail</code> &ndash; Retour per post/verzending<br>
 &nbsp;&nbsp;&bull; <code>store</code> &ndash; Retour in de fysieke winkel<br>
 &nbsp;&nbsp;&bull; <code>both</code> &ndash; Retour per post of in de winkel<br>
 &nbsp;&nbsp;&bull; <code>none</code> &ndash; Geen retour mogelijk</li>
</ul>
<p><strong>Voorbeelden:</strong></p>
<ol>
<li>Duitsland, 14–30 dagen retourtermijn, gratis, per verzending:<br>
<code>DE|14|30|finite|free|mail</code></li>
<li>Oostenrijk & Zwitserland, 14–30 dagen, klant betaalt retourzending:<br>
<code>AT,CH|14|30|finite|buyer|mail</code></li>
<li>Geen retour voor specifieke landen:<br>
<code>US|0|0|not_permitted|buyer|none</code></li>
</ol>',

  'MODULE_' . $modulname . '_PRICEVALID_DEFAULT_DAYS_TITLE' => 'Standaard geldigheid voor priceValidUntil (dagen)',
  'MODULE_' . $modulname . '_PRICEVALID_DEFAULT_DAYS_DESC'  => 'Als er geen vervaldatum van een speciale prijs beschikbaar is, wordt <code>priceValidUntil</code> ingesteld op dit aantal dagen in de toekomst. 0 = geen <code>priceValidUntil</code> instellen.',

  'MODULE_' . $modulname . '_UPDATE_AVAILABLE_TITLE' => '<span style="font-weight:bold;color:#900;background:#ff6;border-radius:3px;padding:2px;border:1px solid #900;">Module-update vereist!</span>',
  'MODULE_' . $modulname . '_UPDATE_AVAILABLE_DESC'  => '',
  'MODULE_' . $modulname . '_UPDATE_FINISHED'        => 'De module MITS JSON-LD is bijgewerkt.',
  'MODULE_' . $modulname . '_UPDATE_ERROR'           => 'Fout',
  'MODULE_' . $modulname . '_UPDATE_MODUL'           => 'Module bijwerken',
  'MODULE_' . $modulname . '_DELETE_MODUL'           => 'MITS JSON-LD volledig van de server verwijderen',
  'MODULE_' . $modulname . '_CONFIRM_DELETE_MODUL'   => 'Wil je de module MITS JSON-LD en alle bestanden echt van de server verwijderen?',
  'MODULE_' . $modulname . '_DELETE_FINISHED'        => 'De module MITS JSON-LD is van de server verwijderd.',
);

foreach ($lang_array as $key => $val) {
    defined($key) || define($key, $val);
}
