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

if (!defined('MODULE_MITS_JSON_LD_STATUS') || MODULE_MITS_JSON_LD_STATUS !== 'true') {
    return;
}

require_once DIR_FS_INC . 'parse_multi_language_value.inc.php';

$mitsJsonLdGraph = [];

/**
 * @param $const
 * @return bool
 */
function mits_flag($const): bool
{
    return defined($const) && constant($const) === 'true';
}

/**
 * @param $value
 * @return mixed
 */
function mits_ml($value): mixed
{
    return parse_multi_language_value($value, $_SESSION['language_code']);
}

/**
 * Helper: Knoten zum @graph hinzufügen
 */
function mits_graph_add(array $node)
{
    global $mitsJsonLdGraph;
    if (!empty($node)) {
        $mitsJsonLdGraph[] = $node;
    }
}

/**
 * Helper: kompletten @graph ausgeben
 */
function mits_graph_output()
{
    global $mitsJsonLdGraph;

    if (empty($mitsJsonLdGraph)) {
        return;
    }

    $payload = [
      '@context' => 'https://schema.org',
      '@graph'   => $mitsJsonLdGraph,
    ];

    echo '<script type="application/ld+json">'
      . json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
      . '</script>';
}

/**
 * @param array $data
 * @return void
 */
function mits_output_json(array $data): void
{
    echo '<script type="application/ld+json">' .
      json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) .
      '</script>';
}

/**
 * @param array $data
 * @return array
 */
function mits_jsonld_clean(array $data): array
{
    foreach ($data as $key => $value) {
        if ($value === null || $value === '' || $value === []) {
            unset($data[$key]);
            continue;
        }

        if (is_array($value)) {
            $data[$key] = mits_jsonld_clean($value);

            if ($data[$key] === []) {
                unset($data[$key]);
            }
        }
    }
    return $data;
}

/**
 * @return string|null
 */
function mits_get_logo(): ?string
{
    if (!mits_flag('MODULE_MITS_JSON_LD_SHOW_LOGO') || MODULE_MITS_JSON_LD_LOGOFILE === '') {
        return null;
    }

    $paths = [
      'templates/' . CURRENT_TEMPLATE . '/img/' . MODULE_MITS_JSON_LD_LOGOFILE,
      'images/' . MODULE_MITS_JSON_LD_LOGOFILE,
    ];

    foreach ($paths as $p) {
        if (is_file(DIR_FS_CATALOG . $p)) {
            return DIR_WS_BASE . $p;
        }
    }

    return null;
}

/**
 * @param $productId
 * @return array
 */
function mits_build_additional_properties($productId): array
{
    if (
      !defined('TABLE_PRODUCTS_TAGS') ||
      !defined('TABLE_PRODUCTS_TAGS_OPTIONS') ||
      !defined('TABLE_PRODUCTS_TAGS_VALUES') ||
      !defined('ADD_TAGS_SELECT')
    ) {
        return [];
    }

    $sql = "
        SELECT " . ADD_TAGS_SELECT . "
               pto.options_id,
               pto.options_name,
               pto.options_description,
               pto.sort_order AS options_sort_order,
               pto.options_content_group,
               ptv.values_id,
               ptv.values_name,
               ptv.values_description,
               ptv.sort_order AS values_sort_order,
               ptv.values_image,
               ptv.values_content_group
          FROM " . TABLE_PRODUCTS_TAGS . " pt
          JOIN " . TABLE_PRODUCTS_TAGS_OPTIONS . " pto
               ON pt.options_id = pto.options_id
              AND pto.status = '1'
              AND pto.languages_id = '" . (int)$_SESSION['languages_id'] . "'
          JOIN " . TABLE_PRODUCTS_TAGS_VALUES . " ptv
               ON ptv.values_id = pt.values_id
              AND ptv.status = '1'
              AND ptv.languages_id = '" . (int)$_SESSION['languages_id'] . "'
         WHERE pt.products_id = '" . (int)$productId . "'
      ORDER BY pt.sort_order,
               pt.options_id,
               pto.sort_order,
               pto.options_name,
               ptv.sort_order,
               ptv.values_name
    ";

    $tags_query = xtDBquery($sql);
    if (xtc_db_num_rows($tags_query, true) <= 0) {
        return [];
    }

    $tags_content = [];
    while ($row = xtc_db_fetch_array($tags_query, true)) {
        if (!isset($tags_content[$row['options_id']])) {
            $tags_content[$row['options_id']] = [
              'OPTIONS_NAME'        => $row['options_name'],
              'OPTIONS_ID'          => $row['options_id'],
              'OPTIONS_SORT_ORDER'  => $row['options_sort_order'],
              'OPTIONS_DESCRIPTION' => $row['options_description'],
              'DATA'                => [],
            ];
        }

        $tags_content[$row['options_id']]['DATA'][] = [
          'VALUES_NAME'        => $row['values_name'],
          'VALUES_ID'          => $row['values_id'],
          'VALUES_SORT_ORDER'  => $row['values_sort_order'],
          'VALUES_DESCRIPTION' => $row['values_description'],
        ];
    }

    $additional = [];
    foreach ($tags_content as $tag_option) {
        $value = null;

        if (count($tag_option['DATA']) > 1) {
            $value = [];
            foreach ($tag_option['DATA'] as $tag_value) {
                $value[] = $tag_value['VALUES_NAME'];
            }
        } elseif (!empty($tag_option['DATA'][0]['VALUES_NAME'])) {
            $value = $tag_option['DATA'][0]['VALUES_NAME'];
        }

        if ($value === null) {
            continue;
        }

        $entry = [
          '@type' => 'PropertyValue',
          'name'  => $tag_option['OPTIONS_NAME'],
          'value' => $value,
        ];

        if (!empty($tag_option['OPTIONS_DESCRIPTION'])) {
            $entry['description'] = preg_replace(
              "/\n+/",
              "\n",
              strip_tags($tag_option['OPTIONS_DESCRIPTION'])
            );
        }

        $additional[] = $entry;
    }

    return $additional;
}

