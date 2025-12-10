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
 * @param string $const
 *
 * @return bool
 */
function mits_flag(string $const): bool
{
    return defined($const) && constant($const) === 'true';
}

/**
 * @param mixed $value
 *
 * @return mixed
 */
function mits_ml(mixed $constant): mixed
{
    $value = is_string($constant) && defined($constant) ? constant($constant) : '';
    return parse_multi_language_value($value, $_SESSION['language_code']);
}

/**
 * @param array $node
 *
 * @return void
 */
function mits_graph_add(array $node): void
{
    global $mitsJsonLdGraph;

    if (!empty($node)) {
        $mitsJsonLdGraph[] = $node;
    }
}

/**
 * @return void
 */
function mits_graph_output(): void
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
 *
 * @return void
 */
function mits_output_json(array $data): void
{
    echo '<script type="application/ld+json">'
      . json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
      . '</script>';
}

/**
 * @param array $base
 * @param array $extra
 *
 * @return array
 */
function mits_jsonld_merge_property_value_lists(array $base, array $extra): array
{
    $byName = [];

    $addList = function (array $list) use (&$byName) {
        foreach ($list as $entry) {
            if (!is_array($entry) || empty($entry['name'])) {
                continue;
            }
            $key = mb_strtolower(trim($entry['name']));
            if (!isset($byName[$key])) {
                $byName[$key] = $entry;
                continue;
            }

            if (isset($entry['value'])) {
                $existingValue = $byName[$key]['value'] ?? null;

                $valuesExisting = is_array($existingValue)
                  ? $existingValue
                  : ($existingValue !== null ? [$existingValue] : []);

                $valuesNew = is_array($entry['value']) ? $entry['value'] : [$entry['value']];

                $merged = array_values(array_unique(array_merge($valuesExisting, $valuesNew)));

                if (!empty($merged)) {
                    $byName[$key]['value'] = count($merged) === 1 ? $merged[0] : $merged;
                }
            }

            if (
              !empty($entry['description']) &&
              (empty($byName[$key]['description']))
            ) {
                $byName[$key]['description'] = $entry['description'];
            }
        }
    };

    $addList($base);
    $addList($extra);

    return array_values($byName);
}

/**
 * @param array $customNodes
 *
 * @return void
 */
function mits_jsonld_add_custom_nodes_to_graph(array $customNodes): void
{
    global $PHP_SELF;
    if (basename($PHP_SELF) === FILENAME_PRODUCT_INFO) {
        $blocked = ['website', 'organization', 'localbusiness', 'contactpage', 'breadcrumblist', 'product', 'faqpage', 'qapage', 'question'];
    } else {
        $blocked = ['website', 'organization', 'localbusiness', 'contactpage', 'breadcrumblist'];
    }

    foreach ($customNodes as $node) {
        if (!is_array($node) || empty($node['@type'])) {
            continue;
        }

        $types = is_array($node['@type']) ? $node['@type'] : [$node['@type']];
        $typesLower = array_map('strtolower', $types);

        if (!array_intersect($typesLower, $blocked)) {
            mits_graph_add(mits_jsonld_clean($node));
        }
    }
}


/**
 * @param array $base
 * @param array $extra
 *
 * @return array
 */
function mits_jsonld_merge_questions(array $base, array $extra): array
{
    $byName = [];

    $addList = function (array $list) use (&$byName) {
        foreach ($list as $q) {
            if (!is_array($q) || empty($q['@type']) || empty($q['name'])) {
                continue;
            }
            $types = is_array($q['@type']) ? $q['@type'] : [$q['@type']];
            $typesLower = array_map('strtolower', $types);
            if (!in_array('question', $typesLower, true)) {
                continue;
            }

            $key = mb_strtolower(trim($q['name']));
            if (!isset($byName[$key])) {
                $byName[$key] = $q;
            }
        }
    };

    $addList($base);
    $addList($extra);

    return array_values($byName);
}

/**
 * @param array $schema
 * @param array $customNodes
 * @param array|null $faqBase
 *
 * @return array
 */
function mits_jsonld_merge_product_with_custom(array $schema, array $customNodes, ?array $faqBase = null): array
{
    $customFaq = [];
    $customAdditionalProps = [];

    foreach ($customNodes as $node) {
        if (!is_array($node) || empty($node['@type'])) {
            continue;
        }

        $types = is_array($node['@type']) ? $node['@type'] : [$node['@type']];
        $typesLower = array_map('strtolower', $types);

        if (array_intersect($typesLower, ['website', 'organization', 'localbusiness', 'contactpage', 'breadcrumblist'])) {
            continue;
        }

        if (in_array('product', $typesLower, true)) {
            if (!empty($node['additionalProperty']) && is_array($node['additionalProperty'])) {
                $customAdditionalProps = array_merge($customAdditionalProps, $node['additionalProperty']);
            }

            if (!empty($node['mainEntity'])) {
                $me = is_array($node['mainEntity']) ? $node['mainEntity'] : [$node['mainEntity']];
                $customFaq = array_merge($customFaq, $me);
            }

            continue;
        }

        if (array_intersect($typesLower, ['faqpage', 'qapage'])) {
            if (!empty($node['mainEntity'])) {
                $me = is_array($node['mainEntity']) ? $node['mainEntity'] : [$node['mainEntity']];
                $customFaq = array_merge($customFaq, $me);
            }
            continue;
        }

        if (in_array('question', $typesLower, true)) {
            $customFaq[] = $node;
            continue;
        }
    }

    if (!empty($customAdditionalProps)) {
        $schema['additionalProperty'] = mits_jsonld_merge_property_value_lists(
          isset($schema['additionalProperty']) && is_array($schema['additionalProperty']) ? $schema['additionalProperty'] : [],
          $customAdditionalProps
        );
    }

    $faqAll = [];
    if (!empty($faqBase)) {
        $faqAll = $faqBase;
    } elseif (!empty($schema['mainEntity']) && is_array($schema['mainEntity'])) {
        $faqAll = $schema['mainEntity'];
    }

    if (!empty($customFaq)) {
        $faqAll = mits_jsonld_merge_questions($faqAll, $customFaq);
    }

    if (!empty($faqAll)) {
        $schema['mainEntity'] = array_values($faqAll);
    }

    return $schema;
}

