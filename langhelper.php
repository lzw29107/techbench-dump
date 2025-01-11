<?php
if(php_sapi_name() != 'cli') {
    $v = $_SERVER['SERVER_PROTOCOL'];
    header("$v 403 Forbidden");
    exit();
}
require_once join(DIRECTORY_SEPARATOR, [dirname(__FILE__), 'contrib', 'langconf.php']);
require_once join(DIRECTORY_SEPARATOR, [dirname(__FILE__), 'shared', 'utils.php']);

$languages = [
    'ar-SA',
    'bg-BG',
    'cs-CZ',
    'da-DK',
    'de-DE',
    'el-GR',
    'en-AU',
    'en-CA',
    'en-GB',
    'en-IN',
    'en-US',
    'es-ES',
    'es-MX',
    'et-EE',
    'fi-FI',
    'fr-CA',
    'fr-FR',
    'he-IL',
    'hr-HR',
    'hu-HU',
    'it-IT',
    'ja-JP',
    'ko-KR',
    'lt-LT',
    'lv-LV',
    'nb-NO',
    'nl-NL',
    'pl-PL',
    'pt-BR',
    'pt-PT',
    'ro-RO',
    'ru-RU',
    'sk-SK',
    'sl-SI',
    'sv-SE',
    'th-TH',
    'tr-TR',
    'uk-UA',
    'zh-CN',
    'zh-HK',
    'zh-TW'
];

function generateLang($language) {
    global $sessionId, $enLangNames;
    $msStrings = [
        'selectLang' => '',
        'selectLangDesc' => '',
        'selectNotice' => '',
        'selectPlaceholder' => '',
        'confirm' => '',
        'download' => '',
        'downLinks' => '',
        'linksNotice' => '',
        'linksExpire' => '',
        'langName' => [
            'ar-SA' => 'Arabic',
            'bg-BG' => 'Bulgarian',
            'cs-CZ' => 'Czech',
            'da-DK' => 'Danish',
            'de-DE' => 'German',
            'en-GB' => 'English International',
            'en-US' => 'English (United States)',
            'es-ES' => 'Spanish',
            'es-MX' => 'Spanish (Mexico)',
            'et-EE' => 'Estonian',
            'fi-FI' => 'Finnish',
            'fr-CA' => 'French Canadian',
            'fr-FR' => 'French',
            'el-GR' => 'Greek',
            'he-IL' => 'Hebrew',
            'hi-IN' => 'Hindi',
            'hr-HR' => 'Croatian',
            'hu-HU' => 'Hungarian',
            'it-IT' => 'Italian',
            'ja-JP' => 'Japanese',
            'kk-KZ' => 'Kazakh',
            'ko-KR' => 'Korean',
            'lv-LV' => 'Latvian',
            'lt-LT' => 'Lithuanian',
            'nb-NO' => 'Norwegian',
            'nl-NL' => 'Dutch',
            'pl-PL' => 'Polish',
            'pt-BR' => 'Brazilian Portuguese',
            'pt-PT' => 'Portuguese',
            'ro-RO' => 'Romanian',
            'ru-RU' => 'Russian',
            'sr-Latn-RS' => 'Serbian Latin',
            'sk-SK' => 'Slovak',
            'sl-SI' => 'Slovenian',
            'sv-SE' => 'Swedish',
            'th-TH' => 'Thai',
            'tr-TR' => 'Turkish',
            'uk-UA' => 'Ukrainian',
            'zh-CN' => 'Chinese Simplified',
            'zh-HK' => 'Chinese Traditional Hong Kong',
            'zh-TW' => 'Chinese Traditional',
            'Multiple' => 'Multiple'
        ]
    ];

    echo "Current Language: $language\n";
    echo "Generating Session ID...\n";
    $sessionId = genSessionId();
    echo "Session ID: $sessionId\n";
    echo "Fetch select strings...\n";
    $time = time();
    $data = false;
    while(!$data) {
        if(time() - $time > 120) exit("Error: Timeout.\n");
        $data = getInfo(2, 'Page', lang: $language);
    }
    $html = new DOMDocument();
    @$html->loadHTML($data);
    if(!$html) {
        exit("Error: Unknown Error.\n");
    }
    $select = $html->getElementById('SDS_LanguageSelectionByProductEdition');
    if(!$select) {
        echo "Error: Unknown Error.\n";
        return false;
    } else {
        $msStrings['selectPlaceholder'] = trim($html->getElementById('product-languages')->textContent);
        $texts = $select->getElementsByTagName('p');
        $desc = $texts->item(1);
        $msStrings['selectLangDesc'] = preg_replace('/<p>\s*(.*)\s*<\/p>/', '$1', $html->saveHTML($desc));
        $msStrings['confirm'] = $html->getElementById('submit-sku')->textContent;
        $msStrings['selectNotice'] = $html->getElementById('product-languages-error')->textContent;
        $msStrings['selectLang'] = $texts->item(0)->textContent;
    }
    $download = $html->getElementById('SoftwareDownload_DownloadLinks');
    if(!$download) {
        echo "Error: Unknown Error.\n";
        return false;
    } else {
        $msStrings['download'] = preg_replace('/(.*){{GetProductDownloadLinksBySku.ProductDownloadOptions.0.LocalizedProductDisplayName}}/', '$1', $download->getElementsByTagName('h2')->item(0)->textContent);
        $msStrings['downLinks'] = $html->getElementById('download-links')->textContent;
        $texts = $download->getElementsByTagName('em');
        $msStrings['linksNotice'] = $texts->item(0)->textContent;
        $msStrings['linksExpire'] = preg_replace('/(.*){{GetProductDownloadLinksBySku.DownloadExpirationDatetime}} UTC/', '$1', $texts->item(1)->textContent);
    }

    echo "Fetch language strings...\n";
    foreach([4, 188, 3131, 3140] as $productId) {
        $time = time();
        $data = false;
        while(!$data) {
            if(time() - $time > 120) exit("Error: Timeout.\n");
            $data = getInfo(2, 'Prod', $productId, $language);
        }

        $info = json_decode($data, true);
        if($info) {
            if(array_key_exists('Errors', $info)) {
                $errorMsg = $info['Errors'][0]['Value'];
                $errorType = $info['Errors'][0]['Type'];
                if($errorType == 10) {
                    if($productId == 3140) {
                        echo "Warning: Non en-US languages do not have the 'Multiple' string.\n";
                    } else {
                        echo "Warning: $errorMsg\n";
                    }
                } else {
                    exit("Error: $errorMsg\n");
                }
            } else {
                foreach($info['Skus'] as $sku) {
                    $lang = in_subArray($sku['Language'], $enLangNames);
                    if($lang) {
                        $msStrings['langName'][$lang] = $sku['LocalizedLanguage'];
                    }
                }
            }
        }
    }
    return $msStrings;
}

$strings = [];
foreach($languages as $language)
{
    $result = generateLang($language);
    if($result) {
        $strings[$language] = $result;
    } else {
        echo "Warning: Unsupported Language: $language\n";
    }
}
file_put_contents('lang.json', json_encode($strings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
?>