/**
 * @param $product
 * @return bool
 */
function mits_jsonld_attributes_enabled_for_product($product)
{
    $globalEnabled =
      defined('MODULE_MITS_JSON_LD_ENABLE_ATTRIBUTES') &&
      MODULE_MITS_JSON_LD_ENABLE_ATTRIBUTES === 'true';

    if (!isset($product->data['mits_jsonld_attributes_enabled'])) {
        return $globalEnabled;
    }

    $val = $product->data['mits_jsonld_attributes_enabled'];

    if (is_string($val)) {
        $val = strtolower(trim($val));
        if ($val === 'true' || $val === '1') {
            return true;
        }
        return false;
    }

    return (bool)$val;
}


/**
 * @param $product
 * @return bool
 */
function mits_jsonld_tags_enabled_for_product($product): bool
{
    $globalEnabled = defined('MODULE_MITS_JSON_LD_ENABLE_TAGS')
      && MODULE_MITS_JSON_LD_ENABLE_TAGS === 'true';

    if (!isset($product->data['mits_jsonld_tags_enabled'])) {
        return $globalEnabled;
    }

    $val = $product->data['mits_jsonld_tags_enabled'];

    if (is_string($val)) {
        $val = strtolower(trim($val));
        if ($val === 'true' || $val === '1') {
            return true;
        }
        return false;
    }

    return (bool)$val;
}

/**
 * @param float $price
 * @return string
 */
function mits_jsonld_price_format($price): string
{
    $price = (string)$price;

    if (function_exists('bcadd')) {
        return bcadd($price, '0', 2);
    }

    return number_format((double)$price, 2, '.', '');
}

/**
 * @param $product
 * @param array $productDataArray
 * @return array|mixed
 */