/**
 * @param array $data
 *
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
 * @param mixed $text
 *
 * @return string|null
 */
function mits_jsonld_sanitize(mixed $text): ?string
{
    static $charset = null;

    if ($charset === null) {
        $charset = defined('CHARSET') ? CHARSET : 'UTF-8';
    }

    if ($text === null || $text === '') {
        return null;
    }

    if (is_array($text)) {
        $text = implode(', ', $text);
    }

    $text = strip_tags($text);

    $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, $charset);

    $text = str_replace(["\u{00A0}", "\u{00AD}"], ' ', $text);

    $text = str_replace(
      [
        "„",
        "“",
        "‚",
        "‘",
        "‟",
        "«",
        "»",
        "\u{201C}",
        "\u{201D}",
        "\u{201E}",
        "\u{201F}",
        "\u{2018}",
        "\u{2019}",
        "\u{00AB}",
        "\u{00BB}",
      ],
      [
        '"',
        '"',
        "'",
        "'",
        '"',
        '"',
        '"',
        '"',
        '"',
        '"',
        '"',
        '"',
        "'",
        "'",
        '"',
        '"',
      ],
      $text
    );

    $text = preg_replace('/[\x00-\x1F\x7F]/u', ' ', $text);

    $text = preg_replace('/[\x{2000}-\x{206F}]/u', ' ', $text);

    if (class_exists('Normalizer')) {
        $text = Normalizer::normalize($text, Normalizer::FORM_C);
    }

    $text = preg_replace('/\s+/u', ' ', $text);

    $text = trim($text);

    return $text === '' ? null : $text;
}

/**
 * @param string $file
 * @param string $params
 * @param string $ssl
 *
 * @return string
 */
function mits_link(string $file, string $params = '', string $ssl = 'NONSSL'): string
{
    return xtc_href_link($file, $params, $ssl, false);
}

/**
 * @param int|string $productId
 * @param bool $noSsl
 *
 * @return string
 */
function mits_product_url(int|string $productId, bool $noSsl = true): string
{
    return xtc_href_link(
      FILENAME_PRODUCT_INFO,
      xtc_product_link($productId),
      $noSsl ? 'NONSSL' : 'SSL',
      false
    );
}

/**
 * @param string $s
 *
 * @return array
 */
function mits_jsonld_parse_country_list(string $s): array
{
    $countries = array_filter(array_map('trim', preg_split('/\s*,\s*/', $s)));
    $countries = array_values(array_filter($countries, static function ($c) {
        return $c !== '' && strlen($c) <= 3;
    }));

    return $countries;
}


/**
 * @param float|int|string $price
 *
 * @return string
 */
function mits_jsonld_price_format(float|int|string $price): string
{
    $price = (string)$price;

    if (function_exists('bcadd')) {
        return bcadd($price, '0', 2);
    }

    return number_format((float)$price, 2, '.', '');
}

/**
 * @param array $productDataArray
 *
 * @return string|null
 */
function mits_jsonld_compute_price_valid_until(array $productDataArray): ?string
{
    if (!empty($productDataArray['PRODUCTS_PRICE_ARRAY'][0]['PRODUCTS_PRICE_EXPIRES_DATE'])) {
        $ts = strtotime($productDataArray['PRODUCTS_PRICE_ARRAY'][0]['PRODUCTS_PRICE_EXPIRES_DATE']);
        if ($ts !== false) {
            return date('Y-m-d', $ts);
        }
    }

    if (defined('MODULE_MITS_JSON_LD_PRICEVALID_DEFAULT_DAYS')) {
        $days = (int)MODULE_MITS_JSON_LD_PRICEVALID_DEFAULT_DAYS;
        if ($days > 0) {
            return date('Y-m-d', time() + $days * 86400);
        }
    }

    return null;
}

/**
 * @return array
 */
function mits_jsonld_build_shipping_details_from_config(): array
{
    if (!defined('MODULE_MITS_JSON_LD_SHIPPING_CONFIG')
      || trim(MODULE_MITS_JSON_LD_SHIPPING_CONFIG) === ''
    ) {
        return [];
    }

    $lines = preg_split("/\r?\n/", trim(MODULE_MITS_JSON_LD_SHIPPING_CONFIG));
    $shippingDetails = [];

    foreach ($lines as $line) {
        if (trim($line) === '') {
            continue;
        }

        $parts = array_map('trim', explode('|', $line));

        if (count($parts) < 8) {
            continue;
        }

        list(
          $country,
          $label,
          $price,
          $currency,
          $handlingMin,
          $handlingMax,
          $transitMin,
          $transitMax,
          $minValue,
          $maxValue
          ) = array_pad($parts, 10, '');

        if ($country === '' || $price === '' || $label === '') {
            continue;
        }

        if ($currency === '') {
            $currency = $_SESSION['currency'] ?? 'EUR';
        }

        if (strtolower($price) === 'free') {
            $price = '0.00';
        }

        $shippingRate = [
          '@type'    => 'MonetaryAmount',
          'value'    => mits_jsonld_price_format($price),
          'currency' => $currency,
        ];

        $shippingDeliveryTime = [
          '@type'        => 'ShippingDeliveryTime',
          'handlingTime' => [
            '@type'    => 'QuantitativeValue',
            'minValue' => (int)$handlingMin,
            'maxValue' => (int)$handlingMax,
            'unitCode' => 'DAY',
          ],
          'transitTime'  => [
            '@type'    => 'QuantitativeValue',
            'minValue' => (int)$transitMin,
            'maxValue' => (int)$transitMax,
            'unitCode' => 'DAY',
          ],
        ];

        $eligibleTransactionVolume = null;
        if ($minValue !== '' || $maxValue !== '') {
            $eligibleMonetary = [];
            if ($minValue !== '') {
                $eligibleMonetary['minValue'] = (float)$minValue;
            }
            if ($maxValue !== '') {
                $eligibleMonetary['maxValue'] = (float)$maxValue;
            }

            if (!empty($eligibleMonetary)) {
                $eligibleTransactionVolume = [
                    '@type'         => 'PriceSpecification',
                    'priceCurrency' => $currency,
                  ] + $eligibleMonetary;
            }
        }

        $countries = mits_jsonld_parse_country_list($country);

        foreach ($countries as $c) {
            $entry = [
              '@type'               => 'OfferShippingDetails',
              'shippingDestination' => [
                '@type'          => 'DefinedRegion',
                'addressCountry' => strtoupper($c), // EINZELNES Land
              ],
              'shippingRate'        => $shippingRate,
              'shippingLabel'       => $label,
              'deliveryTime'        => $shippingDeliveryTime,
            ];

            if ($eligibleTransactionVolume !== null) {
                $entry['eligibleTransactionVolume'] = $eligibleTransactionVolume;
            }

            $shippingDetails[] = mits_jsonld_clean($entry);
        }
    }

    return $shippingDetails;
}

