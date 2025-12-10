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
  'MODULE_' . $modulname . '_TITLE'        => 'MITS JSON-LD f&uuml;r modified eCommerce Shopsoftware <span style="white-space:nowrap;">&copy; by <span style="padding:2px;background:#ffe;color:#6a9;font-weight:bold;">Hetfield (MerZ IT-SerVice)</span></span>',
  'MODULE_' . $modulname . '_DESCRIPTION'  => '
    <a href="https://www.merz-it-service.de/" target="_blank">
      <img src="' . (ENABLE_SSL === true ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG . DIR_WS_IMAGES . 'merz-it-service.png" border="0" alt="MerZ IT-SerVice" style="display:block;max-width:100%;height:auto;" />
    </a><br />
    <p style="font-size: larger">Mit diesem Modul erweitern Sie Ihre modified eCommerce Shopsoftware um die von Google empfohlenen JSON-LD Markups.</p>
    <p>Diese Modul unterstützt die Markups für folgende Typen:</p>
    <ul style="font-size: larger">
      <li>WebSite <small>name, alternateName, description, url und logo</small></li>
      <li>Organization <small>name, alternateName, description, url, logo und ContactPints f&uuml;r customer service, technical support, billing support, sales</small></li>
      <li>LocalBusiness <small>name, image, url, telephone, address, geo, sameAs</small></li>
      <li>ContactPage <small>name, url,  description</small></li>
      <li>Breadcrumb</li>
      <li>Product <small>name, image, description, brand, priceCurrency, priceValidUntil (Fix 1 Monat), price, url, itemCondition, avaiability, mpn, sku, gtin13 und reviews</small></li>
      <li>Review (Rating und aggregrateRating) <small>ratingValue, worstRating, bestRating, author, datePublished, reviewBody</small></li>
      <li>Sitelink Searchbox</li>
    </ul>
    <p style="font-size: larger">Das Modul kann auf Wunsch nat&uuml;rlich noch erweitert und angepasst werden. F&uuml;r Ihre individuellen Anpassungsw&uuml;sche wenden Sie sich einfach direkt an uns.<br />
    <div style="text-align:center;">
      <small>Nur auf Github gibt es immer die aktuellste Version des Moduls!</small><br />
      <a style="background:#6a9;color:#444" target="_blank" href="https://github.com/hetfield74/MITS-json-ld" class="button" onclick="this.blur();">MITS JSON-LD on Github</a>
    </div>
    <p>Bei Fragen, Problemen oder W&uuml;nschen zu diesem Modul oder auch zu anderen Anliegen rund um die modified eCommerce Shopsoftware nehmen Sie einfach Kontakt zu uns auf:</p> 
    <div style="text-align:center;"><a style="background:#6a9;color:#444" target="_blank" href="https://www.merz-it-service.de/Kontakt.html" class="button" onclick="this.blur();">Kontaktseite auf MerZ-IT-SerVice.de</strong></a></div>  