function mits_build_offers_for_product($product, array $productDataArray): mixed
{
    global $xtPrice;

    $maxOffers = 100;
    if (defined('MODULE_MITS_JSON_LD_MAX_OFFERS') && (int)MODULE_MITS_JSON_LD_MAX_OFFERS > 0) {
        $maxOffers = (int)MODULE_MITS_JSON_LD_MAX_OFFERS;
    }

    $useAttributes = mits_jsonld_attributes_enabled_for_product($product)
      && method_exists($product, 'getAttributesCount')
      && $product->getAttributesCount() > 0;

    $offers = [];
    $offerIndex = 0;

    if ($useAttributes) {
        $attributesByOptionId = [];

        $attributesQuery = xtDBquery(
          "
            SELECT pa.*, pav.products_options_values_name
              FROM " . TABLE_PRODUCTS_ATTRIBUTES . " AS pa
              JOIN " . TABLE_PRODUCTS_OPTIONS_VALUES . " AS pav
                ON pav.products_options_values_id = pa.options_values_id
               AND pav.language_id = " . (int)$_SESSION['languages_id'] . "
             WHERE pa.products_id = " . (int)$product->data['products_id'] . "
             ORDER BY pa.options_id, pa.sortorder
        "
        );

        while ($row = xtc_db_fetch_array($attributesQuery, true)) {
            if (!isset($attributesByOptionId[$row['options_id']])) {
                $attributesByOptionId[$row['options_id']] = [];
            }
            $attributesByOptionId[$row['options_id']][] = $row;
        }

        if (!empty($attributesByOptionId)) {
            $optionGroups = array_values($attributesByOptionId);

            $buildCombinationOffers = function (
              array $optionGroups,
              int $currentOptionIndex,
              array $currentCombination,
              array &$offers,
              int &$offerIndex,
              int $maxOffers,
              $product,
              array $productDataArray
            ) use (&$buildCombinationOffers, $xtPrice) {
                if ($offerIndex >= $maxOffers) {
                    return;
                }

                if ($currentOptionIndex >= count($optionGroups)) {
                    $offers[] = mits_build_single_variant_offer_from_combination(
                      $currentCombination,
                      $product,
                      $productDataArray,
                      $xtPrice
                    );
                    $offerIndex++;
                    return;
                }

                $currentGroup = $optionGroups[$currentOptionIndex];

                foreach ($currentGroup as $attributeRow) {
                    if ($offerIndex >= $maxOffers) {
                        break;
                    }

                    $nextCombination = $currentCombination;
                    $nextCombination[] = $attributeRow;

                    $buildCombinationOffers(
                      $optionGroups,
                      $currentOptionIndex + 1,
                      $nextCombination,
                      $offers,
                      $offerIndex,
                      $maxOffers,
                      $product,
                      $productDataArray
                    );
                }
            };

            $buildCombinationOffers(
              $optionGroups,
              0,
              [],
              $offers,
              $offerIndex,
              $maxOffers,
              $product,
              $productDataArray
            );
        }
    }

    if (empty($offers)) {
        $offers[] = mits_build_single_base_offer_without_attributes($product, $productDataArray, $xtPrice);
    }

    if (count($offers) === 1) {
        return $offers[0];
    }

    $allPrices = [];
    foreach ($offers as $offer) {
        if (isset($offer['price'])) {
            $allPrices[] = $offer['price'];
        }
    }

    if (empty($allPrices)) {
        return $offers[0];
    }

    $currency = $_SESSION['currency'] ?? ($xtPrice->actualCurr ?? 'EUR');

    return [
      '@type'         => 'AggregateOffer',
      'priceCurrency' => $currency,
      'lowPrice'      => mits_jsonld_price_format(min($allPrices)),
      'highPrice'     => mits_jsonld_price_format(max($allPrices)),
      'offerCount'    => count($offers),
      'offers'        => $offers,
    ];
}

/**
 * @param array $attributeCombination
 * @param $product
 * @param array $productDataArray
 * @param bool $sumBaseUnitQuantities
 * @param $xtPrice
 * @return array
 */