/**
 * @return array
 */
function mits_jsonld_collect_shipping_details(): array
{
    $details = mits_jsonld_build_shipping_details_from_config();

    $auto = mits_jsonld_build_shipping_details_from_modules();
    if (!empty($auto)) {
        $details = array_merge($details, $auto);
    }

    return $details;
}

/**
 * @param string $code
 *
 * @return string
 */
function mits_jsonld_map_return_category(string $code): string
{
    $code = strtolower(trim($code));

    return match ($code) {
        'finite' => 'MerchantReturnFiniteReturnWindow',
        'unlimited' => 'MerchantReturnUnlimitedReturnWindow',
        'not_permitted' => 'MerchantReturnNotPermitted',
        default => 'MerchantReturnFiniteReturnWindow',
    };
}

/**
 * @param string $code
 *
 * @return string
 */
function mits_jsonld_map_return_fees_type(string $code): string
{
    $code = strtolower(trim($code));

    return match ($code) {
        'free', 'freereturn', 'refundfull' => 'FreeReturn',
        'buyer', 'buyerpays', 'customer' => 'ReturnFeesCustomerResponsibility',
        'seller', 'sellerpays' => 'ReturnFeesSellerResponsibility',
        default => 'ReturnFeesCustomerResponsibility',
    };
}


/**
 * @param string $code
 *
 * @return string
 */
function mits_jsonld_map_return_method(string $code): string
{
    $code = strtolower(trim($code));

    return match ($code) {
        'mail', 'post', 'postal' => 'ReturnByMail',
        'store', 'shop', 'instore', 'in_store' => 'ReturnInStore',
        'both', 'store_or_mail', 'in_store_or_mail' => 'ReturnInStoreOrByMail',
        'none', 'not_permitted', 'not-allowed' => 'ReturnNotPermitted',
        default => 'ReturnByMail',
    };
}

/**
 * @return array
 */
function mits_jsonld_build_return_policies_from_config(): array
{
    if (!mits_flag('MODULE_MITS_JSON_LD_ENABLE_RETURNS')) {
        return [];
    }

    if (!defined('MODULE_MITS_JSON_LD_RETURN_POLICY_CONFIG')
      || trim(MODULE_MITS_JSON_LD_RETURN_POLICY_CONFIG) === ''
    ) {
        return [];
    }

    $lines = preg_split('/\r\n|\r|\n/', MODULE_MITS_JSON_LD_RETURN_POLICY_CONFIG);
    $policies = [];

    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '|') === false) {
            continue;
        }

        $parts = array_map('trim', explode('|', $line));

        if (count($parts) === 3) {
            $parts = [
              $parts[0], // Länder
              $parts[1], // Tage
              $parts[1], // maxDays = minDays
              'finite',  // Kategorie
              'buyer',   // Gebührentyp
              'mail',    // Methode
            ];
        }

        if (count($parts) < 5) {
            continue;
        }

        $countryRaw = $parts[0] ?? '';
        $minDays = $parts[1] ?? '';
        $maxDays = $parts[2] ?? $parts[1];
        $categoryCode = $parts[3] ?? 'finite';
        $feeCode = $parts[4] ?? 'buyer';
        $methodCode = $parts[5] ?? 'mail';

        $countries = mits_jsonld_parse_country_list($countryRaw);
        if (empty($countries)) {
            continue;
        }

        $category = mits_jsonld_map_return_category($categoryCode);
        $returnFees = mits_jsonld_map_return_fees_type($feeCode);
        $returnMethod = mits_jsonld_map_return_method($methodCode);

        $minDaysInt = (int)$minDays;
        $maxDaysInt = (int)$maxDays;
        $days = $maxDaysInt > 0 ? $maxDaysInt : $minDaysInt;

        $policy = [
          '@type'                => 'MerchantReturnPolicy',
          'applicableCountry'    => $countries,
          'returnPolicyCategory' => $category,
          'returnFees'           => $returnFees,
          'returnMethod'         => $returnMethod,
        ];

        if ($days > 0) {
            $policy['merchantReturnDays'] = $days;
        }

        $policies[] = mits_jsonld_clean($policy);
    }

    return $policies;
}

/**
 * @param mixed $offersBlock
 * @param array $shippingDetails
 * @param array $returnPolicies
 *
 * @return mixed
 */
