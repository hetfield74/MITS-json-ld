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

$mitsJsonLdCurrentFile = basename($GLOBALS['PHP_SELF'] ?? ($_SERVER['PHP_SELF'] ?? ''));
$mitsJsonLdBlockedFiles = [
  'shopping_cart.php',
  'checkout.php',
  'checkout_shipping.php',
  'checkout_shipping_address.php',
  'checkout_payment.php',
  'checkout_payment_address.php',
  'checkout_confirmation.php',
  'checkout_process.php',
  'checkout_success.php',
  'checkout_new_address.php',
];

foreach ([
  'FILENAME_SHOPPING_CART',
  'FILENAME_CHECKOUT_SHIPPING',
  'FILENAME_CHECKOUT_SHIPPING_ADDRESS',
  'FILENAME_CHECKOUT_PAYMENT',
  'FILENAME_CHECKOUT_PAYMENT_ADDRESS',
  'FILENAME_CHECKOUT_CONFIRMATION',
  'FILENAME_CHECKOUT_PROCESS',
  'FILENAME_CHECKOUT_SUCCESS',
  'FILENAME_CHECKOUT_NEW_ADDRESS',
] as $mitsJsonLdBlockedConstant) {
    if (defined($mitsJsonLdBlockedConstant)) {
        $mitsJsonLdBlockedFiles[] = constant($mitsJsonLdBlockedConstant);
    }
}

if (
  in_array($mitsJsonLdCurrentFile, array_unique($mitsJsonLdBlockedFiles), true)
  || preg_match('/^checkout_.*\.php$/i', $mitsJsonLdCurrentFile)
) {
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

    mits_output_json($payload);
}

/**
 * @param array $data
 *
 * @return void
 */
function mits_output_json(array $data): void
{
    $json = mits_jsonld_encode($data);

    if ($json === null) {
        return;
    }

    echo '<script type="application/ld+json">' . $json . '</script>';
}


/**
 * @return string
 */
function mits_jsonld_encoding_mode(): string
{
    $mode = 'auto';

    if (defined('MODULE_MITS_JSON_LD_JSON_ENCODING')) {
        $mode = (string)constant('MODULE_MITS_JSON_LD_JSON_ENCODING');
    }

    if (defined('MITS_JSON_LD_JSON_ENCODING')) {
        $mode = (string)constant('MITS_JSON_LD_JSON_ENCODING');
    }

    if (isset($GLOBALS['mits_jsonld_json_encoding'])) {
        $mode = (string)$GLOBALS['mits_jsonld_json_encoding'];
    }

    $mode = strtolower(trim($mode));
    $mode = str_replace('_', '-', $mode);

    if (in_array($mode, ['utf8', 'utf-8'], true)) {
        return 'utf-8';
    }

    if (in_array($mode, ['iso', 'latin9', 'latin-9', 'iso8859-15', 'iso-8859-15'], true)) {
        return 'iso-8859-15';
    }

    return 'auto';
}

/**
 * @param string $value
 *
 * @return bool
 */
function mits_jsonld_is_valid_utf8(string $value): bool
{
    if ($value === '') {
        return true;
    }

    if (function_exists('mb_check_encoding')) {
        return mb_check_encoding($value, 'UTF-8');
    }

    return preg_match('//u', $value) === 1;
}

/**
 * @param string $value
 * @param string $fromEncoding
 *
 * @return string
 */
function mits_jsonld_convert_to_utf8(string $value, string $fromEncoding): string
{
    if ($value === '') {
        return '';
    }

    if (function_exists('mb_convert_encoding')) {
        $converted = @mb_convert_encoding($value, 'UTF-8', $fromEncoding);
        if (is_string($converted)) {
            return $converted;
        }
    }

    if (function_exists('iconv')) {
        $converted = @iconv($fromEncoding, 'UTF-8//TRANSLIT//IGNORE', $value);
        if (is_string($converted)) {
            return $converted;
        }
    }

    return $value;
}

/**
 * @param string $value
 * @param string $mode
 *
 * @return string
 */
function mits_jsonld_normalize_string_for_json(string $value, string $mode): string
{
    if ($value === '') {
        return '';
    }

    if (mits_jsonld_is_valid_utf8($value)) {
        return $value;
    }

    if ($mode === 'utf-8') {
        return mits_jsonld_convert_to_utf8($value, 'UTF-8');
    }

    return mits_jsonld_convert_to_utf8($value, 'ISO-8859-15');
}

/**
 * @param mixed $data
 * @param string|null $mode
 *
 * @return mixed
 */
function mits_jsonld_normalize_for_json(mixed $data, ?string $mode = null): mixed
{
    $mode = $mode ?? mits_jsonld_encoding_mode();

    if (is_string($data)) {
        return mits_jsonld_normalize_string_for_json($data, $mode);
    }

    if (is_array($data)) {
        $normalized = [];

        foreach ($data as $key => $value) {
            $normalizedKey = is_string($key)
              ? mits_jsonld_normalize_string_for_json($key, $mode)
              : $key;

            $normalized[$normalizedKey] = mits_jsonld_normalize_for_json($value, $mode);
        }

        return $normalized;
    }

    if (is_object($data)) {
        foreach ($data as $key => $value) {
            $data->{$key} = mits_jsonld_normalize_for_json($value, $mode);
        }
    }

    return $data;
}

/**
 * @param string $mode
 *
 * @return int
 */
function mits_jsonld_json_encode_flags(string $mode): int
{
    $flags = JSON_UNESCAPED_SLASHES;

    if ($mode === 'utf-8') {
        $flags |= JSON_UNESCAPED_UNICODE;
    }

    if (defined('JSON_INVALID_UTF8_SUBSTITUTE')) {
        $flags |= JSON_INVALID_UTF8_SUBSTITUTE;
    }

    return $flags;
}

/**
 * @param string $message
 *
 * @return void
 */
function mits_jsonld_log_json_error(string $message): void
{
    if (function_exists('error_log')) {
        error_log('[MITS JSON-LD] ' . $message);
    }
}

/**
 * @param mixed $data
 *
 * @return string|null
 */