function mits_build_single_variant_offer_from_combination(
  array $attributeCombination,
  $product,
  array $productDataArray,
  $xtPrice
): array {
    $variantStockQuantity = $productDataArray['PRODUCTS_QUANTITY'] ?? $product->data['products_quantity'];
    $variantBaseUnitQuantity = (double)($product->data['products_vpe_value'] ?? 0);
    $variantBasePrice = (float)($productDataArray['PRODUCTS_PRICE_ARRAY'][0]['PRODUCTS_PRICE_PLAIN'] ?? $product->data['products_price']);

    $usedOptionValueIds = [];
    $optionValueNames = [];
    $optionModelParts = [];

    $variantEan = $productDataArray['PRODUCTS_EAN'] ?? ($product->data['products_ean'] ?? '');
    $hasAttributeEan = false;

    foreach ($attributeCombination as $attributeRow) {
        $usedOptionValueIds[$attributeRow['options_id']] = $attributeRow['options_values_id'];

        if (!empty($attributeRow['products_options_values_name'])) {
            $optionValueNames[] = $attributeRow['products_options_values_name'];
        }

        if (!empty($attributeRow['attributes_model'])) {
            $optionModelParts[] = $attributeRow['attributes_model'];
        }

        if (isset($attributeRow['attributes_stock']) && $attributeRow['attributes_stock'] !== '') {
            $qty = (float)$attributeRow['attributes_stock'];
            if ($variantStockQuantity === null) {
                $variantStockQuantity = $qty;
            } else {
                $variantStockQuantity = min($variantStockQuantity, $qty);
            }
        }

        if (!empty($attributeRow['options_values_price'])) {
            $attrPrice = (float)$xtPrice->xtcFormat(
              $attributeRow['options_values_price'],
              false,
              $product->data['products_tax_class_id']
            );

            switch ($attributeRow['price_prefix']) {
                case '-':
                    $variantBasePrice -= $attrPrice;
                    break;
                case '=':
                    $variantBasePrice = $attrPrice;
                    break;
                default: // '+'
                    $variantBasePrice += $attrPrice;
                    break;
            }
        }

        if (!empty($attributeRow['attributes_ean'])) {
            $variantEan = $attributeRow['attributes_ean'];
            $hasAttributeEan = true;
        }

        if (!empty($attributeRow['attributes_vpe_value'])) {
            $attrVpe = (float)$attributeRow['attributes_vpe_value'];
            switch ($attributeRow['weight_prefix']) {
                case '-':
                    $variantBaseUnitQuantity -= $attrVpe;
                    break;
                case '=':
                    $variantBaseUnitQuantity = $attrVpe;
                    break;
                default:
                    $variantBaseUnitQuantity += $attrVpe;
                    break;
            }
        }
    }

    if (
      isset($product->data['products_discount']) &&
      (float)$product->data['products_discount'] != 0 &&
      !empty($_SESSION['customers_status']['customers_status_discount_attributes'])
    ) {
        $variantBasePrice = $variantBasePrice * (100 - (float)$product->data['products_discount']) / 100;
    }

    if ($variantStockQuantity === null) {
        $variantStockQuantity = $product->data['products_quantity'];
    }
    if (count($attributeCombination) === 1 && !$hasAttributeEan) {
        $variantStockQuantity = $product->data['products_quantity'];
    }
    if ($variantStockQuantity < 0) {
        $variantStockQuantity = 0;
    }

    $availability = ($variantStockQuantity <= 0 && mits_flag('STOCK_CHECK'))
      ? 'https://schema.org/OutOfStock'
      : 'https://schema.org/InStock';

    $productIdForLink = $product->data['products_id'];
    if (!empty($usedOptionValueIds)) {
        $attributeSignatureParts = [];
        foreach ($usedOptionValueIds as $optionId => $valueId) {
            $attributeSignatureParts[] = '{' . $optionId . '}' . $valueId;
        }
        if (!empty($attributeSignatureParts)) {
            $productIdForLink .= implode('', $attributeSignatureParts);
        }
    }

    $offerName = $productDataArray['PRODUCTS_NAME'] ?? $product->data['products_name'];
    if (!empty($optionValueNames)) {
        $offerName .= ' ' . implode(' ', $optionValueNames);
    }

    $offerSku = $productDataArray['PRODUCTS_MODEL'] ?? $product->data['products_model'];
    if (!empty($optionModelParts)) {
        $offerSku .= '-' . implode('-', $optionModelParts);
    }

    $offerUrl = xtc_href_link(
      FILENAME_PRODUCT_INFO,
      xtc_product_link($productIdForLink),
      'NONSSL',
      false,
      empty($usedOptionValueIds)
    );

    $currencyCode = $_SESSION['currency'] ?? ($xtPrice->actualCurr ?? 'EUR');
    $finalPrice = mits_jsonld_price_format($variantBasePrice);

    $priceSpecification = [
      [
        '@type'                 => 'UnitPriceSpecification',
        'price'                 => $finalPrice,
        'priceCurrency'         => $currencyCode,
        'valueAddedTaxIncluded' => true,
        'priceType'             => 'https://schema.org/RegularPrice',
      ]
    ];

    $eligibleQuantity = null;

    if (!empty($product->data['products_vpe_status'])
      && $variantBaseUnitQuantity > 0
      && $finalPrice > 0
    ) {
        $baseUnitName = xtc_get_vpe_name($product->data['products_vpe']);

        $priceSpecification[] = [
          '@type'                 => 'UnitPriceSpecification',
          'price'                 => $finalPrice,
          'priceCurrency'         => $currencyCode,
          'valueAddedTaxIncluded' => true,
          'referenceQuantity'     => [
            '@type'    => 'QuantitativeValue',
            'value'    => 1,
            'unitText' => $baseUnitName,
          ],
          'description'           => $xtPrice->xtcFormat($finalPrice * (1 / $variantBaseUnitQuantity), true) . TXT_PER . $baseUnitName,
        ];

        $eligibleQuantity = [
          '@type'    => 'QuantitativeValue',
          'value'    => $variantBaseUnitQuantity,
          'unitText' => $baseUnitName,
        ];
    }

    $sellerName = defined('META_COMPANY') ? META_COMPANY : STORE_NAME;

    $offer = [
      '@type'              => 'Offer',
      '@id'                => $offerUrl . '#offer-' . strtolower(preg_replace('/[^a-z0-9]+/', '-', $offerSku)),
      'name'               => $offerName,
      'sku'                => $offerSku,
      'url'                => $offerUrl,
      'priceCurrency'      => $currencyCode,
      'price'              => $finalPrice,
      'itemCondition'      => 'https://schema.org/NewCondition',
      'availability'       => $availability,
      'seller'             => [
        '@type' => 'Organization',
        'name'  => $sellerName,
      ],
      'priceSpecification' => $priceSpecification,
    ];

    if (!empty($variantEan)) {
        $offer['gtin'] = $variantEan;
    }
    if ($eligibleQuantity !== null) {
        $offer['eligibleQuantity'] = $eligibleQuantity;
    }

    return $offer;
}