function mits_jsonld_attach_shipping_and_return_to_offers(mixed $offersBlock, array $shippingDetails, array $returnPolicies): mixed
{
    if (empty($shippingDetails) && empty($returnPolicies)) {
        return $offersBlock;
    }

    $attach = function (array $offer) use ($shippingDetails, $returnPolicies): array {
        if (!empty($shippingDetails)) {
            $offer['shippingDetails'] = $shippingDetails;
        }
        if (!empty($returnPolicies)) {
            $offer['hasMerchantReturnPolicy'] = count($returnPolicies) === 1
              ? $returnPolicies[0]
              : $returnPolicies;
        }

        return $offer;
    };

    if (isset($offersBlock['@type']) && strtolower($offersBlock['@type']) === 'aggregateoffer') {
        $offersBlock = $attach($offersBlock);

        if (!empty($offersBlock['offers']) && is_array($offersBlock['offers'])) {
            foreach ($offersBlock['offers'] as $k => $subOffer) {
                if (is_array($subOffer)) {
                    $offersBlock['offers'][$k] = $attach($subOffer);
                }
            }
        }

        return $offersBlock;
    }

    if (is_array($offersBlock)) {
        return $attach($offersBlock);
    }

    return $offersBlock;
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
 * @return array
 */
function mits_build_additional_properties(): array
{
    if (
      !isset($GLOBALS['mits_jsonld_tags_content'])
      || !is_array($GLOBALS['mits_jsonld_tags_content'])
    ) {
        return [];
    }

    $content = $GLOBALS['mits_jsonld_tags_content'];
    $additional = [];

    foreach ($content as $tagOption) {
        if (empty($tagOption['DATA']) || !is_array($tagOption['DATA'])) {
            continue;
        }

        if (count($tagOption['DATA']) > 1) {
            $values = [];
            foreach ($tagOption['DATA'] as $valueData) {
                $values[] = mits_jsonld_sanitize($valueData['VALUES_NAME']);
            }
        } else {
            $values = mits_jsonld_sanitize($tagOption['DATA'][0]['VALUES_NAME']);
        }

        $entry = [
          '@type' => 'PropertyValue',
          'name'  => mits_jsonld_sanitize($tagOption['OPTIONS_NAME']),
          'value' => $values,
        ];

        if (!empty($tagOption['OPTIONS_DESCRIPTION'])) {
            $entry['description'] = mits_jsonld_sanitize($tagOption['OPTIONS_DESCRIPTION']);
        }

        $additional[] = $entry;
    }

    return $additional;
}

/**
 * @param object $product
 *
 * @return bool
 */
function mits_jsonld_attributes_enabled_for_product(object $product): bool
{
    $globalEnabled = defined('MODULE_MITS_JSON_LD_ENABLE_ATTRIBUTES')
      && MODULE_MITS_JSON_LD_ENABLE_ATTRIBUTES === 'true';

    if (!isset($product->data['mits_jsonld_attributes_enabled'])) {
        return $globalEnabled;
    }

    $val = $product->data['mits_jsonld_attributes_enabled'];

    if (is_string($val)) {
        $val = strtolower(trim($val));
        return $val === 'true' || $val === '1';
    }

    return (bool)$val;
}

/**
 * @param object $product
 *
 * @return bool
 */
function mits_jsonld_tags_enabled_for_product(object $product): bool
{
    $globalEnabled = defined('MODULE_MITS_JSON_LD_ENABLE_TAGS')
      && MODULE_MITS_JSON_LD_ENABLE_TAGS === 'true';

    if (!isset($product->data['mits_jsonld_tags_enabled'])) {
        return $globalEnabled;
    }

    $val = $product->data['mits_jsonld_tags_enabled'];

    if (is_string($val)) {
        $val = strtolower(trim($val));
        return $val === 'true' || $val === '1';
    }

    return (bool)$val;
}

/**
 * @param object $product
 * @param array $productDataArray
 * @param array $products_options_data
 *
 * @return mixed
 */
function mits_build_offers_for_product(object $product, array $productDataArray, array $products_options_data): mixed
{
    global $xtPrice;

    if (!mits_jsonld_attributes_enabled_for_product($product)) {
        return mits_build_single_base_offer_without_attributes($product, $productDataArray, $xtPrice);
    }

    if (empty($products_options_data)) {
        return mits_build_single_base_offer_without_attributes($product, $productDataArray, $xtPrice);
    }

    $maxOffers = 100;
    if (defined('MODULE_MITS_JSON_LD_MAX_OFFERS') && (int)MODULE_MITS_JSON_LD_MAX_OFFERS > 0) {
        $maxOffers = (int)MODULE_MITS_JSON_LD_MAX_OFFERS;
    }

    $groups = [];
    foreach ($products_options_data as $group) {
        if (empty($group['DATA']) || !is_array($group['DATA'])) {
            continue;
        }

        $optionName = $group['NAME'] ?? '';
        $optionId = $group['ID'] ?? null;

        $dataWithOptionInfo = [];
        foreach ($group['DATA'] as $row) {
            $row['OPTION_NAME'] = $optionName;
            $row['OPTION_ID'] = $optionId;
            $dataWithOptionInfo[] = $row;
        }

        $groups[] = $dataWithOptionInfo;
    }

    if (empty($groups)) {
        return mits_build_single_base_offer_without_attributes($product, $productDataArray, $xtPrice);
    }

    $combinations = mits_combine_attributes_arrays($groups, $maxOffers);

    $offers = [];
    foreach ($combinations as $combo) {
        $offers[] = mits_build_offer_from_products_options_data(
          $combo,
          $product,
          $productDataArray,
          $xtPrice
        );
    }

    if (empty($offers)) {
        return mits_build_single_base_offer_without_attributes($product, $productDataArray, $xtPrice);
    }

    if (count($offers) === 1) {
        return $offers[0];
    }

    $prices = array_map(fn($offer) => (float)$offer['price'], $offers);

    return [
      '@type'         => 'AggregateOffer',
      'priceCurrency' => $_SESSION['currency'] ?? $xtPrice->actualCurr ?? 'EUR',
      'lowPrice'      => mits_jsonld_price_format(min($prices)),
      'highPrice'     => mits_jsonld_price_format(max($prices)),
      'offerCount'    => count($offers),
      'offers'        => $offers,
    ];
}

/**
 * @param array $groups
 * @param int $maxOffers
 *
 * @return array
 */
function mits_combine_attributes_arrays(array $groups, int $maxOffers = 100): array
{
    $result = [[]];
    $limit = max(1, $maxOffers);

    foreach ($groups as $group) {
        $new = [];
        foreach ($result as $combination) {
            foreach ($group as $value) {
                $new[] = array_merge($combination, [$value]);
                if (count($new) >= $limit) {
                    break 2;
                }
            }
        }
        $result = $new;
    }

    return $result;
}

/**
 * @param array $combo
 * @param $product
 * @param array $productDataArray
 * @param $xtPrice
 *
 * @return array
 */
function mits_build_offer_from_products_options_data(array $combo, $product, array $productDataArray, $xtPrice): array
{
    $basePrice = (float)$productDataArray['PRODUCTS_PRICE_ARRAY'][0]['PRODUCTS_PRICE_PLAIN'];
    $sku = $product->data['products_model'];
    $name = $productDataArray['PRODUCTS_NAME'];
    $ean = $product->data['products_ean'];
    $stock = $product->data['products_quantity'];
    $vpeValue = (float)$product->data['products_vpe_value'];
    $vpeName = mits_jsonld_sanitize(xtc_get_vpe_name($product->data['products_vpe']));
    $hasVpe = ($product->data['products_vpe_status'] && $vpeValue > 0);

    $optionIdSignature = [];

    foreach ($combo as $opt) {
        if (!empty($opt['OPTION_NAME']) && !empty($opt['TEXT'])) {
            $name .= ' ' . $opt['OPTION_NAME'] . ' ' . $opt['TEXT'];
        } elseif (!empty($opt['TEXT'])) {
            $name .= ' ' . $opt['TEXT'];
        }

        if (!empty($opt['MODEL'])) {
            $sku .= '-' . $opt['MODEL'];
        } elseif (!empty($opt['TEXT'])) {
            $sku .= '-' . preg_replace('/[^a-z0-9]+/i', '', $opt['TEXT']);
        }

        if ($opt['PLAIN_PRICE'] !== '') {
            $valuePrice = (float)$opt['PLAIN_PRICE'];

            switch ($opt['PREFIX']) {
                case '-':
                    $basePrice -= $valuePrice;
                    break;
                case '=':
                    $basePrice = $valuePrice;
                    break;
                default:
                    $basePrice += $valuePrice;
            }
        }

        if (!empty($opt['EAN'])) {
            $ean = $opt['EAN'];
        }

        if (!empty($opt['VPE_VALUE'])) {
            $vpeValue = (float)$opt['VPE_VALUE'];
            $vpeName = $opt['VPE_NAME'];
            $hasVpe = true;
        }

        if (isset($opt['STOCK']) && $opt['STOCK'] !== '') {
            $stock = min($stock, (int)$opt['STOCK']);
        }

        $optOptionId = $opt['OPTION_ID'] ?? null;
        $optValueId = $opt['ID'] ?? null;

        if ($optOptionId !== null && $optValueId !== null) {
            $optionIdSignature[] = '{' . $optOptionId . '}' . $optValueId;
        }
    }

    $finalPrice = mits_jsonld_price_format($basePrice);
    $currency = $_SESSION['currency'] ?? $xtPrice->actualCurr ?? 'EUR';

    $productIdForLink = $product->data['products_id'] . implode('', $optionIdSignature);
    $url = mits_product_url($productIdForLink);

    $availability = ($stock <= 0 && mits_flag('STOCK_CHECK'))
      ? 'https://schema.org/OutOfStock'
      : 'https://schema.org/InStock';

    $sellerName = defined('META_COMPANY') ? META_COMPANY : STORE_NAME;

    $offer = [
      '@type'              => 'Offer',
      '@id'                => $url . '#offer-' . strtolower(preg_replace('/[^a-z0-9]+/', '-', $sku)),
      'name'               => mits_jsonld_sanitize($name),
      'sku'                => $sku,
      'url'                => $url,
      'price'              => $finalPrice,
      'priceCurrency'      => $currency,
      'itemCondition'      => 'https://schema.org/NewCondition',
      'availability'       => $availability,
      'seller'             => [
        '@type' => 'Organization',
        'name'  => mits_jsonld_sanitize($sellerName),
      ],
      'priceSpecification' => [
        [
          '@type'                 => 'UnitPriceSpecification',
          'price'                 => $finalPrice,
          'priceCurrency'         => $currency,
          'valueAddedTaxIncluded' => true,
          'priceType'             => 'https://schema.org/RegularPrice'
        ]
      ],
    ];

    $priceValidUntil = mits_jsonld_compute_price_valid_until($productDataArray);
    if ($priceValidUntil !== null) {
        $offer['priceValidUntil'] = $priceValidUntil;
    }

    if (!empty($ean)) {
        $offer['gtin'] = $ean;
    }

    if ($hasVpe && $vpeValue > 0) {
        $offer['eligibleQuantity'] = [
          '@type'    => 'QuantitativeValue',
          'value'    => $vpeValue,
          'unitText' => $vpeName,
        ];
    }

    return $offer;
}

/**
 * @param object $product
 * @param array $productDataArray
 * @param object $xtPrice
 *
 * @return array
 */
function mits_build_single_base_offer_without_attributes(
  object $product,
  array $productDataArray,
  object $xtPrice
): array {
    $currencyCode = $_SESSION['currency'] ?? ($xtPrice->actualCurr ?? 'EUR');
    $basePricePlain = (float)($productDataArray['PRODUCTS_PRICE_ARRAY'][0]['PRODUCTS_PRICE_PLAIN']
      ?? $product->data['products_price']);
    $basePrice = mits_jsonld_price_format($basePricePlain);

    $priceSpecification = [];

    if (
      !empty($productDataArray['PRODUCTS_PRICE_ARRAY'][0]['PRODUCTS_PRICE_FLAG'])
      && stripos($productDataArray['PRODUCTS_PRICE_ARRAY'][0]['PRODUCTS_PRICE_FLAG'], 'special') !== false
    ) {
        $oldPricePlain = $productDataArray['PRODUCTS_PRICE_ARRAY'][0]['PRODUCTS_PRICE_OLD_PRICE_PLAIN'] ?? null;

        if ($oldPricePlain === null || $oldPricePlain === '' || (float)$oldPricePlain <= 0) {
            $oldPricePlain = $basePricePlain;
        }

        $oldPrice = mits_jsonld_price_format($oldPricePlain);

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

    if (
      !empty($product->data['products_vpe_status'])
      && !empty($product->data['products_vpe_value'])
      && $product->data['products_vpe_value'] != 0.0
      && $basePricePlain > 0
    ) {
        $baseUnitName = mits_jsonld_sanitize(xtc_get_vpe_name($product->data['products_vpe']));

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

    $offerUrl = mits_product_url($product->data['products_id']);

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
        'name'  => mits_jsonld_sanitize($sellerName),
      ],
      'priceSpecification' => $priceSpecification,
    ];

    $priceValidUntil = mits_jsonld_compute_price_valid_until($productDataArray);
    if ($priceValidUntil !== null) {
        $offer['priceValidUntil'] = $priceValidUntil;
    }

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

/**
 * WebSite
 */
if (mits_flag('MODULE_MITS_JSON_LD_SHOW_WEBSITE')) {
    $webSite = [
      '@type'         => 'WebSite',
      'name'          => mits_jsonld_sanitize(mits_ml('MODULE_MITS_JSON_LD_NAME')),
      'alternateName' => mits_jsonld_sanitize(mits_ml('MODULE_MITS_JSON_LD_ALTERNATE_NAME')),
      'description'   => mits_jsonld_sanitize(mits_ml('MODULE_MITS_JSON_LD_WEBSITE_DESCRIPTION')),
      'url'           => mits_link(FILENAME_DEFAULT),
    ];

    if ($logo) {
        $webSite['image'] = $logo;
    }

    if (mits_flag('MODULE_MITS_JSON_LD_SHOW_SEARCHFIELD')) {
        $webSite['potentialAction'] = [
          '@type'       => 'SearchAction',
          'target'      => mits_link(FILENAME_ADVANCED_SEARCH_RESULT) . '?keywords={search_term_string}',
          'query-input' => 'required name=search_term_string',
        ];
    }

    $webSite = mits_jsonld_clean($webSite);
    mits_graph_add($webSite);
}

/**
 * Organization
 */
if (mits_flag('MODULE_MITS_JSON_LD_SHOW_ORGANISTATION')) {
    global $lng;

    $org = [
      '@type'         => 'Organization',
      '@id'           => mits_link(FILENAME_DEFAULT),
      'name'          => mits_jsonld_sanitize(mits_ml('MODULE_MITS_JSON_LD_NAME')),
      'alternateName' => mits_jsonld_sanitize(mits_ml('MODULE_MITS_JSON_LD_ALTERNATE_NAME')),
      'description'   => mits_jsonld_sanitize(mits_ml('MODULE_MITS_JSON_LD_WEBSITE_DESCRIPTION')),
      'url'           => mits_link(FILENAME_DEFAULT),
    ];

    if ($logo) {
        $org['logo'] = $logo;
        $org['image'] = $logo;
    }

    if (mits_flag('MODULE_MITS_JSON_LD_SHOW_CONTACT') && isset($lng->catalog_languages)) {
        $langs = array_map(static fn($x) => $x['name'], $lng->catalog_languages);
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
                  'telephone'         => mits_jsonld_sanitize(mits_ml($const)),
                  'contactType'       => $type,
                  'areaServed'        => strtoupper($_SESSION['language_code']),
                  'availableLanguage' => $langs,
                ];
            }
        }

        if (defined('MODULE_MITS_JSON_LD_TELEPHONE_DEFAULT') && !empty(MODULE_MITS_JSON_LD_TELEPHONE_DEFAULT)) {
            $contacts[] = [
              '@type'       => 'ContactPoint',
              'telephone'   => mits_jsonld_sanitize(mits_ml('MODULE_MITS_JSON_LD_TELEPHONE_DEFAULT')),
              'contactType' => 'customer service',
            ];
        }

        if (!empty($contacts)) {
            $org['contactPoint'] = $contacts;
        }
    }

    if (defined('MODULE_MITS_JSON_LD_SOCIAL_MEDIA') && !empty(MODULE_MITS_JSON_LD_SOCIAL_MEDIA)) {
        $org['sameAs'] = preg_split('/\s*,\s*/', MODULE_MITS_JSON_LD_SOCIAL_MEDIA);
    }

    $org = mits_jsonld_clean($org);
    mits_graph_add($org);
}