',
  'MODULE_' . $modulname . '_STATUS_TITLE' => 'Modul aktivieren?',
  'MODULE_' . $modulname . '_STATUS_DESC'  => 'Das Modul MITS JSON-LD im Shop aktivieren?',

  'MODULE_' . $modulname . '_SHOW_BREADCRUMB_TITLE' => 'Breadcrumb aktivieren?',
  'MODULE_' . $modulname . '_SHOW_BREADCRUMB_DESC'  => 'JSON-LD Markup f&uuml;r die Breadcrumb aktivieren?',

  'MODULE_' . $modulname . '_SHOW_PRODUCT_TITLE' => 'Produkte aktivieren?',
  'MODULE_' . $modulname . '_SHOW_PRODUCT_DESC'  => 'JSON-LD Markup f&uuml;r Produkte auf der Produktdetailseite aktivieren?',

  'MODULE_' . $modulname . '_ENABLE_ATTRIBUTES_TITLE' => 'Artikelmerkmale (Attribute) im JSON-LD ausgeben',
  'MODULE_' . $modulname . '_ENABLE_ATTRIBUTES_DESC'  => 'Sollen die Artikelmerkmale (Produktattribute) als Offers ausgegeben werden.',

  'MODULE_' . $modulname . '_ENABLE_TAGS_TITLE' => 'Artikeleigenschaften im JSON-LD ausgeben',
  'MODULE_' . $modulname . '_ENABLE_TAGS_DESC'  => 'Sollen die Artikeleigenschaften (Produkt-Tags) in additionalProperty ausgegeben werden?',

  'MODULE_' . $modulname . '_MAX_OFFERS_TITLE' => 'Max. Anzahl Offers',
  'MODULE_' . $modulname . '_MAX_OFFERS_DESC'  => 'Verhindert Memory-Probleme bei vielen Attributen. Default: 100.',

  'MODULE_' . $modulname . '_ENABLE_MICRODATA_FIX_TITLE' => 'Microdata Fix aktivieren?',
  'MODULE_' . $modulname . '_ENABLE_MICRODATA_FIX_DESC'  => 'Entfernt mittels jQuery die Microdata-Attribute aus dem Shop, falls das Microdata-Schema noch im verwendeten Template vorhanden ist. Die doppelten strukturierten Daten (JSON-LD und Microdata) sind nicht ideal, da sie zu Inkonsistenzen f&uuml;hren k&ouml;nnen.',

  'MODULE_' . $modulname . '_ENABLE_CUSTOM_JSON_TITLE' => 'Custom JSON-LD aus Texten automatisch erkennen & integrieren?',
  'MODULE_' . $modulname . '_ENABLE_CUSTOM_JSON_DESC'  => 'Wenn aktiviert, durchsucht das Modul Produkt- und Inhaltsseiten nach eingebetteten &lt;script type="application/ld+json"&gt;&mldr;&lt;/script&gt;-Bl&ouml;cken, entfernt diese aus dem Text und integriert sie korrekt in das zentrale JSON+LD des Moduls.<br><br><strong>Hinweis:</strong>Diese Funktion ist nur n&ouml;tig, wenn im Editor Strukturdaten eingebettet werden. Bei sehr gro&szlig;en Texten oder hochfrequentierten Shops kann es leicht erh&ouml;hte Serverlast verursachen.',

  'MODULE_' . $modulname . '_SHOW_PRODUCT_REVIEWS_TITLE' => 'Bewertungen bei Produkten aktivieren?',
  'MODULE_' . $modulname . '_SHOW_PRODUCT_REVIEWS_DESC'  => 'JSON-LD Markup f&uuml;r die Bewertungen auf der Produktdetailseite aktivieren? Nur in Kombination mit dem der Nutzung des Markups f&uuml;r das Produkt.',

  'MODULE_' . $modulname . '_SHOW_PRODUCT_REVIEWS_INFO_TITLE' => 'Bewertungsdetailseite aktivieren?',
  'MODULE_' . $modulname . '_SHOW_PRODUCT_REVIEWS_INFO_DESC'  => 'JSON-LD Markup f&uuml;r die Bewertungen auf der Detailseite einer Bewertung aktivieren?',

  'MODULE_' . $modulname . '_SHOW_SEARCHFIELD_TITLE' => 'Sitelinks Searchbox aktivieren?',
  'MODULE_' . $modulname . '_SHOW_SEARCHFIELD_DESC'  => 'JSON-LD Markup f&uuml;r die Sitelinks Searchbox aktivieren?',

  'MODULE_' . $modulname . '_SHOW_WEBSITE_TITLE' => 'WebSite aktivieren?',
  'MODULE_' . $modulname . '_SHOW_WEBSITE_DESC'  => 'JSON-LD Markup f&uuml;r die WebSite aktivieren?',

  'MODULE_' . $modulname . '_SHOW_LOGO_TITLE' => 'Logo aktivieren?',
  'MODULE_' . $modulname . '_SHOW_LOGO_DESC'  => 'Das Logo wird bei den Markups f&uuml;r WebSite, Organization und LocalBusiness verwendet.',

  'MODULE_' . $modulname . '_LOGOFILE_TITLE' => 'Logo',
  'MODULE_' . $modulname . '_LOGOFILE_DESC'  => 'Ihr Firmenlogo, Dateiname bitte ohne Pfad eingeben. Datei muss im img-Ordner des verwendeten Templates liegen (Standard: logo.gif). Das Logo wird bei den Markups f&uuml;r WebSite, Organization und LocalBusiness verwendet.',

  'MODULE_' . $modulname . '_SHOW_ORGANISTATION_TITLE' => 'Organization aktivieren?',
  'MODULE_' . $modulname . '_SHOW_ORGANISTATION_DESC'  => 'Soll das JSON-LD Markup f&uuml;r den Typ Organization aktiviert werden?',

  'MODULE_' . $modulname . '_SHOW_CONTACT_TITLE' => 'ContactPage aktivieren?',
  'MODULE_' . $modulname . '_SHOW_CONTACT_DESC'  => 'Soll das JSON-LD Markup f&uuml;r den Typ ContactPage aktiviert werden?',

  'MODULE_' . $modulname . '_SHOW_LOCATION_TITLE' => 'LocalBusiness aktivieren?',
  'MODULE_' . $modulname . '_SHOW_LOCATION_DESC'  => 'Soll das JSON-LD Markup f&uuml;r den Typ LocalBusiness aktiviert werden?',

  'MODULE_' . $modulname . '_NAME_TITLE' => 'Name der Firma/Webseite',
  'MODULE_' . $modulname . '_NAME_DESC'  => 'Der Name wird bei den Markups f&uuml;r WebSite, Organization und LocalBusiness verwendet.',

  'MODULE_' . $modulname . '_ALTERNATE_NAME_TITLE' => 'Alternativer Name der Firma/Webseite',
  'MODULE_' . $modulname . '_ALTERNATE_NAME_DESC'  => 'Der alternative Name wird bei den Markups f&uuml;r WebSite, Organization und LocalBusiness verwendet.',

  'MODULE_' . $modulname . '_WEBSITE_DESCRIPTION_TITLE' => 'Beschreibung der Firma/Webseite',
  'MODULE_' . $modulname . '_WEBSITE_DESCRIPTION_DESC'  => 'Die Beschreibung wird bei den Markups f&uuml;r WebSite, Organization und LocalBusiness verwendet. Wenn Sie dieses Feld nicht ausf&uuml;llen, dann wird die Standard Meta Description verwendet.',

  'MODULE_' . $modulname . '_EMAIL_TITLE' => 'E-Mail-Adresse der Firma/Webseite',
  'MODULE_' . $modulname . '_EMAIL_DESC'  => 'Die Beschreibung wird bei den Markups f&uuml;r WebSite, Organization und LocalBusiness verwendet.',

  'MODULE_' . $modulname . '_TELEPHONE_DEFAULT_TITLE' => 'Telefonnummer',
  'MODULE_' . $modulname . '_TELEPHONE_DEFAULT_DESC'  => 'Diese Telefonnummer wird als Hauptnummer bei den Markups f&uuml;r Organization und LocalBusiness verwendet. Notwendiges Format: +49-2722-631363',

  'MODULE_' . $modulname . '_TELEPHONE_SERVICE_TITLE' => 'Service-Telefonnummer',
  'MODULE_' . $modulname . '_TELEPHONE_SERVICE_DESC'  => 'Diese Telefonnummer wird f&uuml;r den Kundenservice bei den Markups f&uuml;r Organization verwendet. Notwendiges Format: +49-2722-631363',

  'MODULE_' . $modulname . '_TELEPHONE_TECHNICAL_TITLE' => 'Telefonnummer Technischer Support',
  'MODULE_' . $modulname . '_TELEPHONE_TECHNICAL_DESC'  => 'Diese Telefonnummer wird f&uuml;r den technischen Support bei den Markups f&uuml;r Organization verwendet. Notwendiges Format: +49-2722-631363',

  'MODULE_' . $modulname . '_TELEPHONE_BILLING_TITLE' => 'Telefonnummer Buchhaltung',
  'MODULE_' . $modulname . '_TELEPHONE_BILLING_DESC'  => 'Diese Telefonnummer wird f&uuml;r Rechnungsfragen bei den Markups f&uuml;r Organization verwendet. Notwendiges Format: +49-2722-631363',

  'MODULE_' . $modulname . '_TELEPHONE_SALES_TITLE' => 'Telefonnummer Verkauf',
  'MODULE_' . $modulname . '_TELEPHONE_SALES_DESC'  => 'Diese Telefonnummer wird f&uuml;r den Verkauf bei den Markups f&uuml;r Organization verwendet. Notwendiges Format: +49-2722-631363',

  'MODULE_' . $modulname . '_FAX_TITLE' => 'Faxnummer',
  'MODULE_' . $modulname . '_FAX_DESC'  => 'Die Faxnummer des Unternehmens. Notwendiges Format: +49-2722-631400',

  'MODULE_' . $modulname . '_SOCIAL_MEDIA_TITLE' => 'Social Media Profile',
  'MODULE_' . $modulname . '_SOCIAL_MEDIA_DESC'  => 'Tragen Sie hier die Adresse(n) zu Ihren Social Media Seiten als komplette URL ein. Mehrere Adressen bitte mit einem Komma trennen <i>(z.B. https://www.facebook.com/merzitservice.ecomtech,https://twitter.com/MerZ_IT_SerVice)</i>. Diese Angabe wird bei den Markups f&uuml;r WebSite, Organization und LocalBusiness verwendet.',

  'MODULE_' . $modulname . '_LOCATION_STREETADDRESS_TITLE' => 'Strasse/Hausnummer',
  'MODULE_' . $modulname . '_LOCATION_STREETADDRESS_DESC'  => 'Tragen Sie hier Ihren Strassennamen und die Hausnummer ein. Die Angabe wird bei den Markups f&uuml;r Localisation verwendet.',

  'MODULE_' . $modulname . '_LOCATION_ADDRESSLOCALITY_TITLE' => 'Stadt',
  'MODULE_' . $modulname . '_LOCATION_ADDRESSLOCALITY_DESC'  => 'Stadt/Ort der Adresse eintragen. Die Angabe wird bei den Markups f&uuml;r Localisation verwendet.',

  'MODULE_' . $modulname . '_LOCATION_POSTALCODE_TITLE' => 'Postcode (PLZ)',
  'MODULE_' . $modulname . '_LOCATION_POSTALCODE_DESC'  => 'Postcode (PLZ) der Adresse eintragen. Die Angabe wird bei den Markups f&uuml;r Localisation verwendet.',

  'MODULE_' . $modulname . '_LOCATION_ADDRESSCOUNTRY_TITLE' => 'Land',
  'MODULE_' . $modulname . '_LOCATION_ADDRESSCOUNTRY_DESC'  => 'Land der Adresse in ISO-Code-2 eintragen (z.B. DE f&uuml;r Deutschland). Die Angabe wird bei den Markups f&uuml;r Localisation verwendet.',

  'MODULE_' . $modulname . '_LOCATION_GEO_LATITUDE_TITLE' => 'GEO Latitude',
  'MODULE_' . $modulname . '_LOCATION_GEO_LATITUDE_DESC'  => 'Die Angabe ist optional wird bei dem Markup f&uuml;r Localisation verwendet. Leer lassen f&uuml;r keine Angabe.',

  'MODULE_' . $modulname . '_LOCATION_GEO_LONGITUDE_TITLE' => 'GEO Longitude',
  'MODULE_' . $modulname . '_LOCATION_GEO_LONGITUDE_DESC'  => 'Die Angabe ist optional wird bei dem Markup f&uuml;r Localisation verwendet. Leer lassen f&uuml;r keine Angabe.',

  'MODULE_' . $modulname . '_ENABLE_SHIPPING_DETAILS_TITLE' => 'ShippingDetails im JSON-LD ausgeben?',
  'MODULE_' . $modulname . '_ENABLE_SHIPPING_DETAILS_DESC'  => 'Wenn &bdquo;Ja&ldquo;, werden die unten konfigurierten Versandinformationen als <code>shippingDetails</code> in den Offers ausgegeben.',

  'MODULE_' . $modulname . '_SHIPPING_CONFIG_TITLE' => 'Versandkonfiguration f&uuml;r shippingDetails',
  'MODULE_' . $modulname . '_SHIPPING_CONFIG_DESC'  => '<code>country|label|price|currency|handlingMin|handlingMax|transitMin|transitMax|minValue|maxValue</code>

