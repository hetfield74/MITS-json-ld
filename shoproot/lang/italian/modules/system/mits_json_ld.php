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
  'MODULE_' . $modulname . '_TITLE'        => 'MITS JSON-LD per modified eCommerce Shopsoftware <span style="white-space:nowrap;">© di <span style="padding:2px;background:#ffe;color:#6a9;font-weight:bold;">Hetfield (MerZ IT-SerVice)</span></span>',
  'MODULE_' . $modulname . '_DESCRIPTION'  => '
    <a href="https://www.merz-it-service.de/" target="_blank">
      <img src="' . (ENABLE_SSL === true ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG . DIR_WS_IMAGES . 'merz-it-service.png" border="0" alt="MerZ IT-SerVice" style="display:block;max-width:100%;height:auto;" />
    </a><br />
    <p style="font-size: larger">Questo modulo estende il tuo shop modified eCommerce con i markup JSON-LD raccomandati da Google.</p>
    <p>Il modulo supporta i seguenti tipi di markup:</p>
    <ul style="font-size: larger">
      <li>WebSite <small>name, alternateName, description, url e logo</small></li>
      <li>Organization <small>name, alternateName, description, url, logo e ContactPoints per customer service, technical support, billing support, sales</small></li>
      <li>LocalBusiness <small>name, image, url, telephone, address, geo, sameAs</small></li>
      <li>ContactPage <small>name, url, description</small></li>
      <li>Breadcrumb</li>
      <li>Product <small>name, image, description, brand, priceCurrency, priceValidUntil (fisso a 1 mese), price, url, itemCondition, availability, mpn, sku, gtin13 e reviews</small></li>
      <li>Review (Rating e aggregateRating) <small>ratingValue, worstRating, bestRating, author, datePublished, reviewBody</small></li>
      <li>Sitelink Searchbox</li>
    </ul>
    <p style="font-size: larger">Il modulo può essere ampliato e adattato secondo le tue esigenze. Per personalizzazioni individuali, contattaci direttamente.<br />
    <div style="text-align:center;">
      <small>La versione più recente del modulo è sempre disponibile su Github!</small><br />
      <a style="background:#6a9;color:#444" target="_blank" href="https://github.com/hetfield74/MITS-json-ld" class="button" onclick="this.blur();">MITS JSON-LD su Github</a>
    </div>
    <p>Per domande, problemi o richieste riguardanti questo modulo o altri aspetti relativi a modified eCommerce Shopsoftware, non esitare a contattarci:</p> 
    <div style="text-align:center;"><a style="background:#6a9;color:#444" target="_blank" href="https://www.merz-it-service.de/Kontakt.html" class="button" onclick="this.blur();">Pagina di contatto su MerZ-IT-SerVice.de</a></div>  
