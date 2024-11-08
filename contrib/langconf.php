<?php
// TechBench dump
// Copyright (C) 2024  TechBench dump website authors and contributors

// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

// Additional terms to GPLv3 license apply, see LICENSE.txt file or
// <https://github.com/lzw29107/techbench-dump/blob/master/LICENSE.txt>.

// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.

// You should have received a copy of the GNU General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.

//List of languages supported by the website
$supportedLangs = [
    'ar-eg',
    'de-de',
    'en-us',
    'es-es',
    'fr-fr',
    'it-it',
    'ja-jp',
    'nl-nl',
    'pl-pl',
    'pt-br',
    'qps-ploc',
    'ru-ru',
    'th-th',
    'zh-cn',
    'zh-tw',
];

/*
List of languages automatically set from user's browser configuration.

These mappings were made only for somewhat complete translations on purpose as
it's better to not force half translated website down to people's throats.
*/
$autoLangMappings = [
    'ar' => 'ar-eg',
    'de' => 'de-de',
    'en' => 'en-us',
    'es' => 'es-es',
    'fr' => 'fr-fr',
    'it' => 'it-it',
    'ja' => 'ja-jp',
    'nl' => 'nl-nl',
    'pl' => 'pl-pl',
    'pt' => 'pt-br',
    'qps' => 'qps-ploc',
    'ru' => 'ru-ru',
    'th' => 'th-th',
    'zh' => 'zh-cn',
];

$enLangName = [
    'ar-SA' => ['Arabic'],
    'bg-BG' => ['Bulgarian'],
    'cs-CZ' => ['Czech'],
    'da-DK' => ['Danish'],
    'de-DE' => ['German'],
    'en-GB' => ['English (United Kingdom)', 'English International'],
    'en-US' => ['English', 'English (United States)'],
    'es-ES' => ['Spanish'],
    'es-MX' => ['Spanish (Mexico)'],
    'et-EE' => ['Estonian'],
    'fi-FI' => ['Finnish'],
    'fr-CA' => ['French Canadian'],
    'fr-FR' => ['French'],
    'el-GR' => ['Greek'],
    'he-IL' => ['Hebrew'],
    'hi-IN' => ['Hindi'],
    'hr-HR' => ['Croatian'],
    'hu-HU' => ['Hungarian'],
    'it-IT' => ['Italian'],
    'ja-JP' => ['Japanese'],
    'kk-KZ' => ['Kazakh'],
    'ko-KR' => ['Korean'],
    'lv-LV' => ['Latvian'],
    'lt-LT' => ['Lithuanian'],
    'nb-NO' => ['Norwegian'],
    'nl-NL' => ['Dutch'],
    'pl-PL' => ['Polish'],
    'pt-BR' => ['Brazilian Portuguese'],
    'pt-PT' => ['Portuguese'],
    'ro-RO' => ['Romanian'],
    'ru-RU' => ['Russian'],
    'sr-Latn-RS' => ['Serbian Latin'],
    'sk-SK' => ['Slovak'],
    'sl-SI' => ['Slovenian'],
    'sv-SE' => ['Swedish'],
    'th-TH' => ['Thai'],
    'tr-TR' => ['Turkish'],
    'uk-UA' => ['Ukrainian'],
    'vi-VN' => ['Vietnamese'],
    'zh-CN' => ['Chinese (Simplified)', 'Chinese Simplified'],
    'zh-HK' => ['Chinese (Traditional Hong Kong)', 'Chinese Traditional Hong Kong'],
    'zh-TW' => ['Chinese (Traditional)', 'Chinese Traditional'],
    'Multiple' => ['Multiple']
];