<p><strong>Felder:</strong></p>
<ul>
<li><b>country</b>: L&auml;ndercodes (ISO2), mehrere durch Komma getrennt  
&nbsp;&nbsp;z. B. <code>DE</code> oder <code>DE,AT,CH</code></li>
<li><b>label</b>: Name der Versandart  
&nbsp;&nbsp;z. B. <code>DHL Standard</code>, <code>Auslandsversand</code></li>
<li><b>price</b>: Versandkosten<br>
&nbsp;&nbsp;&ndash; <code>0.00</code> f&uuml;r kostenlos<br>
&nbsp;&nbsp;&ndash; <code>free</code> wird automatisch zu <code>0.00</code></li>
<li><b>currency</b>: W&auml;hrung, z. B. <code>EUR</code></li>
<li><b>handlingMin / handlingMax</b>: Bearbeitungszeit in Tagen  
&nbsp;&nbsp;&rarr; z. B. <code>0|1</code> = zwischen 0 und 1 Tag</li>
<li><b>transitMin / transitMax</b>: Lieferzeit in Tagen  
&nbsp;&nbsp;&rarr; z. B. <code>1|3</code> = Lieferung zwischen 1 und 3 Tagen</li>
<li><b>minValue</b> (optional): Mindestwarenwert, ab dem diese Versandregel gilt</li>
<li><b>maxValue</b> (optional): Maximaler Warenwert, bis zu dem die Regel gilt</li>
</ul>
<p>Bleiben minValue/maxValue leer &rarr; gilt f&uuml;r alle Warenwerte.</p>
<p><strong>Beispiele:</strong></p>
<ol>
<li>Deutschland &amp; &Ouml;sterreich, Standardversand 4,90 &euro;  
<code>DE,AT|Standardversand|4.90|EUR|0|1|1|3</code></li>
<li>Deutschland, Versandkostenfrei ab 150 &euro;  
<code>DE|DHL Standard ab 150 EUR|0.00|EUR|0|1|1|3|150|</code></li>
<li>Schweiz, Auslandsversand 9,90 &euro;, ohne Warenwert-Begrenzung  
<code>CH|Auslandsversand|9.90|EUR|0|1|2|5</code></li>
<li>EU-Versand mit Mindest- und Maximalwert  
<code>EU|EU-Versand|12.90|EUR|0|2|3|7|50|200</code></li>
</ol>
<p><strong>Hinweise:</strong></p>
<ul>
<li>Jede Zeile erzeugt eine eigene <em>OfferShippingDetails</em>-Struktur.</li>
<li>Werden mehrere L&auml;nder angegeben, erzeugt das System automatisch einzelne Eintr&auml;ge pro Land.</li>
<li>minValue/maxValue sind optional &ndash; bei leeren Feldern gelten keine Einschr&auml;nkungen.</li>
</ul>',

  'MODULE_' . $modulname . '_ENABLE_RETURNS_TITLE' => 'R&uuml;ckgaberichtlinien (hasMerchantReturnPolicy) ausgeben?',
  'MODULE_' . $modulname . '_ENABLE_RETURNS_DESC'  => 'Wenn &bdquo;Ja&ldquo;, werden die unten konfigurierten R&uuml;ckgaberichtlinien als <code>hasMerchantReturnPolicy</code> in den Offers ausgegeben.',

  'MODULE_' . $modulname . '_RETURN_POLICY_CONFIG_TITLE' => 'R&uuml;ckgabekonfiguration',
  'MODULE_' . $modulname . '_RETURN_POLICY_CONFIG_DESC'  => 'Trage pro Zeile eine R&uuml;ckgaberegel ein. Format:<br>