/**
 * LocalBusiness
 */
if (mits_flag('MODULE_MITS_JSON_LD_SHOW_LOCATION')) {
    $loc = [
      '@type'     => 'LocalBusiness',
      'name'      => mits_jsonld_sanitize(mits_ml('MODULE_MITS_JSON_LD_NAME')),
      '@id'       => mits_link(FILENAME_DEFAULT),
      'url'       => mits_link(FILENAME_DEFAULT),
      'telephone' => mits_jsonld_sanitize(mits_ml('MODULE_MITS_JSON_LD_TELEPHONE_DEFAULT')),
      'address'   => [
        '@type'           => 'PostalAddress',
        'streetAddress'   => mits_jsonld_sanitize(mits_ml('MODULE_MITS_JSON_LD_LOCATION_STREETADDRESS')),
        'addressLocality' => mits_jsonld_sanitize(mits_ml('MODULE_MITS_JSON_LD_LOCATION_ADDRESSLOCALITY')),
        'postalCode'      => mits_jsonld_sanitize(mits_ml('MODULE_MITS_JSON_LD_LOCATION_POSTALCODE')),
        'addressCountry'  => mits_jsonld_sanitize(mits_ml('MODULE_MITS_JSON_LD_LOCATION_ADDRESSCOUNTRY')),
      ],
      'priceRange' => 'Varied'
    ];

    if ($logo) {
        $loc['image'] = $logo;
    }

    if (defined('MODULE_MITS_JSON_LD_LOCATION_GEO_LATITUDE')
      && !empty(MODULE_MITS_JSON_LD_LOCATION_GEO_LATITUDE)
      && defined('MODULE_MITS_JSON_LD_LOCATION_GEO_LONGITUDE')
      && !empty(MODULE_MITS_JSON_LD_LOCATION_GEO_LONGITUDE)
    ) {
        $loc['geo'] = [
          '@type'     => 'GeoCoordinates',
          'latitude'  => mits_jsonld_sanitize(mits_ml('MODULE_MITS_JSON_LD_LOCATION_GEO_LATITUDE')),
          'longitude' => mits_jsonld_sanitize(mits_ml('MODULE_MITS_JSON_LD_LOCATION_GEO_LONGITUDE')),
        ];
    }

    if (defined('MODULE_MITS_JSON_LD_SOCIAL_MEDIA') && !empty(MODULE_MITS_JSON_LD_SOCIAL_MEDIA)) {
        $loc['sameAs'] = array_map('trim', explode(',', MODULE_MITS_JSON_LD_SOCIAL_MEDIA));
    }

    $loc = mits_jsonld_clean($loc);
    mits_graph_add($loc);
}