function mits_jsonld_encode(mixed $data): ?string
{
    $mode = mits_jsonld_encoding_mode();
    $normalized = mits_jsonld_normalize_for_json($data, $mode);

    $json = json_encode($normalized, mits_jsonld_json_encode_flags($mode));

    if ($json === false) {
        mits_jsonld_log_json_error('json_encode failed: ' . json_last_error_msg() . ' (encoding=' . $mode . ')');
        return null;
    }

    if ($json === '') {
        mits_jsonld_log_json_error('json_encode returned an empty string (encoding=' . $mode . ')');
        return null;
    }

    return $json;
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
function mits_jsonld_add_custom_nodes_to_graph(array $customNodes, array $extraBlockedTypes = []): void
{
    global $PHP_SELF;
    if (basename($PHP_SELF) === FILENAME_PRODUCT_INFO) {
        $blocked = ['website', 'organization', 'localbusiness', 'contactpage', 'breadcrumblist', 'product', 'faqpage', 'qapage', 'question'];
    } else {
        $blocked = ['website', 'organization', 'localbusiness', 'contactpage', 'breadcrumblist'];
    }

    if (!empty($extraBlockedTypes)) {
        $blocked = array_values(array_unique(array_merge($blocked, array_map('strtolower', $extraBlockedTypes))));
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
        $schema['_mits_jsonld_faq_questions'] = array_values($faqAll);
    }

    return $schema;
}

/**
 * @param mixed $schema
 *
 * @return array
 */
function mits_jsonld_collect_faq_questions(mixed $schema): array
{
    $questions = [];

    if (!is_array($schema) || $schema === []) {
        return [];
    }

    if (isset($schema[0]) && is_array($schema[0])) {
        foreach ($schema as $entry) {
            $questions = array_merge($questions, mits_jsonld_collect_faq_questions($entry));
        }
        return mits_jsonld_merge_questions([], $questions);
    }

    $types = [];
    if (!empty($schema['@type'])) {
        $types = is_array($schema['@type']) ? $schema['@type'] : [$schema['@type']];
        $types = array_map('strtolower', $types);
    }

    if (in_array('question', $types, true)) {
        return [$schema];
    }

    if (array_intersect($types, ['faqpage', 'qapage'])) {
        if (!empty($schema['mainEntity'])) {
            return mits_jsonld_collect_faq_questions($schema['mainEntity']);
        }
        return [];
    }

    if (!empty($schema['mainEntity'])) {
        return mits_jsonld_collect_faq_questions($schema['mainEntity']);
    }

    return [];
}

/**
 * @return array
 */
function mits_jsonld_collect_global_faq_questions(): array
{
    $questions = [];

    if (isset($GLOBALS['structuredFAQDataforJSON']) && is_array($GLOBALS['structuredFAQDataforJSON'])) {
        $questions = array_merge($questions, mits_jsonld_collect_faq_questions($GLOBALS['structuredFAQDataforJSON']));
    }

    if (isset($GLOBALS['structuredFAQPageDataForJSON']) && is_array($GLOBALS['structuredFAQPageDataForJSON'])) {
        $questions = array_merge($questions, mits_jsonld_collect_faq_questions($GLOBALS['structuredFAQPageDataForJSON']));
    }

    return mits_jsonld_merge_questions([], $questions);
}

/**
 * @param array $page
 * @param array $customNodes
 * @param array $faqBase
 *
 * @return array
 */
function mits_jsonld_merge_page_with_custom(array $page, array $customNodes = [], array $faqBase = []): array
{
    $questions = $faqBase;

    foreach ($customNodes as $node) {
        if (!is_array($node) || empty($node['@type'])) {
            continue;
        }

        $types = is_array($node['@type']) ? $node['@type'] : [$node['@type']];
        $typesLower = array_map('strtolower', $types);

        if (array_intersect($typesLower, ['website', 'organization', 'localbusiness', 'contactpage', 'breadcrumblist'])) {
            continue;
        }

        if (array_intersect($typesLower, ['faqpage', 'qapage', 'question'])) {
            $questions = array_merge($questions, mits_jsonld_collect_faq_questions($node));
        }
    }

    $questions = mits_jsonld_merge_questions([], $questions);

    if (!empty($questions) && empty($page['mainEntity'])) {
        $page['mainEntity'] = array_values($questions);
    }

    return $page;
}

/**
 * @param string $pageUrl
 * @param array $questions
 *
 * @return void
 */
function mits_jsonld_add_faq_page_for_url(string $pageUrl, array $questions): void
{
    $questions = mits_jsonld_merge_questions([], $questions);

    if (empty($questions)) {
        return;
    }

    $faqPageNode = [
      '@type'      => 'FAQPage',
      '@id'        => $pageUrl . '#faq',
      'mainEntity' => array_values($questions),
    ];

    mits_graph_add(mits_jsonld_clean($faqPageNode));
    $GLOBALS['mits_jsonld_faq_page_handled'] = true;
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
    $text = mits_jsonld_normalize_string_for_json((string)$text, mits_jsonld_encoding_mode());

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
 * @return array
 */
function mits_jsonld_location_address(): array
{
    $address = [
      '@type'           => 'PostalAddress',
      'streetAddress'   => mits_jsonld_sanitize(mits_ml('MODULE_MITS_JSON_LD_LOCATION_STREETADDRESS')),
      'addressLocality' => mits_jsonld_sanitize(mits_ml('MODULE_MITS_JSON_LD_LOCATION_ADDRESSLOCALITY')),
      'postalCode'      => mits_jsonld_sanitize(mits_ml('MODULE_MITS_JSON_LD_LOCATION_POSTALCODE')),
      'addressCountry'  => mits_jsonld_sanitize(mits_ml('MODULE_MITS_JSON_LD_LOCATION_ADDRESSCOUNTRY')),
    ];

    $address = mits_jsonld_clean($address);

    return count($address) > 1 ? $address : [];
}

/**
 * @return array
 */
function mits_jsonld_founder(): array
{
    $founder = mits_jsonld_sanitize(mits_ml('MODULE_MITS_JSON_LD_FOUNDER'));

    if ($founder === null) {
        return [];
    }

    return [
      '@type' => 'Person',
      'name'  => $founder,
    ];
}

/**
 * @return string|null
 */
function mits_jsonld_founding_date(): ?string
{
    return mits_jsonld_sanitize(mits_ml('MODULE_MITS_JSON_LD_FOUNDING_DATE'));
}

/**
 * @param string $const
 * @return string|null
 */
function mits_jsonld_contact_option(string $const): ?string
{
    if (!defined($const)) {
        return null;
    }

    $value = trim((string)constant($const));

    if ($value === '') {
        return null;
    }

    $allowed = ['TollFree', 'HearingImpairedSupported'];

    return in_array($value, $allowed, true) ? $value : null;
}

/**
 * @param string $day
 * @return string|null
 */
function mits_jsonld_day_name(string $day): ?string
{
    $day = trim(mb_strtolower($day));
    $day = rtrim($day, '.');

    $map = [
      'mo' => 'Monday', 'mon' => 'Monday', 'monday' => 'Monday', 'montag' => 'Monday',
      'di' => 'Tuesday', 'die' => 'Tuesday', 'tue' => 'Tuesday', 'tuesday' => 'Tuesday', 'dienstag' => 'Tuesday',
      'mi' => 'Wednesday', 'mit' => 'Wednesday', 'wed' => 'Wednesday', 'wednesday' => 'Wednesday', 'mittwoch' => 'Wednesday',
      'do' => 'Thursday', 'don' => 'Thursday', 'thu' => 'Thursday', 'thursday' => 'Thursday', 'donnerstag' => 'Thursday',
      'fr' => 'Friday', 'fri' => 'Friday', 'friday' => 'Friday', 'freitag' => 'Friday',
      'sa' => 'Saturday', 'sam' => 'Saturday', 'sat' => 'Saturday', 'saturday' => 'Saturday', 'samstag' => 'Saturday', 'sonnabend' => 'Saturday',
      'so' => 'Sunday', 'son' => 'Sunday', 'sun' => 'Sunday', 'sunday' => 'Sunday', 'sonntag' => 'Sunday',
    ];

    return $map[$day] ?? null;
}

/**
 * @param string $days
 * @return array
 */
function mits_jsonld_days_from_string(string $days): array
{
    $order = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
    $result = [];
    $parts = preg_split('/\s*,\s*/', trim($days));

    foreach ($parts as $part) {
        if ($part === '') {
            continue;
        }

        if (preg_match('/^(.+?)\s*-\s*(.+)$/u', $part, $matches)) {
            $start = mits_jsonld_day_name($matches[1]);
            $end = mits_jsonld_day_name($matches[2]);

            if ($start !== null && $end !== null) {
                $startIndex = array_search($start, $order, true);
                $endIndex = array_search($end, $order, true);

                if ($startIndex !== false && $endIndex !== false) {
                    if ($startIndex <= $endIndex) {
                        $range = array_slice($order, $startIndex, $endIndex - $startIndex + 1);
                    } else {
                        $range = array_merge(array_slice($order, $startIndex), array_slice($order, 0, $endIndex + 1));
                    }

                    $result = array_merge($result, $range);
                }
            }
        } else {
            $day = mits_jsonld_day_name($part);

            if ($day !== null) {
                $result[] = $day;
            }
        }
    }

    return array_values(array_unique($result));
}

/**
 * @param string $time
 * @return string|null
 */
function mits_jsonld_normalize_time(string $time): ?string
{
    $time = trim($time);

    if (!preg_match('/^([0-2]?\d):([0-5]\d)$/', $time, $matches)) {
        return null;
    }

    $hour = (int)$matches[1];
    if ($hour > 23) {
        return null;
    }

    return sprintf('%02d:%02d', $hour, (int)$matches[2]);
}

/**
 * @param string $hoursConst
 * @param array $fallback
 * @return array
 */
function mits_jsonld_contact_hours_available(
    string $hoursConst = 'MODULE_MITS_JSON_LD_CONTACT_HOURS_AVAILABLE',
    array $fallback = []
): array {
    if (!defined($hoursConst)) {
        return $fallback;
    }

    $raw = trim((string)constant($hoursConst));

    if ($raw === '') {
        return $fallback;
    }

    $hours = [];
    $lines = preg_split('/\r\n|\r|\n/', $raw);

    foreach ($lines as $line) {
        $line = trim($line);

        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }

        if (!preg_match('/^(.+?)\s+([0-2]?\d:[0-5]\d)\s*-\s*([0-2]?\d:[0-5]\d)$/u', $line, $matches)) {
            continue;
        }

        $opens = mits_jsonld_normalize_time($matches[2]);
        $closes = mits_jsonld_normalize_time($matches[3]);

        if ($opens === null || $closes === null) {
            continue;
        }

        $days = mits_jsonld_days_from_string($matches[1]);

        if (empty($days)) {
            continue;
        }

        $hours[] = [
          '@type'     => 'OpeningHoursSpecification',
          'dayOfWeek' => count($days) === 1 ? $days[0] : $days,
          'opens'     => $opens,
          'closes'    => $closes,
        ];
    }

    return $hours;
}

/**
 * @param string $telephoneConst
 * @param string $emailConst
 * @param string $optionConst
 * @param string $contactType
 * @param array $hoursAvailable
 * @param bool $withLocale
 * @return array
 */
function mits_jsonld_contact_point(
    string $telephoneConst,
    string $emailConst,
    string $optionConst,
    string $contactType,
    array $hoursAvailable = [],
    bool $withLocale = true
): array {
    $telephone = defined($telephoneConst) ? mits_jsonld_sanitize(mits_ml($telephoneConst)) : null;
    $email = defined($emailConst) ? mits_jsonld_sanitize(mits_ml($emailConst)) : null;

    if ($telephone === null && $email === null) {
        return [];
    }

    $contactPoint = [
      '@type'          => 'ContactPoint',
      'telephone'      => $telephone,
      'email'          => $email,
      'contactType'    => $contactType,
      'contactOption'  => mits_jsonld_contact_option($optionConst),
      'hoursAvailable' => $hoursAvailable,
    ];

    if ($withLocale && isset($_SESSION['language_code']) && $_SESSION['language_code'] !== '') {
        $contactPoint['areaServed'] = strtoupper($_SESSION['language_code']);
        $contactPoint['availableLanguage'] = strtolower($_SESSION['language_code']) . '-' . strtoupper($_SESSION['language_code']);
    }

    return mits_jsonld_clean($contactPoint);
}

/**
 *
 * @param mixed $text
 * @param string $context
 *
 * @return string|null
 */
function mits_jsonld_schema_text(mixed $text, string $context = ''): ?string
{
    if (
      in_array($context, ['product.description', 'category.description', 'content.description'], true)
      && defined('MODULE_MITS_EMBEDDED_VIDEOS_STATUS')
      && MODULE_MITS_EMBEDDED_VIDEOS_STATUS === 'true'
      && defined('MODULE_COOKIE_CONSENT_STATUS')
      && MODULE_COOKIE_CONSENT_STATUS === 'true'
    ) {
        if (function_exists('mits_embedded_videos_get_schema_clean_text')) {
            $text = mits_embedded_videos_get_schema_clean_text($text, $context);
        } elseif ($context === 'product.description' && isset($GLOBALS['mits_embedded_videos_schema_clean_description'])) {
            $text = $GLOBALS['mits_embedded_videos_schema_clean_description'];
        } elseif ($context === 'category.description' && isset($GLOBALS['mits_embedded_videos_schema_clean_category_description'])) {
            $text = $GLOBALS['mits_embedded_videos_schema_clean_category_description'];
        } elseif ($context === 'content.description' && isset($GLOBALS['mits_embedded_videos_schema_clean_content_description'])) {
            $text = $GLOBALS['mits_embedded_videos_schema_clean_content_description'];
        }
    }

    return mits_jsonld_sanitize($text);
}

/**
 * @param array $node
 *
 * @return bool
 */
function mits_jsonld_is_valid_custom_node(array $node): bool
{
    if (empty($node['@type'])) {
        return false;
    }

    $types = is_array($node['@type']) ? $node['@type'] : [$node['@type']];
    $typesLower = array_map('strtolower', $types);

    if (!in_array('videoobject', $typesLower, true)) {
        return true;
    }

    $embedUrl = isset($node['embedUrl']) ? trim((string)$node['embedUrl']) : '';
    $contentUrl = isset($node['contentUrl']) ? trim((string)$node['contentUrl']) : '';

    if ($embedUrl === '' && $contentUrl === '') {
        return false;
    }

    if ($embedUrl !== '' && preg_match('#/embed/?$#i', $embedUrl)) {
        return false;
    }

    return true;
}

/**
 * @return string
 */
function mits_jsonld_current_url(array $excludeParams = []): string
{
    return mits_link(basename($GLOBALS['PHP_SELF']), xtc_get_all_get_params($excludeParams, true));
}

/**
 * @return string
 */
function mits_jsonld_catalog_base_url(): string
{
    $server = '';
    if (
      isset($GLOBALS['request_type'])
      && $GLOBALS['request_type'] === 'SSL'
      && defined('HTTPS_SERVER')
      && HTTPS_SERVER !== ''
    ) {
        $server = HTTPS_SERVER;
    } elseif (defined('HTTP_SERVER') && HTTP_SERVER !== '') {
        $server = HTTP_SERVER;
    } elseif (defined('HTTPS_SERVER') && HTTPS_SERVER !== '') {
        $server = HTTPS_SERVER;
    }

    $catalogDir = defined('DIR_WS_CATALOG') ? DIR_WS_CATALOG : (defined('DIR_WS_BASE') ? DIR_WS_BASE : '/');

    if ($server === '') {
        return rtrim((string)$catalogDir, '/');
    }

    return rtrim((string)$server, '/') . '/' . trim((string)$catalogDir, '/');
}

/**
 * @param string|null $url
 *
 * @return string|null
 */
function mits_jsonld_absolute_url(?string $url): ?string
{
    $url = trim((string)$url);
    if ($url === '') {
        return null;
    }

    $url = str_replace('\\', '/', $url);

    if (preg_match('#^https?://#i', $url)) {
        return $url;
    }

    if (strpos($url, '//') === 0) {
        $scheme = (isset($GLOBALS['request_type']) && $GLOBALS['request_type'] === 'SSL') ? 'https:' : 'http:';
        return $scheme . $url;
    }

    if ($url[0] === '/') {
        $server = '';
        if (
          isset($GLOBALS['request_type'])
          && $GLOBALS['request_type'] === 'SSL'
          && defined('HTTPS_SERVER')
          && HTTPS_SERVER !== ''
        ) {
            $server = HTTPS_SERVER;
        } elseif (defined('HTTP_SERVER') && HTTP_SERVER !== '') {
            $server = HTTP_SERVER;
        } elseif (defined('HTTPS_SERVER') && HTTPS_SERVER !== '') {
            $server = HTTPS_SERVER;
        }

        return $server !== '' ? rtrim((string)$server, '/') . $url : $url;
    }

    $baseUrl = mits_jsonld_catalog_base_url();
    if ($baseUrl === '') {
        return $url;
    }

    return rtrim($baseUrl, '/') . '/' . ltrim($url, '/');
}

/**
 * @return int
 */
function mits_jsonld_current_page_number(): int
{
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

    return max(1, $page);
}

/**
 * @return string
 */
function mits_jsonld_base_listing_url(): string
{
    return mits_jsonld_current_url(['page']);
}

/**
 * @param int $page
 *
 * @return string
 */
function mits_jsonld_listing_page_url(int $page): string
{
    $params = xtc_get_all_get_params(['page'], true);

    if ($page > 1) {
        $params .= ($params !== '' ? '&' : '') . 'page=' . $page;
    }

    return mits_link(basename($GLOBALS['PHP_SELF']), $params);
}

/**
 * @return int|null
 */
function mits_jsonld_get_listing_page_size(): ?int
{
    $splitCandidates = ['listing_split', 'products_new_split', 'specials_split', 'manufacturers_split'];
    $propertyCandidates = ['number_of_rows_per_page', 'max_rows', 'maxRows'];

    foreach ($splitCandidates as $name) {
        if (empty($GLOBALS[$name]) || !is_object($GLOBALS[$name])) {
            continue;
        }

        foreach ($propertyCandidates as $property) {
            if (isset($GLOBALS[$name]->{$property}) && (int)$GLOBALS[$name]->{$property} > 0) {
                return (int)$GLOBALS[$name]->{$property};
            }
        }
    }

    if (defined('MAX_DISPLAY_SEARCH_RESULTS') && (int)MAX_DISPLAY_SEARCH_RESULTS > 0) {
        return (int)MAX_DISPLAY_SEARCH_RESULTS;
    }

    return null;
}

/**
 * @return int
 */
function mits_jsonld_get_listing_position_offset(): int
{
    $page = mits_jsonld_current_page_number();

    if ($page <= 1) {
        return 0;
    }

    $pageSize = mits_jsonld_get_listing_page_size();

    if ($pageSize === null || $pageSize <= 0) {
        return 0;
    }

    return ($page - 1) * $pageSize;
}

/**
 * @param array $listingItems
 * @param string $listingUrl
 * @param int $positionOffset
 *
 * @return array|null
 */
function mits_jsonld_build_listing_itemlist(array $listingItems, string $listingUrl, int $positionOffset = 0): ?array
{
    $itemListElement = [];
    $position = max(0, $positionOffset) + 1;

    foreach ($listingItems as $item) {
        if (!is_array($item)) {
            continue;
        }

        $url = isset($item['PRODUCTS_LINK']) ? (string)$item['PRODUCTS_LINK'] : '';

        if ($url === '') {
            continue;
        }

        $listItem = [
          '@type'    => 'ListItem',
          'position' => $position++,
          'item'     => [
            '@type' => 'WebPage',
            '@id'   => $url . '#webpage',
            'url'   => $url,
            'name'  => mits_jsonld_sanitize($item['PRODUCTS_NAME'] ?? ''),
          ],
        ];

        $itemListElement[] = mits_jsonld_clean($listItem);
    }

    if (empty($itemListElement)) {
        return null;
    }

    return [
      '@type'           => 'ItemList',
      '@id'             => $listingUrl . '#itemlist',
      'numberOfItems'   => count($itemListElement),
      'itemListElement' => $itemListElement,
    ];
}

/**
 * @param array $listingItems
 * @param string $categoryUrl
 * @param int $positionOffset
 *
 * @return array|null
 */
function mits_jsonld_build_category_itemlist(array $listingItems, string $categoryUrl, int $positionOffset = 0): ?array
{
    return mits_jsonld_build_listing_itemlist($listingItems, $categoryUrl, $positionOffset);
}

/**
 * @return string|null
 */
function mits_jsonld_get_category_name(): ?string
{
    global $category, $breadcrumb;

    if (!empty($category['categories_name'])) {
        return mits_jsonld_sanitize($category['categories_name']);
    }

    if (isset($breadcrumb->_trail) && is_array($breadcrumb->_trail) && !empty($breadcrumb->_trail)) {
        $last = end($breadcrumb->_trail);
        if (!empty($last['title'])) {
            return mits_jsonld_sanitize($last['title']);
        }
    }

    return null;
}

/**
 * @return string|null
 */
function mits_jsonld_get_category_description(): ?string
{
    global $category, $metadata_array;

    if (!empty($category['categories_description'])) {
        return mits_jsonld_schema_text($category['categories_description'], 'category.description');
    }

    if (!empty($metadata_array['description'])) {
        return mits_jsonld_schema_text($metadata_array['description'], 'category.description');
    }

    return null;
}

/**
 * @param string $categoryUrl
 * @param array|null $itemList
 *
 * @return array
 */
function mits_jsonld_build_category_collection_page(
    string $categoryUrl,
    ?array $itemList = null,
    ?string $baseCategoryUrl = null,
    int $pageNumber = 1
): array {
    $name = mits_jsonld_get_category_name();

    if ($pageNumber > 1 && $name !== null) {
        $name .= ' - Seite ' . $pageNumber;
    }

    $page = [
      '@type'       => 'CollectionPage',
      '@id'         => $categoryUrl . '#webpage',
      'url'         => $categoryUrl,
      'name'        => $name,
      'description' => mits_jsonld_get_category_description(),
    ];

    if (
      $pageNumber > 1
      && $baseCategoryUrl !== null
      && $baseCategoryUrl !== ''
      && $baseCategoryUrl !== $categoryUrl
    ) {
        $page['isPartOf'] = [
          '@type' => 'CollectionPage',
          '@id'   => $baseCategoryUrl . '#webpage',
          'url'   => $baseCategoryUrl,
        ];
    }

    if (!empty($itemList)) {
        $page['mainEntity'] = [
          '@id' => $categoryUrl . '#itemlist',
        ];
    }

    return mits_jsonld_clean($page);
}

/**
 * @return bool
 */
function mits_jsonld_is_search_results_page(): bool
{
    $currentFile = basename($GLOBALS['PHP_SELF'] ?? ($_SERVER['PHP_SELF'] ?? ''));
    $searchResultsFile = defined('FILENAME_ADVANCED_SEARCH_RESULT')
      ? FILENAME_ADVANCED_SEARCH_RESULT
      : 'advanced_search_result.php';

    return $currentFile === $searchResultsFile;
}

/**
 * @return string|null
 */
function mits_jsonld_get_search_results_name(): ?string
{
    $name = '';

    if (defined('NAVBAR_TITLE_2')) {
        $name = constant('NAVBAR_TITLE_2');
    } elseif (defined('HEADING_TITLE')) {
        $name = constant('HEADING_TITLE');
    } else {
        $name = 'Search results';
    }

    $keywords = isset($_GET['keywords']) ? trim((string)$_GET['keywords']) : '';

    if ($keywords !== '') {
        $name .= ': ' . $keywords;
    }

    return mits_jsonld_sanitize($name);
}

/**
 * @return string|null
 */
function mits_jsonld_get_search_results_description(): ?string
{
    global $metadata_array;

    if (!empty($metadata_array['description'])) {
        return mits_jsonld_schema_text($metadata_array['description']);
    }

    return null;
}

/**
 * @param string $searchUrl
 * @param array|null $itemList
 * @param string|null $baseSearchUrl
 * @param int $pageNumber
 *
 * @return array
 */
function mits_jsonld_build_search_results_collection_page(
    string $searchUrl,
    ?array $itemList = null,
    ?string $baseSearchUrl = null,
    int $pageNumber = 1
): array {
    $name = mits_jsonld_get_search_results_name();

    if ($pageNumber > 1 && $name !== null) {
        $name .= ' - Seite ' . $pageNumber;
    }

    $page = [
      '@type'       => 'CollectionPage',
      '@id'         => $searchUrl . '#webpage',
      'url'         => $searchUrl,
      'name'        => $name,
      'description' => mits_jsonld_get_search_results_description(),
    ];

    if (
      $pageNumber > 1
      && $baseSearchUrl !== null
      && $baseSearchUrl !== ''
      && $baseSearchUrl !== $searchUrl
    ) {
        $page['isPartOf'] = [
          '@type' => 'CollectionPage',
          '@id'   => $baseSearchUrl . '#webpage',
          'url'   => $baseSearchUrl,
        ];
    }

    if (!empty($itemList)) {
        $page['mainEntity'] = [
          '@id' => $searchUrl . '#itemlist',
        ];
    }

    return mits_jsonld_clean($page);
}


/**
 * @return bool
 */
function mits_jsonld_is_content_page(): bool
{
    $currentFile = basename($GLOBALS['PHP_SELF'] ?? ($_SERVER['PHP_SELF'] ?? ''));
    $contentFile = defined('FILENAME_CONTENT') ? FILENAME_CONTENT : 'shop_content.php';

    return $currentFile === $contentFile;
}

/**
 * @return array|null
 */
function mits_jsonld_get_content_data(): ?array
{
    if (!mits_jsonld_is_content_page()) {
        return null;
    }

    if (!isset($GLOBALS['shop_content_data']) || !is_array($GLOBALS['shop_content_data'])) {
        return null;
    }

    if (empty($GLOBALS['shop_content_data']['content_title'])) {
        return null;
    }

    return $GLOBALS['shop_content_data'];
}

/**
 * @param array $contentData
 *
 * @return int
 */
function mits_jsonld_get_content_group(array $contentData): int
{
    if (isset($contentData['content_group']) && (int)$contentData['content_group'] > 0) {
        return (int)$contentData['content_group'];
    }

    if (isset($_GET['coID']) && (int)$_GET['coID'] > 0) {
        return (int)$_GET['coID'];
    }

    return 0;
}

/**
 * @param array $contentData
 *
 * @return string
 */
function mits_jsonld_content_url(array $contentData): string
{
    $contentFile = defined('FILENAME_CONTENT') ? FILENAME_CONTENT : 'shop_content.php';
    $contentGroup = mits_jsonld_get_content_group($contentData);
    $contentTitle = isset($contentData['content_title']) ? (string)$contentData['content_title'] : '';
    $ssl = (isset($GLOBALS['request_type']) && $GLOBALS['request_type'] === 'SSL') ? 'SSL' : 'NONSSL';

    if ($contentGroup > 0 && function_exists('xtc_content_link')) {
        return xtc_href_link($contentFile, xtc_content_link($contentGroup, $contentTitle), $ssl, false);
    }

    if ($contentGroup > 0) {
        return xtc_href_link($contentFile, 'coID=' . $contentGroup, $ssl, false);
    }

    return mits_jsonld_current_url([]);
}

/**
 * @param array $contentData
 *
 * @return string|null
 */
function mits_jsonld_get_content_page_name(array $contentData): ?string
{
    if (!empty($contentData['content_heading'])) {
        return mits_jsonld_sanitize($contentData['content_heading']);
    }

    return mits_jsonld_sanitize($contentData['content_title'] ?? '');
}

/**
 * @param string|null $text
 * @param int $maxLength
 *
 * @return string|null
 */
function mits_jsonld_truncate_text(?string $text, int $maxLength = 320): ?string
{
    if ($text === null || $text === '') {
        return null;
    }

    if ($maxLength <= 0) {
        return $text;
    }

    $length = function_exists('mb_strlen') ? mb_strlen($text) : strlen($text);
    if ($length <= $maxLength) {
        return $text;
    }

    $short = function_exists('mb_substr') ? mb_substr($text, 0, $maxLength) : substr($text, 0, $maxLength);
    $short = rtrim($short);

    return $short === '' ? null : $short . '…';
}

/**
 * @param array $contentData
 *
 * @return string|null
 */
function mits_jsonld_get_content_page_description(array $contentData): ?string
{
    global $metadata_array;

    if (!empty($metadata_array['description'])) {
        return mits_jsonld_schema_text($metadata_array['description'], 'content.description');
    }

    if (!empty($contentData['content_text'])) {
        return mits_jsonld_truncate_text(
          mits_jsonld_schema_text($contentData['content_text'], 'content.description'),
          320
        );
    }

    return null;
}

/**
 * @return string
 */
function mits_jsonld_content_schema_type_column(): string
{
    return 'mits_jsonld_schema_type';
}

/**
 * @return string
 */
function mits_jsonld_content_image_column(): string
{
    return 'mits_content_image';
}

/**
 * @return bool
 */
function mits_jsonld_ext_content_manager_enabled(): bool
{
    return defined('MODULE_MITS_EXT_CONTENT_MANAGER_STATUS') && MODULE_MITS_EXT_CONTENT_MANAGER_STATUS === 'true';
}

/**
 * @return string
 */
function mits_jsonld_content_table_name(): string
{
    return defined('TABLE_CONTENT_MANAGER') ? TABLE_CONTENT_MANAGER : 'content_manager';
}

/**
 * @param string $table
 * @param string $column
 *
 * @return bool
 */
function mits_jsonld_db_column_exists(string $table, string $column): bool
{
    static $cache = [];

    if ($table === '' || $column === '' || !preg_match('/^[a-zA-Z0-9_]+$/', $table) || !preg_match('/^[a-zA-Z0-9_]+$/', $column)) {
        return false;
    }

    $cacheKey = $table . '.' . $column;
    if (array_key_exists($cacheKey, $cache)) {
        return $cache[$cacheKey];
    }

    $query = xtc_db_query("SHOW COLUMNS FROM `" . $table . "` LIKE '" . xtc_db_input($column) . "'");
    $cache[$cacheKey] = xtc_db_num_rows($query) > 0;

    return $cache[$cacheKey];
}

/**
 * @param array $contentData
 *
 * @return string
 */
function mits_jsonld_get_content_schema_type(array $contentData): string
{
    $column = mits_jsonld_content_schema_type_column();
    $rawType = isset($contentData[$column]) ? trim((string)$contentData[$column]) : '';

    if ($rawType === '') {
        $table = mits_jsonld_content_table_name();
        $contentGroup = mits_jsonld_get_content_group($contentData);

        if ($contentGroup > 0 && mits_jsonld_db_column_exists($table, $column)) {
            $languagesId = isset($_SESSION['languages_id']) ? (int)$_SESSION['languages_id'] : 0;
            $where = "content_group = " . (int)$contentGroup;
            if ($languagesId > 0) {
                $where .= " AND languages_id = " . $languagesId;
            }

            $typeQuery = xtc_db_query("SELECT `" . $column . "` AS schema_type FROM `" . $table . "` WHERE " . $where . " LIMIT 1");
            if (xtc_db_num_rows($typeQuery) > 0) {
                $typeData = xtc_db_fetch_array($typeQuery);
                $rawType = isset($typeData['schema_type']) ? trim((string)$typeData['schema_type']) : '';
            }
        }
    }

    return mb_strtolower($rawType) === 'article' ? 'Article' : 'WebPage';
}

/**
 * @param mixed $value
 *
 * @return bool
 */
function mits_jsonld_truthy(mixed $value): bool
{
    if (is_bool($value)) {
        return $value;
    }

    if (is_int($value) || is_float($value)) {
        return (float)$value !== 0.0;
    }

    $value = mb_strtolower(trim((string)$value));

    return in_array($value, ['1', 'true', 'yes', 'ja', 'on', 'article'], true);
}

/**
 * Externer Override, z. B. per auto_include:
 * define('MITS_JSON_LD_PRODUCT_AS_ARTICLE', true);
 * $GLOBALS['mits_jsonld_product_as_article'] = true;
 * define('MITS_JSON_LD_PRODUCT_SCHEMA_TYPE', 'Article');
 * $GLOBALS['mits_jsonld_product_schema_type'] = 'Article';
 *
 * @return bool
 */
function mits_jsonld_product_as_article_enabled(): bool
{
    $schemaTypeValues = [];

    if (defined('MITS_JSON_LD_PRODUCT_SCHEMA_TYPE')) {
        $schemaTypeValues[] = constant('MITS_JSON_LD_PRODUCT_SCHEMA_TYPE');
    }

    if (isset($GLOBALS['mits_jsonld_product_schema_type'])) {
        $schemaTypeValues[] = $GLOBALS['mits_jsonld_product_schema_type'];
    }

    foreach ($schemaTypeValues as $value) {
        if (mb_strtolower(trim((string)$value)) === 'article') {
            return true;
        }
    }

    if (defined('MITS_JSON_LD_PRODUCT_AS_ARTICLE') && mits_jsonld_truthy(constant('MITS_JSON_LD_PRODUCT_AS_ARTICLE'))) {
        return true;
    }

    return isset($GLOBALS['mits_jsonld_product_as_article'])
      && mits_jsonld_truthy($GLOBALS['mits_jsonld_product_as_article']);
}

/**
 * @param array $contentData
 *
 * @return string|null
 */
function mits_jsonld_get_content_image_filename(array $contentData): ?string
{
    if (!mits_jsonld_ext_content_manager_enabled()) {
        return null;
    }

    $column = mits_jsonld_content_image_column();
    $image = isset($contentData[$column]) ? trim((string)$contentData[$column]) : '';

    if ($image === '') {
        $table = mits_jsonld_content_table_name();
        $contentGroup = mits_jsonld_get_content_group($contentData);

        if ($contentGroup > 0 && mits_jsonld_db_column_exists($table, $column)) {
            $languagesId = isset($_SESSION['languages_id']) ? (int)$_SESSION['languages_id'] : 0;
            $where = "content_group = " . (int)$contentGroup;
            if ($languagesId > 0) {
                $where .= " AND languages_id = " . $languagesId;
            }

            $imageQuery = xtc_db_query("SELECT `" . $column . "` AS content_image FROM `" . $table . "` WHERE " . $where . " LIMIT 1");
            if (xtc_db_num_rows($imageQuery) > 0) {
                $imageData = xtc_db_fetch_array($imageQuery);
                $image = isset($imageData['content_image']) ? trim((string)$imageData['content_image']) : '';
            }
        }
    }

    return $image !== '' ? $image : null;
}

/**
 * @param array $contentData
 *
 * @return array
 */
function mits_jsonld_get_content_article_images(array $contentData): array
{
    global $main;

    $imageFile = mits_jsonld_get_content_image_filename($contentData);
    if ($imageFile === null) {
        return [];
    }

    if (!isset($main) || !is_object($main) || !method_exists($main, 'getImage')) {
        return [];
    }

    $language = isset($_SESSION['language']) ? (string)$_SESSION['language'] : '';
    if ($language === '') {
        return [];
    }

    $variants = [
      'desktop' => 'content/' . $language . '/desktop/',
      'tablet'  => 'content/' . $language . '/tablet/',
      'mobile'  => 'content/' . $language . '/mobile/',
      'preview' => 'content/' . $language . '/preview/',
    ];

    $images = [];
    $seenUrls = [];

    foreach ($variants as $variant => $path) {
        $imagePath = $main->getImage($imageFile, $path, 'false');
        $imagePath = is_string($imagePath) ? trim($imagePath) : '';
        if ($imagePath === '') {
            continue;
        }

        $imageUrl = $imagePath;
        if (!preg_match('#^https?://#i', $imageUrl) && strpos($imageUrl, '/') !== 0 && defined('DIR_WS_BASE')) {
            $imageUrl = DIR_WS_BASE . $imageUrl;
        }

        $url = mits_jsonld_absolute_url($imageUrl);
        if ($url === null || isset($seenUrls[$url])) {
            continue;
        }

        $seenUrls[$url] = true;
        $images[] = mits_jsonld_clean([
          '@type' => 'ImageObject',
          'url'   => $url,
          'name'  => mits_jsonld_get_content_page_name($contentData),
        ]);
    }

    return $images;
}

/**
 * @param mixed $dateValue
 *
 * @return string|null
 */
function mits_jsonld_format_date(mixed $dateValue): ?string
{
    if ($dateValue === null || $dateValue === '' || $dateValue === '0000-00-00 00:00:00' || $dateValue === '0000-00-00') {
        return null;
    }

    try {
        return (new DateTime((string)$dateValue))->format('Y-m-d');
    } catch (Exception $e) {
        return null;
    }
}

/**
 * @param array $contentData
 * @param string $contentUrl
 * @param bool $showWebsite
 * @param string $websiteId
 * @param bool $showOrganization
 * @param string $organizationId
 *
 * @return array
 */
function mits_jsonld_build_content_webpage(
    array $contentData,
    string $contentUrl,
    bool $showWebsite = false,
    string $websiteId = '',
    bool $showOrganization = false,
    string $organizationId = ''
): array {
    $name = mits_jsonld_get_content_page_name($contentData);
    $schemaType = mits_jsonld_get_content_schema_type($contentData);
    $schemaIdSuffix = $schemaType === 'Article' ? '#article' : '#webpage';

    $page = [
      '@type'       => $schemaType,
      '@id'         => $contentUrl . $schemaIdSuffix,
      'url'         => $contentUrl,
      'name'        => $name,
      'headline'    => $name,
      'description' => mits_jsonld_get_content_page_description($contentData),
    ];

    if ($schemaType === 'Article') {
        $page['mainEntityOfPage'] = [
          '@type' => 'WebPage',
          '@id'   => $contentUrl . '#webpage',
        ];

        foreach (['date_added', 'content_date_added', 'content_date_available'] as $dateKey) {
            if (!empty($contentData[$dateKey])) {
                $page['datePublished'] = mits_jsonld_format_date($contentData[$dateKey]);
                break;
            }
        }

        foreach (['last_modified', 'content_last_modified', 'date_modified'] as $dateKey) {
            if (!empty($contentData[$dateKey])) {
                $page['dateModified'] = mits_jsonld_format_date($contentData[$dateKey]);
                break;
            }
        }

        $articleImages = mits_jsonld_get_content_article_images($contentData);
        if (!empty($articleImages)) {
            $page['image'] = $articleImages;
        }
    }

    if (!empty($_SESSION['language_code'])) {
        $page['inLanguage'] = $_SESSION['language_code'];
    } elseif (!empty($_SESSION['language'])) {
        $page['inLanguage'] = $_SESSION['language'];
    }

    if ($showWebsite && $websiteId !== '') {
        $page['isPartOf'] = [
          '@id' => $websiteId,
        ];
    }

    if ($showOrganization && $organizationId !== '') {
        $page['publisher'] = [
          '@id' => $organizationId,
        ];

        if ($schemaType === 'Article') {
            $page['author'] = [
              '@id' => $organizationId,
            ];
        }
    }

    return mits_jsonld_clean($page);
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

        $orderValue = null;
        if ($minValue !== '' || $maxValue !== '') {
            $orderValue = [
              '@type'    => 'MonetaryAmount',
              'currency' => $currency,
            ];
            if ($minValue !== '') {
                $orderValue['minValue'] = (float)$minValue;
            }
            if ($maxValue !== '') {
                $orderValue['maxValue'] = (float)$maxValue;
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

            if ($orderValue !== null) {
                $serviceId = rtrim(mits_jsonld_catalog_base_url(), '/') . '#shipping-service-' . substr(
                  md5(strtoupper($c) . '|' . $label . '|' . $currency . '|' . $price . '|' . $minValue . '|' . $maxValue),
                  0,
                  12
                );

                $entry['hasShippingService'] = [
                  '@type'              => 'ShippingService',
                  '@id'                => $serviceId,
                  'shippingConditions' => [
                    '@type'               => 'ShippingConditions',
                    'shippingDestination' => $entry['shippingDestination'],
                    'orderValue'          => $orderValue,
                    'shippingRate'        => $shippingRate,
                  ],
                ];
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
    if (!mits_flag('MODULE_MITS_JSON_LD_ENABLE_SHIPPING_DETAILS')) {
        return [];
    }

    $details = mits_jsonld_build_shipping_details_from_config();

    /*
    $auto = mits_jsonld_build_shipping_details_from_modules();
    if (!empty($auto)) {
        $details = array_merge($details, $auto);
    }
    */

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
            $prefix = $opt['PREFIX'] ?? '+';

            switch ($prefix) {
                case '-': $basePrice -= $valuePrice; break;
                case '=': $basePrice = $valuePrice;  break;
                default:  $basePrice += $valuePrice; break;
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
$homeUrl = mits_link(FILENAME_DEFAULT);
$websiteId = $homeUrl . '#website';
$organizationId = $homeUrl . '#organization';
$localBusinessId = $homeUrl . '#localbusiness';
$showWebsite = mits_flag('MODULE_MITS_JSON_LD_SHOW_WEBSITE');
$showOrganization = mits_flag('MODULE_MITS_JSON_LD_SHOW_ORGANISTATION');
$showLocation = mits_flag('MODULE_MITS_JSON_LD_SHOW_LOCATION');

/**
 * WebSite
 */
if ($showWebsite) {
    $webSite = [
      '@type'         => 'WebSite',
      '@id'           => $websiteId,
      'name'          => mits_jsonld_sanitize(mits_ml('MODULE_MITS_JSON_LD_NAME')),
      'alternateName' => mits_jsonld_sanitize(mits_ml('MODULE_MITS_JSON_LD_ALTERNATE_NAME')),
      'description'   => mits_jsonld_sanitize(mits_ml('MODULE_MITS_JSON_LD_WEBSITE_DESCRIPTION')),
      'url'           => $homeUrl,
    ];

    if ($showOrganization) {
        $webSite['publisher'] = [
          '@id' => $organizationId,
        ];
    }

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
if ($showOrganization) {
    $org = [
      '@type'           => 'Organization',
      '@id'             => $organizationId,
      'name'            => mits_jsonld_sanitize(mits_ml('MODULE_MITS_JSON_LD_NAME')),
      'alternateName'   => mits_jsonld_sanitize(mits_ml('MODULE_MITS_JSON_LD_ALTERNATE_NAME')),
      'description'     => mits_jsonld_sanitize(mits_ml('MODULE_MITS_JSON_LD_WEBSITE_DESCRIPTION')),
      'url'             => $homeUrl,
      'email'           => mits_jsonld_sanitize(mits_ml('MODULE_MITS_JSON_LD_EMAIL')),
    ];

    if ($showWebsite) {
        $org['mainEntityOfPage'] = [
          '@id' => $websiteId,
        ];
    }

    $address = mits_jsonld_location_address();
    if (!empty($address)) {
        $org['address'] = $address;
    }

    $founder = mits_jsonld_founder();
    if (!empty($founder)) {
        $org['founder'] = $founder;
    }

    $foundingDate = mits_jsonld_founding_date();
    if ($foundingDate !== null) {
        $org['foundingDate'] = $foundingDate;
    }

    if ($logo) {
        $org['logo'] = $logo;
        $org['image'] = $logo;
    }

    if (mits_flag('MODULE_MITS_JSON_LD_SHOW_CONTACT')) {
        $contacts = [];
        $fallbackHoursAvailable = mits_jsonld_contact_hours_available();

        $map = [
          ['MODULE_MITS_JSON_LD_TELEPHONE_SERVICE', 'MODULE_MITS_JSON_LD_EMAIL_SERVICE', 'MODULE_MITS_JSON_LD_CONTACT_OPTION_SERVICE', 'customer service', 'MODULE_MITS_JSON_LD_CONTACT_HOURS_SERVICE'],
          ['MODULE_MITS_JSON_LD_TELEPHONE_TECHNICAL', 'MODULE_MITS_JSON_LD_EMAIL_TECHNICAL', 'MODULE_MITS_JSON_LD_CONTACT_OPTION_TECHNICAL', 'technical support', 'MODULE_MITS_JSON_LD_CONTACT_HOURS_TECHNICAL'],
          ['MODULE_MITS_JSON_LD_TELEPHONE_BILLING', 'MODULE_MITS_JSON_LD_EMAIL_BILLING', 'MODULE_MITS_JSON_LD_CONTACT_OPTION_BILLING', 'billing support', 'MODULE_MITS_JSON_LD_CONTACT_HOURS_BILLING'],
          ['MODULE_MITS_JSON_LD_TELEPHONE_SALES', 'MODULE_MITS_JSON_LD_EMAIL_SALES', 'MODULE_MITS_JSON_LD_CONTACT_OPTION_SALES', 'sales', 'MODULE_MITS_JSON_LD_CONTACT_HOURS_SALES'],
        ];

        foreach ($map as $contactConfig) {
            $contactPoint = mits_jsonld_contact_point(
              $contactConfig[0],
              $contactConfig[1],
              $contactConfig[2],
              $contactConfig[3],
              mits_jsonld_contact_hours_available($contactConfig[4], $fallbackHoursAvailable)
            );

            if (!empty($contactPoint)) {
                $contacts[] = $contactPoint;
            }
        }

        $defaultContact = mits_jsonld_contact_point(
          'MODULE_MITS_JSON_LD_TELEPHONE_DEFAULT',
          'MODULE_MITS_JSON_LD_EMAIL',
          'MODULE_MITS_JSON_LD_CONTACT_OPTION_DEFAULT',
          'customer service',
          mits_jsonld_contact_hours_available('MODULE_MITS_JSON_LD_CONTACT_HOURS_DEFAULT', $fallbackHoursAvailable)
        );

        if (!empty($defaultContact)) {
            $contacts[] = $defaultContact;
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
if ($showLocation) {
    $loc = [
      '@type'       => 'LocalBusiness',
      'name'        => mits_jsonld_sanitize(mits_ml('MODULE_MITS_JSON_LD_NAME')),
      '@id'         => $localBusinessId,
      'url'         => $homeUrl,
      'description' => mits_jsonld_sanitize(mits_ml('MODULE_MITS_JSON_LD_WEBSITE_DESCRIPTION')),
      'telephone'   => mits_jsonld_sanitize(mits_ml('MODULE_MITS_JSON_LD_TELEPHONE_DEFAULT')),
      'email'       => mits_jsonld_sanitize(mits_ml('MODULE_MITS_JSON_LD_EMAIL')),
      'address'     => mits_jsonld_location_address(),
      'founder'     => mits_jsonld_founder(),
      'foundingDate' => mits_jsonld_founding_date(),
      'priceRange'  => 'Varied'
    ];

    if ($showOrganization) {
        $loc['branchOf'] = [
          '@id' => $organizationId,
        ];
    }

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

    $contactUrl = mits_jsonld_content_url($shop_content_data);

    $schema = [
      '@type'       => 'ContactPage',
      '@id'         => $contactUrl . '#webpage',
      'url'         => $contactUrl,
      'name'        => mits_jsonld_sanitize($shop_content_data['content_title']),
      'description' => mits_jsonld_sanitize(decode_htmlentities($metaDescr)),
    ];

    if ($showWebsite) {
        $schema['isPartOf'] = [
          '@id' => $websiteId,
        ];
    }

    if ($showOrganization) {
        $schema['publisher'] = [
          '@id' => $organizationId,
        ];
    }

    $schema = mits_jsonld_clean($schema);
    mits_graph_add($schema);
}


/**
 * Allgemeine Content-Seiten: WebPage oder Article + FAQ/Custom-JSON
 */
if (
  mits_flag('MODULE_MITS_JSON_LD_SHOW_CONTENT')
  && ($mitsJsonLdContentData = mits_jsonld_get_content_data()) !== null
  && mits_jsonld_get_content_group($mitsJsonLdContentData) !== 7
) {
    $contentUrl = mits_jsonld_content_url($mitsJsonLdContentData);
    $customJsonNodes = $GLOBALS['mits_jsonld_custom_nodes'] ?? [];
    $faqQuestions = mits_jsonld_collect_global_faq_questions();

    $contentPage = mits_jsonld_build_content_webpage(
      $mitsJsonLdContentData,
      $contentUrl,
      $showWebsite,
      $websiteId,
      $showOrganization,
      $organizationId
    );

    $contentPage = mits_jsonld_merge_page_with_custom($contentPage, $customJsonNodes, $faqQuestions);
    mits_graph_add(mits_jsonld_clean($contentPage));

    $allFaqQuestions = mits_jsonld_collect_faq_questions($contentPage);
    if (!empty($allFaqQuestions)) {
        mits_jsonld_add_faq_page_for_url($contentUrl, $allFaqQuestions);
    }

    if (!empty($customJsonNodes)) {
        mits_jsonld_add_custom_nodes_to_graph(
          $customJsonNodes,
          ['webpage', 'article', 'faqpage', 'qapage', 'question']
        );
    }
}

/**
 * Kategorie-Seite: CollectionPage + ItemList der sichtbaren Produktliste.
 */
if (
  basename($PHP_SELF) === FILENAME_DEFAULT
  && mits_flag('MODULE_MITS_JSON_LD_SHOW_CATEGORY')
  && (isset($_GET['cPath']) || (isset($current_category_id) && (int)$current_category_id > 0))
) {
    global $module_content;

    $pageNumber = mits_jsonld_current_page_number();
    $categoryUrl = mits_jsonld_listing_page_url($pageNumber);
    $baseCategoryUrl = mits_jsonld_base_listing_url();
    $positionOffset = mits_jsonld_get_listing_position_offset();
    $categoryItems = [];

    if (isset($module_content) && is_array($module_content)) {
        $categoryItems = $module_content;
    } elseif (isset($GLOBALS['module_content']) && is_array($GLOBALS['module_content'])) {
        $categoryItems = $GLOBALS['module_content'];
    }

    $itemList = !empty($categoryItems)
      ? mits_jsonld_build_category_itemlist($categoryItems, $categoryUrl, $positionOffset)
      : null;

    $customJsonNodes = $GLOBALS['mits_jsonld_custom_nodes'] ?? [];
    $faqQuestions = mits_jsonld_collect_global_faq_questions();
    $collectionPage = mits_jsonld_build_category_collection_page($categoryUrl, $itemList, $baseCategoryUrl, $pageNumber);
    $collectionPage = mits_jsonld_merge_page_with_custom($collectionPage, $customJsonNodes, $faqQuestions);
    mits_graph_add(mits_jsonld_clean($collectionPage));

    if (!empty($itemList)) {
        mits_graph_add(mits_jsonld_clean($itemList));
    }

    $allFaqQuestions = array_merge($faqQuestions, mits_jsonld_collect_faq_questions($customJsonNodes));
    $allFaqQuestions = mits_jsonld_merge_questions([], $allFaqQuestions);
    if (!empty($allFaqQuestions)) {
        mits_jsonld_add_faq_page_for_url($categoryUrl, $allFaqQuestions);
    }

    if (!empty($customJsonNodes)) {
        mits_jsonld_add_custom_nodes_to_graph(
          $customJsonNodes,
          ['collectionpage', 'faqpage', 'qapage', 'question']
        );
    }
}

/**
 * Suchergebnisseite: CollectionPage + ItemList der sichtbaren Produktliste.
 */
if (
  mits_jsonld_is_search_results_page()
  && mits_flag('MODULE_MITS_JSON_LD_SHOW_SEARCH_RESULTS')
) {
    global $module_content;

    $pageNumber = mits_jsonld_current_page_number();
    $searchUrl = mits_jsonld_listing_page_url($pageNumber);
    $baseSearchUrl = mits_jsonld_base_listing_url();
    $positionOffset = mits_jsonld_get_listing_position_offset();
    $searchItems = [];

    if (isset($module_content) && is_array($module_content)) {
        $searchItems = $module_content;
    } elseif (isset($GLOBALS['module_content']) && is_array($GLOBALS['module_content'])) {
        $searchItems = $GLOBALS['module_content'];
    }

    $itemList = !empty($searchItems)
      ? mits_jsonld_build_listing_itemlist($searchItems, $searchUrl, $positionOffset)
      : null;

    $collectionPage = mits_jsonld_build_search_results_collection_page($searchUrl, $itemList, $baseSearchUrl, $pageNumber);
    mits_graph_add($collectionPage);

    if (!empty($itemList)) {
        mits_graph_add(mits_jsonld_clean($itemList));
    }
}


/**
 * @param object $product
 * @param array $productDataArray
 * @param array|null $productImagesSchema
 * @param string $productURL
 * @param bool $showWebsite
 * @param string $websiteId
 * @param bool $showOrganization
 * @param string $organizationId
 *
 * @return array
 */
function mits_jsonld_build_article_from_product(
  object $product,
  array $productDataArray,
  ?array $productImagesSchema,
  string $productURL,
  bool $showWebsite = false,
  string $websiteId = '',
  bool $showOrganization = false,
  string $organizationId = ''
): array {
    $article = [
      '@type'            => 'Article',
      '@id'              => $productURL . '#article',
      'url'              => $productURL,
      'name'             => mits_jsonld_sanitize($product->data['products_name'] ?? ''),
      'headline'         => mits_jsonld_sanitize($product->data['products_name'] ?? ''),
      'description'      => mits_jsonld_sanitize($product->data['products_description'] ?? ''),
      'image'            => $productImagesSchema,
      'mainEntityOfPage' => [
        '@type' => 'WebPage',
        '@id'   => $productURL . '#webpage',
      ],
    ];

    foreach (['products_date_added', 'date_added', 'products_date_available'] as $dateKey) {
        if (!empty($product->data[$dateKey])) {
            $article['datePublished'] = mits_jsonld_format_date($product->data[$dateKey]);
            break;
        }

        if (!empty($productDataArray[$dateKey])) {
            $article['datePublished'] = mits_jsonld_format_date($productDataArray[$dateKey]);
            break;
        }
    }

    foreach (['products_last_modified', 'last_modified', 'date_modified'] as $dateKey) {
        if (!empty($product->data[$dateKey])) {
            $article['dateModified'] = mits_jsonld_format_date($product->data[$dateKey]);
            break;
        }

        if (!empty($productDataArray[$dateKey])) {
            $article['dateModified'] = mits_jsonld_format_date($productDataArray[$dateKey]);
            break;
        }
    }

    if (!empty($_SESSION['language_code'])) {
        $article['inLanguage'] = $_SESSION['language_code'];
    } elseif (!empty($_SESSION['language'])) {
        $article['inLanguage'] = $_SESSION['language'];
    }

    if ($showWebsite && $websiteId !== '') {
        $article['isPartOf'] = [
          '@id' => $websiteId,
        ];
    }

    if ($showOrganization && $organizationId !== '') {
        $article['publisher'] = [
          '@id' => $organizationId,
        ];
        $article['author'] = [
          '@id' => $organizationId,
        ];
    }

    return mits_jsonld_clean($article);
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

    if (mits_jsonld_product_as_article_enabled()) {
        $schema = mits_jsonld_build_article_from_product(
          $product,
          is_array($productDataArray) ? $productDataArray : [],
          $productImagesSchema,
          $productURL,
          $showWebsite,
          $websiteId,
          $showOrganization,
          $organizationId
        );

        $productFaqQuestions = mits_jsonld_merge_questions(
          mits_jsonld_collect_global_faq_questions(),
          !empty($customJsonNodes) ? mits_jsonld_collect_faq_questions($customJsonNodes) : []
        );

        if (!empty($productFaqQuestions)) {
            mits_jsonld_add_faq_page_for_url($productURL, $productFaqQuestions);
        }

        mits_graph_add(mits_jsonld_clean($schema));

        if (!empty($customJsonNodes)) {
            mits_jsonld_add_custom_nodes_to_graph(
              $customJsonNodes,
              ['product', 'faqpage', 'qapage', 'question']
            );
        }
    } else {
        $productPageId = $productURL . '#webpage';
        $productSchemaId = $productURL . '#product';

        $schema = [
      '@type'            => 'Product',
      '@id'              => $productSchemaId,
      'url'              => $productURL,
      'mainEntityOfPage' => [
        '@type' => 'WebPage',
        '@id'   => $productPageId,
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
          'ratingValue' => $product->getReviewsAverage($product->data['products_id'], 1),
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

    $faqBaseQuestions = mits_jsonld_collect_global_faq_questions();

    $productFaqQuestions = !empty($faqBaseQuestions) ? $faqBaseQuestions : [];

    if (!empty($customJsonNodes)) {
        $schema = mits_jsonld_merge_product_with_custom(
          $schema,
          $customJsonNodes,
          !empty($faqBaseQuestions) ? $faqBaseQuestions : null
        );

        if (!empty($schema['_mits_jsonld_faq_questions']) && is_array($schema['_mits_jsonld_faq_questions'])) {
            $productFaqQuestions = $schema['_mits_jsonld_faq_questions'];
            unset($schema['_mits_jsonld_faq_questions']);
        }
    }

    if (!empty($productFaqQuestions)) {
        mits_jsonld_add_faq_page_for_url($productURL, $productFaqQuestions);
    }

    $schema = mits_jsonld_clean($schema);
    mits_graph_add($schema);

        if (!empty($customJsonNodes)) {
            mits_jsonld_add_custom_nodes_to_graph($customJsonNodes);
        }
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

if (
  empty($GLOBALS['mits_jsonld_faq_page_handled'])
  && isset($structuredFAQPageDataForJSON)
  && is_array($structuredFAQPageDataForJSON)
) {
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
    $mitsDebugJson = json_encode(
      mits_jsonld_normalize_for_json(['@context' => 'https://schema.org', '@graph' => $mitsJsonLdGraph]),
      JSON_PRETTY_PRINT | mits_jsonld_json_encode_flags(mits_jsonld_encoding_mode())
    );
    echo $mitsDebugJson !== false ? $mitsDebugJson : 'json_encode failed: ' . json_last_error_msg();
    echo "</pre>";

    echo "</div>";
}

mits_graph_output();