/**
 * @param $product
 * @param array $productDataArray
 * @param $xtPrice
 * @return array
 */
function mits_build_single_base_offer_without_attributes($product, array $productDataArray, $xtPrice): array
{
    $currencyCode = $_SESSION['currency'] ?? ($xtPrice->actualCurr ?? 'EUR');
    $basePricePlain = ($productDataArray['PRODUCTS_PRICE_ARRAY'][0]['PRODUCTS_PRICE_PLAIN'] ?? $product->data['products_price']);
    $basePrice = mits_jsonld_price_format($basePricePlain);

    $priceSpecification = [];

    if (!empty($productDataArray['PRODUCTS_PRICE_ARRAY'][0]['PRODUCTS_PRICE_FLAG'])
      && stripos($productDataArray['PRODUCTS_PRICE_ARRAY'][0]['PRODUCTS_PRICE_FLAG'], 'special') !== false
    ) {
        $oldPrice = mits_jsonld_price_format($productDataArray['PRODUCTS_PRICE_ARRAY'][0]['PRODUCTS_PRICE_OLD_PRICE_PLAIN']);
        $priceSpecification[] = [
          '@type'                 => 'UnitPriceSpecification',
          'price'                 => $oldPrice,
          'priceCurrency'         => $currencyCode,
          'valueAddedTaxIncluded' => true,
          'priceType'             => 'https://schema.org/StrikethroughPrice',
        ];

        $currentSpec = [
          '@type'                 => 'UnitPriceSpecification',
          'price'                 => $basePrice,
          'priceCurrency'         => $currencyCode,
          'valueAddedTaxIncluded' => true,
          'priceType'             => 'https://schema.org/RegularPrice',
        ];
        if (!empty($productDataArray['PRODUCTS_PRICE_ARRAY'][0]['PRODUCTS_PRICE_EXPIRES_DATE'])) {
            $currentSpec['validThrough'] = date(
              'Y-m-d',
              strtotime($productDataArray['PRODUCTS_PRICE_ARRAY'][0]['PRODUCTS_PRICE_EXPIRES_DATE'])
            );
        }
        $priceSpecification[] = $currentSpec;
    } else {
        $priceSpecification[] = [
          '@type'                 => 'UnitPriceSpecification',
          'price'                 => $basePrice,
          'priceCurrency'         => $currencyCode,
          'valueAddedTaxIncluded' => true,
          'priceType'             => 'https://schema.org/RegularPrice',
        ];
    }

    $eligibleQuantity = null;
    if (!empty($product->data['products_vpe_status'])
      && !empty($product->data['products_vpe_value'])
      && $product->data['products_vpe_value'] != 0.0
      && $basePricePlain > 0
    ) {
        $baseUnitName = xtc_get_vpe_name($product->data['products_vpe']);
        $priceSpecification[] = [
          '@type'                 => 'UnitPriceSpecification',
          'price'                 => $basePrice,
          'priceCurrency'         => $currencyCode,
          'valueAddedTaxIncluded' => true,
          'referenceQuantity'     => [
            '@type'    => 'QuantitativeValue',
            'value'    => 1,
            'unitText' => $baseUnitName,
          ],
          'description'           => $xtPrice->xtcFormat(
              $basePricePlain * (1 / $product->data['products_vpe_value']),
              true
            ) . TXT_PER . $baseUnitName,
        ];
        $eligibleQuantity = [
          '@type'    => 'QuantitativeValue',
          'value'    => $product->data['products_vpe_value'],
          'unitText' => $baseUnitName,
        ];
    }

    $availability = ($productDataArray['PRODUCTS_QUANTITY'] <= 0 && mits_flag('STOCK_CHECK'))
      ? 'https://schema.org/OutOfStock'
      : 'https://schema.org/InStock';

    $sellerName = defined('META_COMPANY') ? META_COMPANY : STORE_NAME;

    $offerUrl = xtc_href_link(
      FILENAME_PRODUCT_INFO,
      xtc_product_link($product->data['products_id']),
      'NONSSL',
      false
    );

    $offer = [
      '@type'              => 'Offer',
      '@id'                => $offerUrl . '#offer',
      'sku'                => $productDataArray['PRODUCTS_MODEL'] ?? $product->data['products_model'],
      'url'                => $offerUrl,
      'priceCurrency'      => $currencyCode,
      'price'              => $basePrice,
      'itemCondition'      => 'https://schema.org/NewCondition',
      'availability'       => $availability,
      'seller'             => [
        '@type' => 'Organization',
        'name'  => $sellerName,
      ],
      'priceSpecification' => $priceSpecification,
    ];

    if (!empty($productDataArray['PRODUCTS_MANUFACTURERS_MODEL'])) {
        $offer['mpn'] = $productDataArray['PRODUCTS_MANUFACTURERS_MODEL'];
    }
    if (!empty($productDataArray['PRODUCTS_EAN'])) {
        $offer['gtin'] = $productDataArray['PRODUCTS_EAN'];
    }
    if ($eligibleQuantity !== null) {
        $offer['eligibleQuantity'] = $eligibleQuantity;
    }

    return $offer;
}