/**
 * Breadcrumb
 */
if (mits_flag('MODULE_MITS_JSON_LD_SHOW_BREADCRUMB') && isset($breadcrumb)) {
    $items = [];

    foreach ($breadcrumb->_trail as $i => $bc) {
        $items[] = [
          '@type'    => 'ListItem',
          'position' => $i + 1,
          'name'     => mits_jsonld_sanitize($bc['title']),
          'item'     => $bc['link'] ?: mits_link(basename($PHP_SELF), xtc_get_all_get_params([], true)),
        ];
    }

    mits_graph_add([
      '@type'           => 'BreadcrumbList',
      'itemListElement' => $items,
    ]);
}

/**
 * ContactPage (Kontaktseite)
 */
if (
  basename($PHP_SELF) === FILENAME_CONTENT
  && isset($_GET['coID'])
  && (int)$_GET['coID'] === 7
  && mits_flag('MODULE_MITS_JSON_LD_SHOW_CONTACT')
  && isset($shop_content_data['content_title'])
) {
    $metaDescr = isset($metadata_array['description']) ? $metadata_array['description'] : '';

    $schema = [
      '@type'       => 'ContactPage',
      'url'         => mits_link(FILENAME_CONTENT, 'coID=7', 'SSL'),
      'name'        => mits_jsonld_sanitize($shop_content_data['content_title']),
      'description' => mits_jsonld_sanitize(decode_htmlentities($metaDescr)),
    ];

    $schema = mits_jsonld_clean($schema);
    mits_graph_add($schema);
}