<code>country|minDays|maxDays|category|feeType|method</code>
<p><strong>Felder:</strong></p>
<ul>
<li><b>country</b>: L&auml;ndercodes (ISO2), mehrere durch Komma getrennt &ndash; z. B. <code>DE</code> oder <code>DE,AT,CH</code></li>
<li><b>minDays</b>: Minimale R&uuml;ckgabefrist in Tagen</li>
<li><b>maxDays</b>: Maximale R&uuml;ckgabefrist in Tagen (identisch zu minDays m&ouml;glich)</li>
<li><b>category</b>: R&uuml;ckgaberegel-Typ<br>
 &nbsp;&nbsp;&bull; <code>finite</code> &ndash; R&uuml;ckgabe m&ouml;glich und zeitlich begrenzt<br>
 &nbsp;&nbsp;&bull; <code>unlimited</code> &ndash; R&uuml;ckgabe zeitlich unbegrenzt<br>
 &nbsp;&nbsp;&bull; <code>not_permitted</code> &ndash; R&uuml;ckgabe nicht erlaubt</li>
<li><b>feeType</b>: Wer tr&auml;gt die R&uuml;cksendekosten?<br>
 &nbsp;&nbsp;&bull; <code>free</code> &ndash; H&auml;ndler tr&auml;gt die Kosten (FreeReturn)<br>
 &nbsp;&nbsp;&bull; <code>buyer</code> &ndash; Kunde tr&auml;gt die Kosten<br>
 &nbsp;&nbsp;&bull; <code>seller</code> &ndash; H&auml;ndler tr&auml;gt die Kosten</li>