$logo = mits_get_logo();

/* WebSite */
if (mits_flag('MODULE_MITS_JSON_LD_SHOW_WEBSITE')) {
    $webSite = [
      '@type'         => 'WebSite',
      'name'          => mits_ml(MODULE_MITS_JSON_LD_NAME),
      'alternateName' => mits_ml(MODULE_MITS_JSON_LD_ALTERNATE_NAME),
      'description'   => mits_ml(MODULE_MITS_JSON_LD_DESCRIPTION),
      'url'           => xtc_href_link(FILENAME_DEFAULT, '', $request_type, false),
    ];

    if ($logo) {
        $webSite['image'] = $logo;
    }

    if (mits_flag('MODULE_MITS_JSON_LD_SHOW_SEARCHFIELD')) {
        $webSite['potentialAction'] = [
          '@type'       => 'SearchAction',
          'target'      => xtc_href_link(FILENAME_ADVANCED_SEARCH_RESULT, '', $request_type, false) . '?keywords={search_term_string}',
          'query-input' => 'required name=search_term_string',
        ];
    }

    $webSite = mits_jsonld_clean($webSite);
    mits_graph_add($webSite);
}

/* Organization */
if (mits_flag('MODULE_MITS_JSON_LD_SHOW_ORGANISTATION')) {
    global $lng;

    $org = [
      '@type'         => 'Organization',
      '@id'           => xtc_href_link(FILENAME_DEFAULT, '', $request_type, false),
      'name'          => mits_ml(MODULE_MITS_JSON_LD_NAME),
      'alternateName' => mits_ml(MODULE_MITS_JSON_LD_ALTERNATE_NAME),
      'description'   => mits_ml(MODULE_MITS_JSON_LD_DESCRIPTION),
      'url'           => xtc_href_link(FILENAME_DEFAULT, '', $request_type, false),
    ];

    if ($logo) {
        $org['logo'] = $logo;
        $org['image'] = $logo;
    }

    if (mits_flag('MODULE_MITS_JSON_LD_SHOW_CONTACT') && isset($lng->catalog_languages)) {
        $langs = array_map(fn($x) => $x['name'], $lng->catalog_languages);
        $contacts = [];

        $map = [
          'MODULE_MITS_JSON_LD_TELEPHONE_SERVICE'   => 'customer service',
          'MODULE_MITS_JSON_LD_TELEPHONE_TECHNICAL' => 'technical support',
          'MODULE_MITS_JSON_LD_TELEPHONE_BILLING'   => 'billing support',
          'MODULE_MITS_JSON_LD_TELEPHONE_SALES'     => 'sales',
        ];

        foreach ($map as $const => $type) {
            if (defined($const) && !empty(constant($const))) {
                $contacts[] = [
                  '@type'             => 'ContactPoint',
                  'telephone'         => mits_ml(constant($const)),
                  'contactType'       => $type,
                  'areaServed'        => strtoupper($_SESSION['language_code']),
                  'availableLanguage' => $langs,
                ];
            }
        }

        if (defined('MODULE_MITS_JSON_LD_TELEPHONE_DEFAULT') && !empty(MODULE_MITS_JSON_LD_TELEPHONE_DEFAULT)) {
            $contacts[] = [
              '@type'       => 'ContactPoint',
              'telephone'   => mits_ml(MODULE_MITS_JSON_LD_TELEPHONE_DEFAULT),
              'contactType' => 'customer service',
            ];
        }

        if (!empty($contacts)) {
            $org['contactPoint'] = $contacts;
        }
    }

    // Social
    if (defined('MODULE_MITS_JSON_LD_SOCIAL_MEDIA') && !empty(MODULE_MITS_JSON_LD_SOCIAL_MEDIA)) {
        $org['sameAs'] = array_map('trim', explode(',', MODULE_MITS_JSON_LD_SOCIAL_MEDIA));
    }

    $org = mits_jsonld_clean($org);
    mits_graph_add($org);
}