',

  'MODULE_' . $modulname . '_STATUS_TITLE' => 'Attivare il modulo?',
  'MODULE_' . $modulname . '_STATUS_DESC'  => 'Attivare il modulo MITS JSON-LD nel negozio?',

  'MODULE_' . $modulname . '_SHOW_BREADCRUMB_TITLE' => 'Attivare Breadcrumb?',
  'MODULE_' . $modulname . '_SHOW_BREADCRUMB_DESC'  => 'Attivare il markup JSON-LD per il Breadcrumb?',

  'MODULE_' . $modulname . '_SHOW_PRODUCT_TITLE' => 'Attivare Prodotti?',
  'MODULE_' . $modulname . '_SHOW_PRODUCT_DESC'  => 'Attivare il markup JSON-LD per i prodotti nella pagina dei dettagli?',

  'MODULE_' . $modulname . '_ENABLE_ATTRIBUTES_TITLE' => 'Visualizzare gli attributi nel JSON-LD',
  'MODULE_' . $modulname . '_ENABLE_ATTRIBUTES_DESC'  => 'Gli attributi del prodotto devono essere visualizzati come offerte?',

  'MODULE_' . $modulname . '_ENABLE_TAGS_TITLE' => 'Visualizzare le proprietà del prodotto nel JSON-LD',
  'MODULE_' . $modulname . '_ENABLE_TAGS_DESC'  => 'Le proprietà del prodotto (tag) devono essere visualizzate in additionalProperty?',

  'MODULE_' . $modulname . '_MAX_OFFERS_TITLE' => 'Numero massimo di offerte',
  'MODULE_' . $modulname . '_MAX_OFFERS_DESC'  => 'Previene problemi di memoria in caso di molti attributi. Predefinito: 100.',

  'MODULE_' . $modulname . '_ENABLE_MICRODATA_FIX_TITLE' => 'Attivare la correzione Microdata?',
  'MODULE_' . $modulname . '_ENABLE_MICRODATA_FIX_DESC'  => '<i>Rimuove gli attributi Microdata dal negozio utilizzando jQuery, nel caso in cui lo schema Microdata sia ancora presente nel template in uso. I dati strutturati duplicati (JSON-LD e Microdata) non sono l\'ideale in quanto possono portare a incongruenze.',

  'MODULE_' . $modulname . '_SHOW_PRODUCT_REVIEWS_TITLE' => 'Attivare le recensioni dei prodotti?',
  'MODULE_' . $modulname . '_SHOW_PRODUCT_REVIEWS_DESC'  => 'Attivare il markup JSON-LD per le recensioni nella pagina prodotto? Solo insieme al markup del prodotto.',

  'MODULE_' . $modulname . '_SHOW_PRODUCT_REVIEWS_INFO_TITLE' => 'Attivare la pagina dettagliata delle recensioni?',
  'MODULE_' . $modulname . '_SHOW_PRODUCT_REVIEWS_INFO_DESC'  => 'Attivare il markup JSON-LD per la pagina dei dettagli di una recensione?',

  'MODULE_' . $modulname . '_SHOW_SEARCHFIELD_TITLE' => 'Attivare Sitelinks Searchbox?',
  'MODULE_' . $modulname . '_SHOW_SEARCHFIELD_DESC'  => 'Attivare il markup JSON-LD per la Sitelinks Searchbox?',

  'MODULE_' . $modulname . '_SHOW_WEBSITE_TITLE' => 'Attivare WebSite?',
  'MODULE_' . $modulname . '_SHOW_WEBSITE_DESC'  => 'Attivare il markup JSON-LD per WebSite?',

  'MODULE_' . $modulname . '_SHOW_LOGO_TITLE' => 'Attivare Logo?',
  'MODULE_' . $modulname . '_SHOW_LOGO_DESC'  => 'Il logo viene utilizzato nei markup WebSite, Organization e LocalBusiness.',

  'MODULE_' . $modulname . '_LOGOFILE_TITLE' => 'Logo',
  'MODULE_' . $modulname . '_LOGOFILE_DESC'  => 'Il logo della tua azienda, inserire solo il nome del file senza percorso. Deve trovarsi nella cartella img del template utilizzato (standard: logo.gif).',

  'MODULE_' . $modulname . '_SHOW_ORGANISTATION_TITLE' => 'Attivare Organization?',
  'MODULE_' . $modulname . '_SHOW_ORGANISTATION_DESC'  => 'Attivare il markup JSON-LD per Organization?',

  'MODULE_' . $modulname . '_SHOW_CONTACT_TITLE' => 'Attivare ContactPage?',
  'MODULE_' . $modulname . '_SHOW_CONTACT_DESC'  => 'Attivare il markup JSON-LD per ContactPage?',

  'MODULE_' . $modulname . '_SHOW_LOCATION_TITLE' => 'Attivare LocalBusiness?',
  'MODULE_' . $modulname . '_SHOW_LOCATION_DESC'  => 'Attivare il markup JSON-LD per LocalBusiness?',

  'MODULE_' . $modulname . '_NAME_TITLE' => 'Nome azienda / sito web',
  'MODULE_' . $modulname . '_NAME_DESC'  => 'Il nome viene utilizzato nei markup WebSite, Organization e LocalBusiness.',

  'MODULE_' . $modulname . '_ALTERNATE_NAME_TITLE' => 'Nome alternativo azienda / sito web',
  'MODULE_' . $modulname . '_ALTERNATE_NAME_DESC'  => 'Il nome alternativo viene utilizzato nei markup WebSite, Organization e LocalBusiness.',

  'MODULE_' . $modulname . '_WEBSITE_DESCRIPTION_TITLE' => 'Descrizione azienda / sito web',
  'MODULE_' . $modulname . '_WEBSITE_DESCRIPTION_DESC'  => 'Utilizzata in WebSite, Organization e LocalBusiness. Se vuota, viene usata la meta-description standard.',

  'MODULE_' . $modulname . '_EMAIL_TITLE' => 'Indirizzo e-mail azienda / sito web',
  'MODULE_' . $modulname . '_EMAIL_DESC'  => 'Utilizzato nei markup WebSite, Organization e LocalBusiness.',

  'MODULE_' . $modulname . '_TELEPHONE_DEFAULT_TITLE' => 'Numero di telefono',
  'MODULE_' . $modulname . '_TELEPHONE_DEFAULT_DESC'  => 'Numero principale usato per Organization e LocalBusiness. Formato richiesto: +49-2722-631363',

  'MODULE_' . $modulname . '_TELEPHONE_SERVICE_TITLE' => 'Numero servizio clienti',
  'MODULE_' . $modulname . '_TELEPHONE_SERVICE_DESC'  => 'Numero per il servizio clienti. Formato richiesto: +49-2722-631363',

  'MODULE_' . $modulname . '_TELEPHONE_TECHNICAL_TITLE' => 'Numero supporto tecnico',
  'MODULE_' . $modulname . '_TELEPHONE_TECHNICAL_DESC'  => 'Numero per il supporto tecnico. Formato richiesto: +49-2722-631363',

  'MODULE_' . $modulname . '_TELEPHONE_BILLING_TITLE' => 'Numero reparto fatturazione',
  'MODULE_' . $modulname . '_TELEPHONE_BILLING_DESC'  => 'Numero per le domande di fatturazione. Formato richiesto: +49-2722-631363',

  'MODULE_' . $modulname . '_TELEPHONE_SALES_TITLE' => 'Numero reparto vendite',
  'MODULE_' . $modulname . '_TELEPHONE_SALES_DESC'  => 'Numero per il reparto vendite. Formato richiesto: +49-2722-631363',

  'MODULE_' . $modulname . '_FAX_TITLE' => 'Numero fax',
  'MODULE_' . $modulname . '_FAX_DESC'  => 'Numero fax dell’azienda. Formato richiesto: +49-2722-631400',

  'MODULE_' . $modulname . '_SOCIAL_MEDIA_TITLE' => 'Profili social media',
  'MODULE_' . $modulname . '_SOCIAL_MEDIA_DESC'  => 'Inserisci gli URL completi dei tuoi profili social. Separare più indirizzi con una virgola.',

  'MODULE_' . $modulname . '_LOCATION_STREETADDRESS_TITLE' => 'Via / numero civico',
  'MODULE_' . $modulname . '_LOCATION_STREETADDRESS_DESC'  => 'Via e numero per il markup LocalBusiness.',

  'MODULE_' . $modulname . '_LOCATION_ADDRESSLOCALITY_TITLE' => 'Città',
  'MODULE_' . $modulname . '_LOCATION_ADDRESSLOCALITY_DESC'  => 'Città per il markup LocalBusiness.',

  'MODULE_' . $modulname . '_LOCATION_POSTALCODE_TITLE' => 'CAP',
  'MODULE_' . $modulname . '_LOCATION_POSTALCODE_DESC'  => 'CAP utilizzato in LocalBusiness.',

  'MODULE_' . $modulname . '_LOCATION_ADDRESSCOUNTRY_TITLE' => 'Paese',
  'MODULE_' . $modulname . '_LOCATION_ADDRESSCOUNTRY_DESC'  => 'Paese in formato codice ISO-2 (es. DE per Germania).',

  'MODULE_' . $modulname . '_LOCATION_GEO_LATITUDE_TITLE' => 'Latitudine GEO',
  'MODULE_' . $modulname . '_LOCATION_GEO_LATITUDE_DESC'  => 'Opzionale. Lasciare vuoto se non utilizzato.',

  'MODULE_' . $modulname . '_LOCATION_GEO_LONGITUDE_TITLE' => 'Longitudine GEO',
  'MODULE_' . $modulname . '_LOCATION_GEO_LONGITUDE_DESC'  => 'Opzionale. Lasciare vuoto se non utilizzato.',

  'MODULE_' . $modulname . '_UPDATE_AVAILABLE_TITLE' => '<span style="font-weight:bold;color:#900;background:#ff6;padding:2px;border:1px solid #900;">Aggiornamento modulo necessario!</span>',
  'MODULE_' . $modulname . '_UPDATE_AVAILABLE_DESC'  => '',
  'MODULE_' . $modulname . '_UPDATE_FINISHED'        => 'Il modulo MITS JSON-LD è stato aggiornato.',
  'MODULE_' . $modulname . '_UPDATE_ERROR'           => 'Errore',
  'MODULE_' . $modulname . '_UPDATE_MODUL'           => 'Aggiorna modulo',
  'MODULE_' . $modulname . '_DELETE_MODUL'           => 'Rimuovere completamente MITS JSON-LD dal server',
  'MODULE_' . $modulname . '_CONFIRM_DELETE_MODUL'   => 'Vuoi davvero eliminare dal server il modulo MITS JSON-LD con tutti i suoi file?',
  'MODULE_' . $modulname . '_DELETE_FINISHED'        => 'Il modulo MITS JSON-LD è stato eliminato dal server.',
);

foreach ($lang_array as $key => $val) {
    defined($key) || define($key, $val);
}