<li><b>method</b>: R&uuml;ckgabeweg<br>
 &nbsp;&nbsp;&bull; <code>mail</code> &ndash; R&uuml;ckgabe per Versand<br>
 &nbsp;&nbsp;&bull; <code>store</code> &ndash; R&uuml;ckgabe im Ladengesch&auml;ft<br>
 &nbsp;&nbsp;&bull; <code>both</code> &ndash; R&uuml;ckgabe per Versand oder im Laden<br>
 &nbsp;&nbsp;&bull; <code>none</code> &ndash; Keine R&uuml;ckgabe m&ouml;glich</li>
</ul>
<p><strong>Beispiele:</strong></p>
<ol>
<li>Deutschland, 14&ndash;30 Tage R&uuml;ckgabefrist, kostenlos, per Versand:<br>
<code>DE|14|30|finite|free|mail</code></li>
<li>&Ouml;sterreich &amp; Schweiz, 14&ndash;30 Tage, Kunde zahlt R&uuml;ckversand:<br>
<code>AT,CH|14|30|finite|buyer|mail</code></li>
<li>Keine R&uuml;ckgabe f&uuml;r bestimmte L&auml;nder:<br>
<code>US|0|0|not_permitted|buyer|none</code></li>
</ol>',

  'MODULE_' . $modulname . '_PRICEVALID_DEFAULT_DAYS_TITLE' => 'Standard-G&uuml;ltigkeit f&uuml;r priceValidUntil (Tage)',
  'MODULE_' . $modulname . '_PRICEVALID_DEFAULT_DAYS_DESC'  => 'Wenn kein Ablaufdatum aus einem Sonderpreis vorhanden ist, wird <code>priceValidUntil</code> um diese Anzahl Tage in die Zukunft gesetzt. 0 = kein <code>priceValidUntil</code> setzen.',

  'MODULE_' . $modulname . '_UPDATE_AVAILABLE_TITLE' => ' <span style="font-weight:bold;color:#900;background:#ff6;border-radius:3px;padding:2px;border:1px solid #900;">Bitte Modulaktualisierung durchf&uuml;hren!</span>',
  'MODULE_' . $modulname . '_UPDATE_AVAILABLE_DESC'  => '',
  'MODULE_' . $modulname . '_UPDATE_FINISHED'        => 'Das Modul MITS JSON-LD wurde aktualisiert.',
  'MODULE_' . $modulname . '_UPDATE_ERROR'           => 'Fehler',
  'MODULE_' . $modulname . '_UPDATE_MODUL'           => 'Modul aktualisieren',
  'MODULE_' . $modulname . '_DELETE_MODUL'           => 'MITS JSON-LD komplett vom Server entfernen',
  'MODULE_' . $modulname . '_CONFIRM_DELETE_MODUL'   => 'M&ouml;chten sie das Modul MITS JSON-LD mit allen Dateien wirklich vom Server l&ouml;schen?',
  'MODULE_' . $modulname . '_DELETE_FINISHED'        => 'Das Modul MITS JSON-LD wurde vom Server gel&ouml;scht.',
);

foreach ($lang_array as $key => $val) {
    defined($key) || define($key, $val);
}