/* LocalBusiness */
if (mits_flag('MODULE_MITS_JSON_LD_SHOW_LOCATION')) {
    $loc = [
      '@type'     => 'LocalBusiness',
      'name'      => mits_ml(MODULE_MITS_JSON_LD_NAME),
      '@id'       => xtc_href_link(FILENAME_DEFAULT, '', $request_type, false),
      'url'       => xtc_href_link(FILENAME_DEFAULT, '', $request_type, false),
      'telephone' => mits_ml(MODULE_MITS_JSON_LD_TELEPHONE_DEFAULT),
      'address'   => [
        '@type'           => 'PostalAddress',
        'streetAddress'   => mits_ml(MODULE_MITS_JSON_LD_LOCATION_STREETADDRESS),
        'addressLocality' => mits_ml(MODULE_MITS_JSON_LD_LOCATION_ADDRESSLOCALITY),
        'postalCode'      => mits_ml(MODULE_MITS_JSON_LD_LOCATION_POSTALCODE),
        'addressCountry'  => mits_ml(MODULE_MITS_JSON_LD_LOCATION_ADDRESSCOUNTRY),
      ],
    ];

    if ($logo) {
        $loc['image'] = $logo;
    }

    if (!empty(MODULE_MITS_JSON_LD_LOCATION_GEO_LATITUDE) && !empty(MODULE_MITS_JSON_LD_LOCATION_GEO_LONGITUDE)) {
        $loc['geo'] = [
          '@type'     => 'GeoCoordinates',
          'latitude'  => mits_ml(MODULE_MITS_JSON_LD_LOCATION_GEO_LATITUDE),
          'longitude' => mits_ml(MODULE_MITS_JSON_LD_LOCATION_GEO_LONGITUDE),
        ];
    }

    if (defined('MODULE_MITS_JSON_LD_SOCIAL_MEDIA') && !empty(MODULE_MITS_JSON_LD_SOCIAL_MEDIA)) {
        $loc['sameAs'] = array_map('trim', explode(',', MODULE_MITS_JSON_LD_SOCIAL_MEDIA));
    }

    $loc = mits_jsonld_clean($loc);
    mits_graph_add($loc);
}

/* Breadcrumb */
if (mits_flag('MODULE_MITS_JSON_LD_SHOW_BREADCRUMB')
  && isset($breadcrumb)
) {
    $items = [];
    foreach ($breadcrumb->_trail as $i => $bc) {
        $items[] = [
          '@type'    => 'ListItem',
          'position' => $i + 1,
          'name'     => $bc['title'],
          'item'     => $bc['link'] ?: xtc_href_link(basename($PHP_SELF), xtc_get_all_get_params([], true), $request_type),
        ];
    }

    mits_graph_add([
      '@type'           => 'BreadcrumbList',
      'itemListElement' => $items,
    ]);
}


/* ContactPage */
if (basename($PHP_SELF) == FILENAME_CONTENT
  && isset($_GET['coID'])
  && (int)$_GET['coID'] === 7
  && mits_flag('MODULE_MITS_JSON_LD_SHOW_CONTACT')
  && isset($shop_content_data['content_title'])
) {
    $meta_descr = isset($metadata_array['description']) ? $metadata_array['description'] : '';

    $schema = [
      '@type'       => 'ContactPage',
      'url'         => xtc_href_link(FILENAME_CONTENT, 'coID=7', 'SSL', false),
      'name'        => strip_tags($shop_content_data['content_title']),
      'description' => str_replace(["\r", "\n"], ' ', strip_tags(decode_htmlentities($meta_descr))),
    ];

    $schema = mits_jsonld_clean($schema);
    mits_graph_add($schema);
}

