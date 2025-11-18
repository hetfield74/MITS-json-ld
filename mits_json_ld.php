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

if (defined('MODULE_MITS_JSON_LD_STATUS') && MODULE_MITS_JSON_LD_STATUS == 'true') {
    // Definitionen als Schalter
    defined('MODULE_MITS_JSON_LD_SHOW_BREADCRUMB') or define('MODULE_MITS_JSON_LD_SHOW_BREADCRUMB', 'true');
    defined('MODULE_MITS_JSON_LD_SHOW_PRODUCT') or define('MODULE_MITS_JSON_LD_SHOW_PRODUCT', 'true');
    defined('MODULE_MITS_JSON_LD_SHOW_PRODUCT_REVIEWS') or define('MODULE_MITS_JSON_LD_SHOW_PRODUCT_REVIEWS', 'true');
    defined('MODULE_MITS_JSON_LD_SHOW_PRODUCT_REVIEWS_INFO') or define('MODULE_MITS_JSON_LD_SHOW_PRODUCT_REVIEWS_INFO', 'true');
    defined('MODULE_MITS_JSON_LD_SHOW_SEARCHFIELD') or define('MODULE_MITS_JSON_LD_SHOW_SEARCHFIELD', 'false');
    defined('MODULE_MITS_JSON_LD_SHOW_WEBSITE') or define('MODULE_MITS_JSON_LD_SHOW_WEBSITE', 'false');
    defined('MODULE_MITS_JSON_LD_SHOW_LOGO') or define('MODULE_MITS_JSON_LD_SHOW_LOGO', 'false');
    defined('MODULE_MITS_JSON_LD_SHOW_ORGANISTATION') or define('MODULE_MITS_JSON_LD_SHOW_ORGANISTATION', 'false');
    defined('MODULE_MITS_JSON_LD_SHOW_CONTACT') or define('MODULE_MITS_JSON_LD_SHOW_CONTACT', 'false');
    defined('MODULE_MITS_JSON_LD_SHOW_LOCATION') or define('MODULE_MITS_JSON_LD_SHOW_LOCATION', 'false');

    // Definitionen mit Inhalten
    defined('MODULE_MITS_JSON_LD_LOGOFILE') or define('MODULE_MITS_JSON_LD_LOGOFILE', 'logo.gif');
    defined('MODULE_MITS_JSON_LD_NAME') or define('MODULE_MITS_JSON_LD_NAME', STORE_OWNER);
    defined('MODULE_MITS_JSON_LD_ALTERNATE_NAME') or define('MODULE_MITS_JSON_LD_ALTERNATE_NAME', STORE_NAME);
    defined('MODULE_MITS_JSON_LD_DESCRIPTION') or define('MODULE_MITS_JSON_LD_DESCRIPTION', META_DESCRIPTION);
    defined('MODULE_MITS_JSON_LD_EMAIL') or define('MODULE_MITS_JSON_LD_EMAIL', STORE_OWNER_EMAIL_ADDRESS);
    defined('MODULE_MITS_JSON_LD_TELEPHONE_SERVICE') or define('MODULE_MITS_JSON_LD_TELEPHONE_SERVICE', '');
    defined('MODULE_MITS_JSON_LD_TELEPHONE_TECHNICAL') or define('MODULE_MITS_JSON_LD_TELEPHONE_TECHNICAL', '');
    defined('MODULE_MITS_JSON_LD_TELEPHONE_BILLING') or define('MODULE_MITS_JSON_LD_TELEPHONE_BILLING', '');
    defined('MODULE_MITS_JSON_LD_TELEPHONE_SALES') or define('MODULE_MITS_JSON_LD_TELEPHONE_SALES', '');
    defined('MODULE_MITS_JSON_LD_TELEPHONE_DEFAULT') or define('MODULE_MITS_JSON_LD_TELEPHONE_DEFAULT', '');
    defined('MODULE_MITS_JSON_LD_FAX') or define('MODULE_MITS_JSON_LD_FAX', '');
    defined('MODULE_MITS_JSON_LD_SOCIAL_MEDIA') or define('MODULE_MITS_JSON_LD_SOCIAL_MEDIA', '');
    defined('MODULE_MITS_JSON_LD_LOCATION_STREETADDRESS') or define('MODULE_MITS_JSON_LD_LOCATION_STREETADDRESS', '');
    defined('MODULE_MITS_JSON_LD_LOCATION_ADDRESSLOCALITY') or define('MODULE_MITS_JSON_LD_LOCATION_ADDRESSLOCALITY', '');
    defined('MODULE_MITS_JSON_LD_LOCATION_POSTALCODE') or define('MODULE_MITS_JSON_LD_LOCATION_POSTALCODE', '');
    defined('MODULE_MITS_JSON_LD_LOCATION_ADDRESSCOUNTRY') or define('MODULE_MITS_JSON_LD_LOCATION_ADDRESSCOUNTRY', 'DE');
    defined('MODULE_MITS_JSON_LD_LOCATION_GEO_LATITUDE') or define('MODULE_MITS_JSON_LD_LOCATION_GEO_LATITUDE', '');
    defined('MODULE_MITS_JSON_LD_LOCATION_GEO_LONGITUDE') or define('MODULE_MITS_JSON_LD_LOCATION_GEO_LONGITUDE', '');
    
    require_once DIR_FS_INC . 'parse_multi_language_value.inc.php';

    function getCurrentUrl()
    {
        global $PHP_SELF, $request_type;
        return xtc_href_link(basename($PHP_SELF), xtc_get_all_get_params(array('cat', 'filter_id', 'filter', 'show', 'page')), $request_type);
    }

    $mits_snippet_social_media = '';
    if (MODULE_MITS_JSON_LD_SHOW_ORGANISTATION == 'true'
      || MODULE_MITS_JSON_LD_SHOW_LOCATION == 'true'
    ) {
        if (!empty(MODULE_MITS_JSON_LD_SOCIAL_MEDIA)) {
            $mits_snippet_social_array = explode(',', MODULE_MITS_JSON_LD_SOCIAL_MEDIA);
            if (is_array($mits_snippet_social_array)) {
                $mits_snippet_social_media = ',
          "sameAs": [';
                foreach ($mits_snippet_social_array as $mits_snippet_social_item) {
                    $mits_snippet_social_media .= '"' . trim($mits_snippet_social_item) . '",';
                }
                $mits_snippet_social_media = substr($mits_snippet_social_media, 0, -1);
                $mits_snippet_social_media .= ']';
            }
        }
    }

    $mits_snippet_searchfield = '';
    if (MODULE_MITS_JSON_LD_SHOW_SEARCHFIELD == 'true') {
        $mits_snippet_searchfield = ',
    "potentialAction": {
      "@type": "SearchAction",
      "target": "' . xtc_href_link(FILENAME_ADVANCED_SEARCH_RESULT, '', $request_type, false) . '?keywords={search_term_string}",
      "query-input": "required name=search_term_string"
    }
  ';
    }

    $mits_snippet_logo = '';
    $mits_snippet_image = '';
    if (MODULE_MITS_JSON_LD_SHOW_LOGO == 'true'
      && MODULE_MITS_JSON_LD_LOGOFILE != ''
    ) {
        $logo_file = '';
        if (is_file(DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/img/' . MODULE_MITS_JSON_LD_LOGOFILE)) {
            $logo_file = DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/img/' . MODULE_MITS_JSON_LD_LOGOFILE;
        } elseif (is_file(DIR_FS_CATALOG . 'images/' . MODULE_MITS_JSON_LD_LOGOFILE)) {
            $logo_file = DIR_FS_CATALOG . 'images/' . MODULE_MITS_JSON_LD_LOGOFILE;
        }
        if ($logo_file != '') {
            $mits_snippet_logo = ',
      "logo": "' . $logo_file . '"';
            $mits_snippet_image = ',
      "image": "' . $logo_file . '"';
        }
    }

    $mits_snippet_languages = '';
    $mits_snippet_other_contacts = '';
    $mits_snippet_contact = '';
    if (MODULE_MITS_JSON_LD_SHOW_ORGANISTATION == 'true'
      && MODULE_MITS_JSON_LD_SHOW_CONTACT == 'true'
      && isset($_SESSION['language_code'])
    ) {
        foreach ($lng->catalog_languages as $mits_snippet_lang => $mits_snippet_languages_item) {
            $mits_snippet_languages .= '"' . $mits_snippet_languages_item['name'] . '",';
        }
        $mits_snippet_languages = substr($mits_snippet_languages, 0, -1);

        if (!empty(MODULE_MITS_JSON_LD_TELEPHONE_TECHNICAL)) {
            $mits_snippet_other_contacts .= ',{
      "@type": "ContactPoint",
      "telephone": "' . parse_multi_language_value(MODULE_MITS_JSON_LD_TELEPHONE_TECHNICAL, $_SESSION['language_code']) . '",
      "contactType": "technical support",
      "areaServed": "' . strtoupper($_SESSION['language_code']) . '",
      "availableLanguage": [' . $mits_snippet_languages . ']
    }';
        }
        if (!empty(MODULE_MITS_JSON_LD_TELEPHONE_BILLING)) {
            $mits_snippet_other_contacts .= ',{
      "@type": "ContactPoint",
      "telephone": "' . parse_multi_language_value(MODULE_MITS_JSON_LD_TELEPHONE_BILLING, $_SESSION['language_code']) . '",
      "contactType": "billing support",
      "areaServed": "' . strtoupper($_SESSION['language_code']) . '",
      "availableLanguage": [' . $mits_snippet_languages . ']
    }';
        }
        if (!empty(MODULE_MITS_JSON_LD_TELEPHONE_SALES)) {
            $mits_snippet_other_contacts .= ',{
      "@type": "ContactPoint",
      "telephone": "' . parse_multi_language_value(MODULE_MITS_JSON_LD_TELEPHONE_SALES, $_SESSION['language_code']) . '",
      "contactType": "sales",
      "areaServed": "' . strtoupper($_SESSION['language_code']) . '",
      "availableLanguage": [' . $mits_snippet_languages . ']
    }';
        }
        $mits_snippet_contact = ',
    "contactPoint": [{
      "@type": "ContactPoint",
      "telephone": "' . parse_multi_language_value(MODULE_MITS_JSON_LD_TELEPHONE_SERVICE, $_SESSION['language_code']) . '",
      "contactType": "customer service",
      "areaServed": "' . strtoupper($_SESSION['language_code']) . '",
      "availableLanguage": [' . $mits_snippet_languages . ']
    }
    ' . $mits_snippet_other_contacts . '
    ,{
      "@type": "ContactPoint",
      "telephone": "' . parse_multi_language_value(MODULE_MITS_JSON_LD_TELEPHONE_DEFAULT, $_SESSION['language_code']) . '",
      "contactType": "customer service"
    }]
  ';
    }

    // Logo einbinden
    if (MODULE_MITS_JSON_LD_SHOW_WEBSITE == 'true') {
        echo '<script type="application/ld+json">
      {
        "@context": "https://schema.org",
        "@type": "WebSite",
        "name": "' . parse_multi_language_value(MODULE_MITS_JSON_LD_NAME, $_SESSION['language_code']) . '",
        "alternateName": "' . parse_multi_language_value(MODULE_MITS_JSON_LD_ALTERNATE_NAME, $_SESSION['language_code']) . '",
        "description": "' . parse_multi_language_value(MODULE_MITS_JSON_LD_DESCRIPTION, $_SESSION['language_code']) . '",
        "url": "' . xtc_href_link(FILENAME_DEFAULT, '', $request_type, false) . '"
        ' . $mits_snippet_image . $mits_snippet_searchfield . '
      }
    </script>';
    }

    if (MODULE_MITS_JSON_LD_SHOW_ORGANISTATION == 'true') {
        echo '<script type="application/ld+json">
      {
        "@context": "https://schema.org",
        "@type": "Organization",
        "@id": "' . xtc_href_link(FILENAME_DEFAULT, '', $request_type, false) . '",
        "name": "' . parse_multi_language_value(MODULE_MITS_JSON_LD_NAME, $_SESSION['language_code']) . '",
        "alternateName": "' . parse_multi_language_value(MODULE_MITS_JSON_LD_ALTERNATE_NAME, $_SESSION['language_code']) . '",
        "description": "' . parse_multi_language_value(MODULE_MITS_JSON_LD_DESCRIPTION, $_SESSION['language_code'])  . '",
        "url": "' . xtc_href_link(FILENAME_DEFAULT, '', $request_type, false) . '"
        ' . $mits_snippet_logo . $mits_snippet_image . $mits_snippet_contact . $mits_snippet_social_media . '
      }
    </script>';
    }

    // Location einbinden
    if (MODULE_MITS_JSON_LD_SHOW_LOCATION == 'true') {
        if (!empty(MODULE_MITS_JSON_LD_LOCATION_GEO_LATITUDE) && !empty(MODULE_MITS_JSON_LD_LOCATION_GEO_LONGITUDE)) {
            $mits_snippet_geo = ',
      "geo": {
        "@type": "GeoCoordinates",
        "latitude": ' . parse_multi_language_value(MODULE_MITS_JSON_LD_LOCATION_GEO_LATITUDE, $_SESSION['language_code']) . ',
        "longitude": ' . parse_multi_language_value(MODULE_MITS_JSON_LD_LOCATION_GEO_LONGITUDE, $_SESSION['language_code']) . '
      }';
        }
        echo '<script type="application/ld+json">
      {
        "@context": "https://schema.org",
        "@type": "LocalBusiness",
        "name": "' . parse_multi_language_value(MODULE_MITS_JSON_LD_NAME, $_SESSION['language_code']) . '",
        "@id": "' . xtc_href_link(FILENAME_DEFAULT, '', $request_type, false) . '",
        "url": "' . xtc_href_link(FILENAME_DEFAULT, '', $request_type, false) . '"
        ' . $mits_snippet_logo . $mits_snippet_image . ',
        "telephone": "' . parse_multi_language_value(MODULE_MITS_JSON_LD_TELEPHONE_DEFAULT, $_SESSION['language_code']) . '",
        "address": {
          "@type": "PostalAddress",
          "streetAddress": "' . parse_multi_language_value(MODULE_MITS_JSON_LD_LOCATION_STREETADDRESS, $_SESSION['language_code'])  . '",
          "addressLocality": "' . parse_multi_language_value(MODULE_MITS_JSON_LD_LOCATION_ADDRESSLOCALITY, $_SESSION['language_code'])  . '",
          "postalCode": "' . parse_multi_language_value(MODULE_MITS_JSON_LD_LOCATION_POSTALCODE, $_SESSION['language_code'])  . '",
          "addressCountry": "' . parse_multi_language_value(MODULE_MITS_JSON_LD_LOCATION_ADDRESSCOUNTRY, $_SESSION['language_code'])  . '"
        }
        ' . $mits_snippet_geo . $mits_snippet_social_media . '
      }
    </script>';
    }

    // Breadcrumb einbinden
    $mits_snippet_breadcrumb = '';
    if (MODULE_MITS_JSON_LD_SHOW_BREADCRUMB == 'true') {
        foreach ($breadcrumb->_trail as $mits_snippet_breadcrumb_items => $mits_snippet_breadcrumb_content) {
            $mit_snippet_breadcrum_link = (empty($mits_snippet_breadcrumb_content['link'])) ? getCurrentUrl() : $mits_snippet_breadcrumb_content['link'];
            $mits_snippet_breadcrumb .= '{
        "@type": "ListItem",
        "position": ' . ($mits_snippet_breadcrumb_items + 1) . ',
        "name": "' . str_replace(array('"', "'"), array('&quot;', '&apos;'), $mits_snippet_breadcrumb_content['title']) . '",
        "item": "' . $mit_snippet_breadcrum_link . '"
      },';
        }
        $mits_snippet_breadcrumb = substr($mits_snippet_breadcrumb, 0, -1);
        echo '<script type="application/ld+json">
      {
        "@context": "https://schema.org",
        "@type": "BreadcrumbList",
        "itemListElement": [' . $mits_snippet_breadcrumb . ']
      }
    </script>';
    }

    // Produktdaten inkl. Bewertungen
    if (basename($PHP_SELF) == FILENAME_PRODUCT_INFO
      && isset($product)
      && is_object($product)
      && $product->isProduct()
      && MODULE_MITS_JSON_LD_SHOW_PRODUCT == 'true'
    ) {
        $mits_snippet_single_price = $xtPrice->xtcGetPrice($product->data['products_id'], $format = true, 1, $product->data['products_tax_class_id'], $product->data['products_price'], 1);

        $mits_snippet_this_month = (int)date("m");
        $mits_snippet_this_year = (int)date("Y");
        $mits_snippet_this_day = (int)date("d");
        $mits_snippet_next_year = ($mits_snippet_this_month == 12) ? $mits_snippet_this_year + 1 : $mits_snippet_this_year;
        $mits_snippet_next_month = ($mits_snippet_this_month == 12) ? '01' : $mits_snippet_this_month + 1;
        $mits_snippet_date_is_valid = checkdate($mits_snippet_next_month, $mits_snippet_this_day, $mits_snippet_next_year);
        while ($mits_snippet_date_is_valid == false) {
            $mits_snippet_this_day--;
            $mits_snippet_date_is_valid = checkdate($mits_snippet_next_month, $mits_snippet_this_day, $mits_snippet_next_year);
        }
        $mits_snippet_new_expires_date = $mits_snippet_next_year . '-';
        $mits_snippet_new_expires_date .= (strlen($mits_snippet_next_month) == 1) ? '0' . $mits_snippet_next_month . '-' : $mits_snippet_next_month . '-';
        $mits_snippet_new_expires_date .= (strlen($mits_snippet_this_day) == 1) ? '0' . $mits_snippet_this_day : $mits_snippet_this_day;

        if (isset($product->data['availability']) && $product->data['availability'] == 0) {
            $mits_snippet_availability = "InStock";
        } elseif (isset($product->data['availability']) && $product->data['availability'] == 1) {
            $mits_snippet_availability = "InStock";
        } elseif (isset($product->data['availability']) && $product->data['availability'] == 2) {
            $mits_snippet_availability = "OutOfStock";
        } elseif (isset($product->data['availability']) && $product->data['availability'] == 3) {
            $mits_snippet_availability = "PreOrder";
        } else {
            $mits_snippet_availability = "InStock";
        }

        if (isset($product->data['googlecondition']) && $product->data['googlecondition'] == 0) {
            $mits_snippet_googlecondition = "NewCondition";
        } elseif (isset($product->data['googlecondition']) && $product->data['googlecondition'] == 1) {
            $mits_snippet_googlecondition = "UsedCondition";
        } elseif (isset($product->data['googlecondition']) && $product->data['googlecondition'] == 2) {
            $mits_snippet_googlecondition = "RefurbishedCondition";
        } else {
            $mits_snippet_googlecondition = "NewCondition";
        }

        $mits_snippet_products_description = strip_tags($product->data['products_description']);
        $mits_snippet_products_description = html_entity_decode($mits_snippet_products_description, ENT_NOQUOTES, $_SESSION['language_charset']);

        $mits_snippet_sku = (!empty($product->data['products_model'])) ? $product->data['products_model'] : $product->data['products_id'];

        $mits_snippet_rating = '';
        $mits_snippet_reviews = '';
        if (isset($_SESSION['customers_status']['customers_status_read_reviews'])
          && $_SESSION['customers_status']['customers_status_read_reviews'] == '1'
          && isset($products_reviews_count)
          && $products_reviews_count > 0
          && MODULE_MITS_JSON_LD_SHOW_PRODUCT_REVIEWS == 'true'
        ) {
            $mits_snippet_rating = '
      "aggregateRating": {
      "@type": "aggregateRating",
      "ratingValue": "' . $product->getReviewsAverage() . '",
      "reviewCount": "' . $products_reviews_count . '"
      },
    ';

            $mits_snippet_review_data = $product->getReviews($product->data['products_id']);
            if (is_array($mits_snippet_review_data)) {
                $mits_snippet_reviews = '"review": [';
                foreach ($mits_snippet_review_data as $mits_snippet_review) {
                    $mits_snippet_date = explode('.', $mits_snippet_review['DATE']);
                    $mits_snippet_reviews .= '
          {
            "@type": "Review",
            "reviewRating": {
              "@type": "Rating",
              "ratingValue": "' . str_replace(array('"', "'"), array('&quot;', '&apos;'), $mits_snippet_review['RATING_VOTE']) . '",
              "worstRating": "1",
              "bestRating": "5"
            },
            "author": {
              "@type": "Person",
              "name": "' . str_replace(array('"', "'"), array('&quot;', '&apos;'), $mits_snippet_review['AUTHOR']) . '"
            },
            "datePublished": "' . str_replace('-', '', $mits_snippet_date[2]) . '-' . str_replace('-', '', $mits_snippet_date[1]) . '-' . str_replace('-', '', $mits_snippet_date[0]) . '",
            "reviewBody": "' . str_replace(array('"', "'"), array('&quot;', '&apos;'), strip_tags(nl2br(encode_htmlspecialchars($mits_snippet_review['TEXT'])))) . '"
          },';
                }
                $mits_snippet_reviews = substr($mits_snippet_reviews, 0, -1);
                $mits_snippet_reviews .= '],';
            }
        }

        if (!empty($product->data['products_image'])) {
            $mits_snippet_images = '"' . xtc_href_link($product->productImage($product->data['products_image'], 'info'), '', $request_type, false) . '"';
            $mits_snippet_mo_images = xtc_get_products_mo_images($product->data['products_id']);
            if ($mits_snippet_mo_images !== false) {
                foreach ($mits_snippet_mo_images as $mits_snippet_img) {
                    $mits_snippet_images .= ',"' . xtc_href_link($product->productImage($mits_snippet_img['image_name'], 'info'), '', $request_type, false) . '"';
                }
            }
        } else {
            $mits_snippet_images = '';
        }

        $mits_snippet_manufacturer = '';
        if (isset($manufacturer['manufacturers_name']) && $manufacturer['manufacturers_name'] != '') {
            $mits_snippet_manufacturer = '"brand": {
          "@type": "Thing",
          "name": "' . str_replace(array('"', "'"), array('&quot;', '&apos;'), $manufacturer['manufacturers_name']) . '"
        },';
        }

        if (isset($metadata_array) && is_array($metadata_array) && isset($metadata_array['link'])) {
            $canonical_url = $metadata_array['link'];
        } else {
            $canonical_url = xtc_href_link(FILENAME_PRODUCT_INFO, xtc_product_link($product->data['products_id'], $product->data['products_name']));
        }

        echo '<script type="application/ld+json">
      {
        "@context": "https://schema.org/",
        "@type": "Product",
        "id": "' . $product->data['products_id'] . '",
        "sku": "' . $mits_snippet_sku . '",
        "gtin13": "' . $product->data['products_ean'] . '",
        "mpn": "' . $product->data['products_manufacturers_model'] . '",
        "image": [' . $mits_snippet_images . '],
        "name": "' . str_replace(array('"', "'"), array('&quot;', '&apos;'), $product->data['products_name']) . '",
        "description": "' . str_replace(array('"', "'"), array('&quot;', '&apos;'), $mits_snippet_products_description) . '",
        ' . $mits_snippet_manufacturer . $mits_snippet_rating . $mits_snippet_reviews . '
        "offers": {
          "@type": "Offer",
          "priceCurrency": "' . $xtPrice->actualCurr . '",
          "priceValidUntil": "' . $mits_snippet_new_expires_date . '",
          "price": "' . number_format($mits_snippet_single_price['plain'], 2, '.', '') . '",
          "url": "' . $canonical_url . '",
          "itemCondition": "https://schema.org/' . $mits_snippet_googlecondition . '",
          "availability": "https://schema.org/' . $mits_snippet_availability . '"
        }
      }
    </script>';
    }

    // Bewertungsdetailseite
    if (basename($PHP_SELF) == FILENAME_PRODUCT_REVIEWS_INFO
      && MODULE_MITS_JSON_LD_SHOW_PRODUCT_REVIEWS_INFO == 'true'
    ) {
        if (isset($reviews) && is_array($reviews) && count($reviews) > 0) {
            $product_reviews = $reviews;
        }
        if (isset($product_reviews) && is_array($product_reviews) && count($product_reviews) > 0) {
            $products_reviews_count = $product->getReviewsCount($product_reviews['products_id']);
            if ($_SESSION['customers_status']['customers_status_read_reviews'] == '1' && $products_reviews_count > 0 && MODULE_MITS_JSON_LD_SHOW_PRODUCT_REVIEWS_INFO == 'true') {
                $mits_snippet_date = explode('.', $product_reviews['date_added']);
                $mits_snippet_review_info = '<script type="application/ld+json">    
        {
          "@context": "https://schema.org",
          "@type": "Review",
          "reviewRating": {
          "@type": "AggregateRating",
          "ratingValue": "' . $product_reviews['reviews_rating'] . '",
          "ratingCount": "' . $products_reviews_count . '",
          "reviewCount": "' . $products_reviews_count . '",
          "worstRating": "1",
          "bestRating": "5"
        },
        "itemReviewed": {
        "@context": "https://schema.org",
        "@type": "Product",
        "name": "' . str_replace(array('"', "'"), array('&quot;', '&apos;'), $product_reviews['products_name']) . '"
        },
        "author": {
          "@context": "https://schema.org",
          "@type": "Person",
          "name": "' . str_replace(array('"', "'"), array('&quot;', '&apos;'), $product_reviews['customers_name']) . '"
        },
        "datePublished": "' . $product_reviews['date_added'] . '",
        "reviewBody": "' . str_replace(array('"', "'"), array('&quot;', '&apos;'), strip_tags(nl2br(encode_htmlspecialchars($product_reviews['reviews_text'])))) . '"
        }
      </script>';
                echo $mits_snippet_review_info;
            }
        }
    }

    // ContactPage
    if (basename($PHP_SELF) == FILENAME_CONTENT
      && isset($_GET['coID'])
      && $_GET['coID'] == 7
      && MODULE_MITS_JSON_LD_SHOW_CONTACT == 'true'
      && isset($shop_content_data['content_title'])
    ) {
        $meta_descr = isset($metadata_array) && is_array($metadata_array) && isset($metadata_array['description']) ? $metadata_array['description'] : '';
        $mits_snippet_contactpage = '<script type="application/ld+json">    
      {
        "@context": "https://schema.org",
        "@type": "ContactPage",
        "url": "' . xtc_href_link(FILENAME_CONTENT, 'coID=7', 'SSL', false) . '",
        "name": "' . strip_tags($shop_content_data['content_title']) . '",
        "description": "' . str_replace(array('"', "'"), array('&quot;', '&apos;'), strip_tags(decode_htmlentities($meta_descr))) . '"          
        ' . $mits_snippet_contact . '          
      }
    </script>';
        echo $mits_snippet_contactpage;
    }
}
