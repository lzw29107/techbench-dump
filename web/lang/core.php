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
    default:
        require 'langs/en-US.php';
        break;
}

$langParam='lang='.$translation['langCode'];
$langCore_menu = '<li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" role="button">'. $translation['langMenu'] . ' <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="./?lang=en-US"><img src="lang/flags/US.png">&nbsp;English (US)</a></li>
                                <li><a href="./?lang=es-ES"><img src="lang/flags/ES.png">&nbsp;Español (España)</a></li>
                                <li><a href="./?lang=nl-NL"><img src="lang/flags/NL.png">&nbsp;Nederlands</a></li>
                                <li><a href="./?lang=pl-PL"><img src="lang/flags/PL.png">&nbsp;Polski</a></li>
                                <li><a href="./?lang=ru-RU"><img src="lang/flags/RU.png">&nbsp;Русский</a></li>
                            </ul>
                  </li>';
?>
