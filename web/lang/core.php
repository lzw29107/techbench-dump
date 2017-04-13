<?php
// Copyright 2017 mkuba50

// Licensed under the Apache License, Version 2.0 (the "License");
// you may not use this file except in compliance with the License.
// You may obtain a copy of the License at

//    http://www.apache.org/licenses/LICENSE-2.0

// Unless required by applicable law or agreed to in writing, software
// distributed under the License is distributed on an "AS IS" BASIS,
// WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
// See the License for the specific language governing permissions and
// limitations under the License.

if (empty($lang)) {
    $lang = 'en-US';
}

require 'langs/en-US.php';
$lang = strtolower($lang);
switch ($lang) {
    case 'en-us':
        require 'langs/en-US.php';
        break;
    case 'pl-pl':
        require 'langs/pl-PL.php';
        break;
    case 'nl-nl':
        require 'langs/nl-NL.php';
        break;
    case 'es-es':
        require 'langs/es-ES.php';
        break;
    case 'ru-ru':
        require 'langs/ru-RU.php';
        break;
    case 'fr-fr':
        require 'langs/fr-FR.php';
        break;
    case 'ja-jp':
        require 'langs/ja-JP.php';
        break;
    case 'th-th':
        require 'langs/th-TH.php';
        break;
    case 'it-it':
        require 'langs/it-IT.php';
        break;
    case 'zh-cn':
        require 'langs/zh-CN.php';
        break;
    case 'zh-tw':
        require 'langs/zh-TW.php';
        break;
    case 'ar-eg':
        require 'langs/ar-EG.php';
        break;
    case 'qps-ploc':
        require 'langs/qps-ploc.php';
        break;
    default:
        require 'langs/en-US.php';
        break;
}

date_default_timezone_set($translation['timeZone']);
$langParam='lang='.$translation['langCode'];
$langCore_menu = '<li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" role="button"><img src="lang/flags/'.$translation['langCode'].'.png" style="margin-top: -2px;"> '.$translation['langNameLocal'].' <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="./?lang=en-US"><img src="lang/flags/en-US.png">&nbsp;English (US)</a></li>
                                <li><a href="./?lang=es-ES"><img src="lang/flags/es-ES.png">&nbsp;Español (España)</a></li>
                                <li><a href="./?lang=fr-FR"><img src="lang/flags/fr-FR.png">&nbsp;Français</a></li>
                                <li><a href="./?lang=it-IT"><img src="lang/flags/it-IT.png">&nbsp;Italiano</a></li>
                                <li><a href="./?lang=nl-NL"><img src="lang/flags/nl-NL.png">&nbsp;Nederlands</a></li>
                                <li><a href="./?lang=pl-PL"><img src="lang/flags/pl-PL.png">&nbsp;Polski</a></li>
                                <li><a href="./?lang=ru-RU"><img src="lang/flags/ru-RU.png">&nbsp;Русский</a></li>
                                <li><a href="./?lang=ar-EG"><img src="lang/flags/ar-EG.png">&nbsp;العربية</a></li>
                                <li><a href="./?lang=th-TH"><img src="lang/flags/th-TH.png">&nbsp;ภาษาไทย</a></li>
                                <li><a href="./?lang=ja-JP"><img src="lang/flags/ja-JP.png">&nbsp;日本語</a></li>
                                <li><a href="./?lang=zh-CN"><img src="lang/flags/zh-CN.png">&nbsp;简体中文</a></li>
                                <li><a href="./?lang=zh-TW"><img src="lang/flags/zh-TW.png">&nbsp;繁體中文</a></li>
                                <li><a href="./?lang=qps-ploc"><img src="lang/flags/qps-ploc.png">&nbsp;[ !!! Ƥşḗŭḓǿ !!! ]</a></li>
                            </ul>
                  </li>';
?>