/**
 * Custom JSON-LD für allgemeine Content-Seiten (NICHT ContactPage)
 */
if (
  basename($PHP_SELF) === FILENAME_CONTENT
  && (!isset($_GET['coID']) || (int)$_GET['coID'] !== 7)
  && !empty($shop_content_data['content_text'])
  && !empty($GLOBALS['mits_jsonld_custom_nodes'])
) {
    mits_jsonld_add_custom_nodes_to_graph($GLOBALS['mits_jsonld_custom_nodes']);
}

/**
 * Custom JSON-LD für Kategorie-Seiten (index.php mit cPath)
 */
if (
  basename($PHP_SELF) === FILENAME_DEFAULT
  && isset($_GET['cPath'])
  && !empty($GLOBALS['mits_jsonld_custom_nodes'])
) {
    mits_jsonld_add_custom_nodes_to_graph($GLOBALS['mits_jsonld_custom_nodes']);
}


/**
 * Produktseite: Product + Offers + Reviews + Tags + FAQ + Custom-JSON
 */
if (
  basename($PHP_SELF) === FILENAME_PRODUCT_INFO
  && isset($product)
  && is_object($product)
  && $product->isProduct()
  && mits_flag('MODULE_MITS_JSON_LD_SHOW_PRODUCT')
) {
    global $manufacturer, $productDataArray, $xtPrice;

    $customJsonNodes = $GLOBALS['mits_jsonld_custom_nodes'] ?? [];

    $productImagesSchema = [];

    if (!empty($product->data['products_image'])) {
        $main_img_url = $product->productImage($product->data['products_image'], 'info');
        if ($main_img_url) {
            $productImagesSchema[] = [
              '@type'   => 'ImageObject',
              'url'     => $main_img_url,
              'caption' => mits_jsonld_sanitize($product->data['products_name']),
            ];
        }
    }

    $mo_images = xtc_get_products_mo_images($product->data['products_id']);
    if ($mo_images !== false) {
        foreach ($mo_images as $img) {
            $mo_img_url = $product->productImage($img['image_name'], 'info');
            $alt_text = $img['image_alt'] ?? '';
            $title_text = $img['image_title'] ?? '';
            $caption_value = $alt_text ?: $title_text;
            $description_value = $caption_value ? $title_text : '';

            if ($mo_img_url != '') {
                $image_object = [
                  '@type'       => 'ImageObject',
                  'url'         => $mo_img_url,
                  'caption'     => mits_jsonld_sanitize($caption_value),
                  'description' => mits_jsonld_sanitize($description_value),
                ];
                $productImagesSchema[] = $image_object;
            }
        }
    }

    $productImagesSchema = empty($productImagesSchema) ? null : $productImagesSchema;

    $productURL = mits_product_url($product->data['products_id']);

    $schema = [
      '@type'            => 'Product',
      '@id'              => $productURL,
      'url'              => $productURL,
      'mainEntityOfPage' => [
        '@type' => 'WebPage',
        '@id'   => $productURL,
      ],
      'productID'        => $product->data['products_id'],
      'sku'              => mits_jsonld_sanitize(
        $product->data['products_model'] ?: $product->data['products_id']
      ),
      'gtin13'           => mits_jsonld_sanitize($product->data['products_ean']),
      'mpn'              => mits_jsonld_sanitize($product->data['products_manufacturers_model']),
      'image'            => $productImagesSchema,
      'name'             => mits_jsonld_sanitize($product->data['products_name']),
      'description'      => mits_jsonld_sanitize($product->data['products_description']),
    ];

    if (isset($manufacturer['manufacturers_name']) && $manufacturer['manufacturers_name'] != '') {
        $schema['brand'] = [
          '@type' => 'Brand',
          'name'  => mits_jsonld_sanitize($manufacturer['manufacturers_name']),
        ];
    }

    if (mits_jsonld_tags_enabled_for_product($product)) {
        $additional = mits_build_additional_properties();
        if (!empty($additional)) {
            $schema['additionalProperty'] = $additional;
        }
    }

    $reviewsCount = $product->getReviewsCount($product->data['products_id']);

    if (
      mits_flag('MODULE_MITS_JSON_LD_SHOW_PRODUCT_REVIEWS')
      && $_SESSION['customers_status']['customers_status_read_reviews'] === '1'
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
                    'name'  => mits_jsonld_sanitize($r['AUTHOR']),
                  ],
                  'datePublished' => $date ? $date->format('Y-m-d') : '',
                  'reviewBody'    => mits_jsonld_sanitize($r['TEXT']),
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

    if (
      defined('MODULE_TS_TRUSTEDSHOPS_ID')
      && defined('MODULE_TS_PRODUCT_STICKER_STATUS')
      && MODULE_TS_PRODUCT_STICKER_STATUS === '1'
      && defined('MODULE_TS_PRODUCT_STICKER')
      && MODULE_TS_PRODUCT_STICKER !== ''
      && !empty($productDataArray['TS_FEED_REVIEW'])
      && $productDataArray['TS_FEED_REVIEW'] > 0
    ) {
        $schema['aggregateRating'] = [
          '@type'       => 'AggregateRating',
          'ratingValue' => $productDataArray['TS_FEED_AGGREGATERATING'],
          'reviewCount' => $productDataArray['TS_FEED_REVIEW'],
        ];
    }

    $offersBlock = mits_build_offers_for_product(
      $product,
      $productDataArray,
      $GLOBALS['mits_jsonld_products_options_data'] ?? []
    );

    $shippingDetails = mits_jsonld_collect_shipping_details();
    $returnPolicies = mits_jsonld_build_return_policies_from_config();

    $offersBlock = mits_jsonld_attach_shipping_and_return_to_offers(
      $offersBlock,
      $shippingDetails,
      $returnPolicies
    );

    $schema['offers'] = $offersBlock;

    $faqBaseQuestions = [];
    if (isset($structuredFAQDataforJSON) && is_array($structuredFAQDataforJSON)) {
        if (!empty($structuredFAQDataforJSON['mainEntity']) && is_array($structuredFAQDataforJSON['mainEntity'])) {
            $faqBaseQuestions = $structuredFAQDataforJSON['mainEntity'];
        } elseif (isset($structuredFAQDataforJSON[0]) && is_array($structuredFAQDataforJSON[0])) {
            $faqBaseQuestions = $structuredFAQDataforJSON;
        }
    }

    if (!empty($customJsonNodes)) {
        $schema = mits_jsonld_merge_product_with_custom(
          $schema,
          $customJsonNodes,
          !empty($faqBaseQuestions) ? $faqBaseQuestions : null
        );
    } elseif (!empty($faqBaseQuestions)) {
        $schema['mainEntity'] = array_values($faqBaseQuestions);
    }

    if (!empty($schema['mainEntity']) && is_array($schema['mainEntity'])) {
        $faqPageNode = [
          '@type'      => 'FAQPage',
          '@id'        => $productURL . '#faq',
          'mainEntity' => $schema['mainEntity'],
        ];
        $faqPageNode = mits_jsonld_clean($faqPageNode);
        mits_graph_add($faqPageNode);
    }

    $schema = mits_jsonld_clean($schema);
    mits_graph_add($schema);

    if (!empty($customJsonNodes)) {
        mits_jsonld_add_custom_nodes_to_graph($customJsonNodes);
    }
}