/* Produktseite: Product + Offers + Reviews + Tags */
if (basename($PHP_SELF) == FILENAME_PRODUCT_INFO
  && isset($product)
  && is_object($product) && $product->isProduct()
  && mits_flag('MODULE_MITS_JSON_LD_SHOW_PRODUCT')
) {
    global $manufacturer, $productDataArray, $xtPrice;

    $productImages = [];
    if (!empty($product->data['products_image'])) {
        $productImages[] = $product->productImage($product->data['products_image'], 'info');
        $mo = xtc_get_products_mo_images($product->data['products_id']);
        if ($mo) {
            foreach ($mo as $m) {
                $productImages[] = $product->productImage($m['image_name'], 'info');
            }
        }
    }

    $desc = strip_tags($product->data['products_description']);
    $desc = html_entity_decode($desc, ENT_NOQUOTES, $_SESSION['language_charset']);

    $productURL = xtc_href_link(FILENAME_PRODUCT_INFO, xtc_product_link($product->data['products_id']), 'NONSSL', false);
    $schema = [
      '@type'            => 'Product',
      '@id'              => $productURL,
      'url'              => $productURL,
      'mainEntityOfPage' => [
        '@type' => 'WebPage',
        '@id'   => $productURL
      ],
      'productID'        => $product->data['products_id'],
      'sku'              => $product->data['products_model'] ?: $product->data['products_id'],
      'gtin13'           => $product->data['products_ean'],
      'mpn'              => $product->data['products_manufacturers_model'],
      'image'            => $productImages,
      'name'             => $product->data['products_name'],
      'description'      => $desc,
    ];

    if (isset($manufacturer['manufacturers_name'])
      && $manufacturer['manufacturers_name'] != ''
    ) {
        $schema['brand'] = [
          '@type' => 'Brand',
          'name'  => $manufacturer['manufacturers_name'],
        ];
    }

    $schema_light = $schema;

    if (mits_jsonld_tags_enabled_for_product($product)) {
        $additional = mits_build_additional_properties($product->data['products_id']);
        if (!empty($additional)) {
            $schema['additionalProperty'] = $additional;
        }
    }

    $reviewsCount = $product->getReviewsCount($product->data['products_id']);

    if (mits_flag('MODULE_MITS_JSON_LD_SHOW_PRODUCT_REVIEWS')
      && $_SESSION['customers_status']['customers_status_read_reviews'] == '1'
      && $reviewsCount > 0
    ) {
        $schema['aggregateRating'] = [
          '@type'       => 'AggregateRating',
          'ratingValue' => $product->getReviewsAverage(),
          'reviewCount' => $reviewsCount,
        ];

        $reviewList = $product->getReviews($product->data['products_id']);
        if ($reviewList) {
            foreach ($reviewList as $r) {
                $date = DateTime::createFromFormat('d.m.Y', $r['DATE']);
                $schema['review'][] = [
                  '@type'         => 'Review',
                  'author'        => [
                    '@type' => 'Person',
                    'name'  => $r['AUTHOR'],
                  ],
                  'datePublished' => $date ? $date->format('Y-m-d') : '',
                  'reviewBody'    => strip_tags($r['TEXT']),
                  'reviewRating'  => [
                    '@type'       => 'Rating',
                    'ratingValue' => $r['RATING_VOTE'],
                    'worstRating' => '1',
                    'bestRating'  => '5',
                  ],
                ];
            }
        }
    }

    if (defined('MODULE_TS_TRUSTEDSHOPS_ID')
      && defined('MODULE_TS_PRODUCT_STICKER_STATUS')
      && MODULE_TS_PRODUCT_STICKER_STATUS == '1'
      && defined('MODULE_TS_PRODUCT_STICKER')
      && MODULE_TS_PRODUCT_STICKER != ''
      && !empty($productDataArray['TS_FEED_REVIEW'])
      && $productDataArray['TS_FEED_REVIEW'] > 0
    ) {
        $schema['aggregateRating'] = [
          '@type'       => 'AggregateRating',
          'ratingValue' => $productDataArray['TS_FEED_AGGREGATERATING'],
          'reviewCount' => $productDataArray['TS_FEED_REVIEW'],
        ];
    }

    $offersBlock = mits_build_offers_for_product($product, $productDataArray);
    $schema['offers'] = $offersBlock;

    $schema = mits_jsonld_clean($schema);
    mits_graph_add($schema);
    mits_output_json($schema);
}

if (basename($PHP_SELF) == FILENAME_PRODUCT_REVIEWS_INFO
  && mits_flag('MODULE_MITS_JSON_LD_SHOW_PRODUCT_REVIEWS_INFO')
  && isset($reviews) && is_array($reviews) && count($reviews) > 0
  && isset($product) && is_object($product)
) {
    $r = $reviews;
    $count = $product->getReviewsCount($r['products_id']);
    $date = DateTime::createFromFormat('d.m.Y', $r['date_added']);

    $schema = [
      '@type'         => 'Review',
      'reviewRating'  => [
        '@type'       => 'AggregateRating',
        'ratingValue' => $r['reviews_rating'],
        'ratingCount' => $count,
        'reviewCount' => $count,
        'worstRating' => '1',
        'bestRating'  => '5',
      ],
      'itemReviewed'  => [
        '@type' => 'Product',
        'name'  => $r['products_name'],
      ],
      'author'        => [
        '@type' => 'Person',
        'name'  => $r['customers_name'],
      ],
      'datePublished' => $date ? $date->format('Y-m-d') : $r['date_added'],
      'reviewBody'    => strip_tags($r['reviews_text']),
    ];

    $schema = mits_jsonld_clean($schema);
    mits_graph_add($schema);
}

mits_graph_output();
