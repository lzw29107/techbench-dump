<?php
// TechBench dump
// Copyright (C) 2017  mkuba50

// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.

// You should have received a copy of the GNU General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.

if (empty($lang)) {
    $lang = 'en-US';
}

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
    default:
        require 'langs/en-US.php';
        break;
}

$langParam='lang='.$translation['langCode'];
$langCore_menu = '<li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" role="button"><img src="lang/flags/'.$translation['langCode'].'.png"> '.$translation['langNameLocal'].' <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="./?lang=en-US"><img src="lang/flags/en-US.png">&nbsp;English (US)</a></li>
                                <li><a href="./?lang=es-ES"><img src="lang/flags/es-ES.png">&nbsp;Español (España)</a></li>
                                <li><a href="./?lang=fr-FR"><img src="lang/flags/fr-FR.png">&nbsp;Français</a></li>
                                <li><a href="./?lang=it-IT"><img src="lang/flags/it-IT.png">&nbsp;Italiano</a></li>
                                <li><a href="./?lang=nl-NL"><img src="lang/flags/nl-NL.png">&nbsp;Nederlands</a></li>
                                <li><a href="./?lang=pl-PL"><img src="lang/flags/pl-PL.png">&nbsp;Polski</a></li>
                                <li><a href="./?lang=ru-RU"><img src="lang/flags/ru-RU.png">&nbsp;Русский</a></li>
                                <li><a href="./?lang=th-TH"><img src="lang/flags/th-TH.png">&nbsp;ภาษาไทย</a></li>
                                <li><a href="./?lang=ja-JP"><img src="lang/flags/ja-JP.png">&nbsp;日本語</a></li>
                            </ul>
                  </li>';
?>