/**
 * Einzelne Bewertungsseite (Produktbewertung)
 */
if (
  basename($PHP_SELF) === FILENAME_PRODUCT_REVIEWS_INFO
  && mits_flag('MODULE_MITS_JSON_LD_SHOW_PRODUCT_REVIEWS_INFO')
  && isset($reviews)
  && is_array($reviews)
  && count($reviews) > 0
  && isset($product)
  && is_object($product)
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
        'name'  => mits_jsonld_sanitize($r['products_name']),
      ],
      'author'        => [
        '@type' => 'Person',
        'name'  => mits_jsonld_sanitize($r['customers_name']),
      ],
      'datePublished' => $date ? $date->format('Y-m-d') : $r['date_added'],
      'reviewBody'    => mits_jsonld_sanitize($r['reviews_text']),
    ];

    $schema = mits_jsonld_clean($schema);
    mits_graph_add($schema);
}

if (isset($structuredFAQPageDataForJSON) && is_array($structuredFAQPageDataForJSON)) {
    $structuredFAQPageDataForJSON = mits_jsonld_clean($structuredFAQPageDataForJSON);
    mits_graph_add($structuredFAQPageDataForJSON);
}

/**
 * DEBUG-AUSGABE: Nur wenn ?mits_jsonld_debug=1 gesetzt ist und Admin-Status
 */
if (
  isset($_GET['mits_jsonld_debug'])
  && $_GET['mits_jsonld_debug'] == '1'
  && isset($_SESSION['customers_status']['customers_status_id'])
  && $_SESSION['customers_status']['customers_status_id'] == 0
) {
    echo "<div style='background:#222;color:#0f0;padding:20px;margin:20px;font-family:monospace;font-size:14px;'>";
    echo "<h2 style='color:#6f6;'>MITS JSON-LD DEBUG</h2>";

    if (isset($product) && is_object($product)) {
        echo "<h3 style='color:#9f9;'>Produktdaten:</h3><pre>";
        print_r($product->data);
        echo "</pre>";
    }

    if (isset($productDataArray)) {
        echo "<h3 style='color:#9f9;'>productDataArray:</h3><pre>";
        print_r($productDataArray);
        echo "</pre>";
    }

    if (isset($GLOBALS['mits_jsonld_products_options_data'])) {
        echo "<h3 style='color:#9f9;'>mits_jsonld_products_options_data:</h3><pre>";
        print_r($GLOBALS['mits_jsonld_products_options_data']);
        echo "</pre>";
    } else {
        echo "<h3 style='color:#f66;'>Keine Attribute vorhanden oder nicht geladen.</h3>";
    }

    if (isset($GLOBALS['mits_jsonld_tags_content'])) {
        echo "<h3 style='color:#9f9;'>mits_jsonld_tags_content:</h3><pre>";
        print_r($GLOBALS['mits_jsonld_tags_content']);
        echo "</pre>";
    }

    echo "<h3 style='color:#6f6;'>Finaler JSON-LD Graph (@graph):</h3><pre>";
    print_r($mitsJsonLdGraph);
    echo "</pre>";

    echo "<h3 style='color:#6f6;'>Ausgabe als formatiertes JSON:</h3><pre>";
    echo json_encode(
      ['@context' => 'https://schema.org', '@graph' => $mitsJsonLdGraph],
      JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
    );
    echo "</pre>";

    echo "</div>";
}

mits_graph_output();
