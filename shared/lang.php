<?php
/*
TechBench dump
Copyright (C) 2024 TechBench dump website authors and contributors

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

   http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
*/
if(__DIR__ == getcwd() && basename($_SERVER['SCRIPT_NAME']) == basename(__FILE__)) {
    $v = $_SERVER['SERVER_PROTOCOL'];
    header("$v 403 Forbidden");
    exit();
}

require_once join(DIRECTORY_SEPARATOR, [dirname(__FILE__), '..', 'contrib', 'langconf.php']);
require_once join(DIRECTORY_SEPARATOR, [dirname(__FILE__), '..', 'contrib', 'langs', 'en-US.php']);
require_once join(DIRECTORY_SEPARATOR, [dirname(__FILE__), 'utils.php']);

$pageLanguageOptions = array(
    'expires' => time() + 60 * 60 * 24 * 30,
    'path' => '/',
    'domain' => getDomain(),
    'secure' => isset($_SERVER['HTTPS']) ? true : false,
    'httponly' => true,
    'samesite' => 'Strict'
);

$sendCookie = false;
if(isset($_GET['lang'])) {
    $lang = $_GET['lang'];
    $sendCookie = true;
} elseif(isset($_COOKIE['Page-Language'])) {
    $lang = $_COOKIE['Page-Language'];
    $sendCookie = true;
} elseif(isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) && strlen($_SERVER['HTTP_ACCEPT_LANGUAGE']) > 0) {
    // regex inspired from @GabrielAnderson on http://stackoverflow.com/questions/6038236/http-accept-language
    $acceptLanguage = preg_replace('/-Han[st]/', '', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
    preg_match_all('/([a-z]{1,8}(-[a-zA-Z]{1,8})*)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/i', $acceptLanguage, $lang_parse);
    $languageArray = $lang_parse[1];
    $q = $lang_parse[4]; 
    $qualityfactor = [];
    for($i=0; $i < count($languageArray); $i++) {
        if(isset($languageArray[$i]) && preg_match('/^[^-]*$/', $languageArray[$i])) {
            $languageArray[$i] = str_replace(array_keys($autoLangMappings), $autoLangMappings, $languageArray[$i]);
        }
        $languageArray = array_unique($languageArray);
        if(isset($languageArray[$i])) {
            $qualityfactor[$languageArray[$i]] = (float) (!empty($q[$i]) ? $q[$i] : 1);
        }
    }
    // comparison function for uksort (inspired from @200_success on https://codereview.stackexchange.com/questions/54948/detect-prefered-language)
    $cmpLangs = function ($a, $b) use ($qualityfactor) {
        if ($qualityfactor[$a] > $qualityfactor[$b])
            return -1;
        elseif ($qualityfactor[$a] < $qualityfactor[$b])
            return 1;
        elseif (strlen($a) > strlen($b))
            return -1;
        elseif (strlen($a) < strlen($b))
            return 1;
        else
            return 0;
    };

    // sort the languages by qualityfactor
    uksort($qualityfactor, $cmpLangs);

    $langArray = array_keys($qualityfactor);
    $langCount = count($qualityfactor);

    if(isset($langCount)) {
        for($langNum = 0; $langNum < $langCount; ++$langNum) {
            if(array_key_exists($langArray[$langNum], $supportedLangs)) break;
        }
        if(isset($langArray[$langNum])) {
            $lang = $langArray[$langNum];
        }
    }
} else {
    $lang = 'en-US';
}

if(!array_key_exists($lang, $supportedLangs)) {
    $lang = 'en-US';
}

require_once "contrib/langs/$lang.php";

if($sendCookie) {
    setcookie('Page-Language', $lang, $pageLanguageOptions);
}

$url = htmlentities(getUrlWithoutParam('lang'));
date_default_timezone_set($s['timeZone']);

$langItem = '';
foreach($supportedLangs as $lang => $localizedLang) {
    $langItem .= '        <li><a class="dropdown-item" href="' . $url . 'lang=' . $lang . '"><img src="contrib/flags/' . $lang . '.png">  ' . $localizedLang . '</a></li>';
}

$langCore_menu = <<<EOD
<li class="nav-item dropdown">
    <a class="nav-link text-nowrap dropdown-toggle ms-2" data-bs-toggle="dropdown" role="button">
        <img src="contrib/flags/{$s['langCode']}.png" style="margin-bottom: 3px;"> {$s['langNameLocal']} 
    </a>
    <ul class="dropdown-menu">
$langItem
    </ul>
</li>
EOD;
?>
