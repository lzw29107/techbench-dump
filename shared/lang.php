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

require_once dirname(__FILE__).'/../contrib/langconf.php';
require_once dirname(__FILE__).'/../contrib/langs/en-US.php';
require_once 'shared/utils.php';

if($_SERVER['SERVER_NAME'] == '0.0.0.0') {
    $domain = preg_replace('/:.*/', '', $_SERVER['HTTP_HOST']);
} else {
    $domain = $_SERVER['SERVER_NAME'];
}

$pageLanguageOptions = array(
    'expires' => time()+60*60*24*30,
    'path' => '/',
    'domain' => $domain,
    'secure' => isset($_SERVER['HTTPS']) ? true : false,
    'httponly' => true,
    'samesite' => 'Strict'
);

$sendCookie = false;
if(isset($_GET['lang'])) {
    $lang = strtolower($_GET['lang']);
    $sendCookie = true;
} elseif(isset($_COOKIE['Page-Language'])) {
    $lang = strtolower($_COOKIE['Page-Language']);
    $sendCookie = true;
} elseif(isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) && strlen($_SERVER['HTTP_ACCEPT_LANGUAGE']) > 0) {
    // regex inspired from @GabrielAnderson on http://stackoverflow.com/questions/6038236/http-accept-language
    $acceptLanguage = preg_replace('/-han[st]/', '', strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE']));
    preg_match_all('/([a-z]{1,8}(-[a-z]{1,8})*)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/i', $acceptLanguage, $lang_parse);
    $languageArray = $lang_parse[1];
    $q = $lang_parse[4];
    $lcount = count($languageArray); 
    $qualityfactor = array();
    for($i=0; $i<$lcount; $i++) {
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

    $LangArray = array_keys($qualityfactor);
    $langcount = count($qualityfactor); 
    if(isset($langcount)) {
        for($langnum = 0; $langnum<$langcount; ++$langnum) {
            if(in_array($LangArray[$langnum], $supportedLangs)) break;
        }
        if(isset($LangArray[$langnum])) {
            $lang = $LangArray[$langnum];
        }
    }
} else {
    $lang = 'en-us';
}

if(!in_array("$lang", $supportedLangs)) {
    $lang = 'en-us';
}

$lang = preg_replace_callback('/-[a-z]{2}$/', function($matches) {
    return strtoupper($matches[0]);
}, $lang);

require_once "contrib/langs/$lang.php";

if($sendCookie) {
    setcookie('Page-Language', $lang, $pageLanguageOptions);
}

$url = htmlentities(getUrlWithoutParam('lang'));
date_default_timezone_set($s['timeZone']);

$langCore_menu = <<<EOD
<li class="nav-item dropdown">
    <a class="nav-link text-nowrap dropdown-toggle ms-2" data-bs-toggle="dropdown" role="button">
        <img src="contrib/flags/{$s['langCode']}.png" style="margin-bottom: 3px;"> {$s['langNameLocal']} 
    </a>
    <ul class="dropdown-menu">
        <li><a class="dropdown-item" href="{$url}lang=de-DE"><img src="contrib/flags/de-DE.png">  Deutsch</a></li>
        <li><a class="dropdown-item" href="{$url}lang=en-US"><img src="contrib/flags/en-US.png">  English (US)</a></li>
        <li><a class="dropdown-item" href="{$url}lang=es-ES"><img src="contrib/flags/es-ES.png">  Español (España)</a></li>
        <li><a class="dropdown-item" href="{$url}lang=fr-FR"><img src="contrib/flags/fr-FR.png">  Français</a></li>
        <li><a class="dropdown-item" href="{$url}lang=it-IT"><img src="contrib/flags/it-IT.png">  Italiano</a></li>
        <li><a class="dropdown-item" href="{$url}lang=nl-NL"><img src="contrib/flags/nl-NL.png">  Nederlands</a></li>
        <li><a class="dropdown-item" href="{$url}lang=pl-PL"><img src="contrib/flags/pl-PL.png">  Polski</a></li>
        <li><a class="dropdown-item" href="{$url}lang=pt-BR"><img src="contrib/flags/pt-BR.png">  Português (Brasil)</a></li>
        <li><a class="dropdown-item" href="{$url}lang=ru-RU"><img src="contrib/flags/ru-RU.png">  Русский</a></li>
        <li><a class="dropdown-item" href="{$url}lang=ar-EG"><img src="contrib/flags/ar-EG.png">  العربية</a></li>
        <li><a class="dropdown-item" href="{$url}lang=th-TH"><img src="contrib/flags/th-TH.png">  ภาษาไทย</a></li>
        <li><a class="dropdown-item" href="{$url}lang=ja-JP"><img src="contrib/flags/ja-JP.png">  日本語</a></li>
        <li><a class="dropdown-item" href="{$url}lang=zh-CN"><img src="contrib/flags/zh-CN.png">  中文（简体）</a></li>
        <li><a class="dropdown-item" href="{$url}lang=zh-TW"><img src="contrib/flags/zh-TW.png">  中文（繁體）</a></li>
        <li><a class="dropdown-item" href="{$url}lang=qps-ploc"><img src="contrib/flags/qps-ploc.png">  [ !!! Ƥşḗŭḓǿ !!! ]</a></li>
    </ul>
</li>
EOD;

?>
