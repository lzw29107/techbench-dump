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
    'de-DE' => 'Deutsch',
    'en-US' => 'English (United States)',
    'es-ES' => 'Español (España)',
    'fr-FR' => 'Français',
    'it-IT' => 'Italiano',
    'nl-NL' => 'Nederlands',
    'pl-PL' => 'Polski',
    'pt-BR' => 'Português (Brasil)',
    'ru-RU' => 'Русский',
    'ar-EG' => 'العربية',
    'th-TH' => 'ภาษาไทย',
    'ja-JP' => '日本語',
    'zh-CN' => '简体中文',
    'zh-TW' => '繁体中文',
    'qps-ploc' => '[ !!! Ƥşḗŭḓǿ !!! ]'
];

/*
List of languages automatically set from user's browser configuration.

These mappings were made only for somewhat complete translations on purpose as
it's better to not force half translated website down to people's throats.
*/
$autoLangMappings = [
    'ar' => 'ar-EG',
    'de' => 'de-DE',
    'en' => 'en-US',
    'es' => 'es-ES',
    'fr' => 'fr-FR',
    'it' => 'it-IT',
    'ja' => 'ja-JP',
    'nl' => 'nl-NL',
    'pl' => 'pl-PL',
    'pt' => 'pt-BR',
    'qps' => 'qps-ploc',
    'ru' => 'ru-RU',
    'th' => 'th-TH',
    'zh' => 'zh-CN',
];

$enLangNames = [
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
    'zh-CN' => ['Chinese (Simplified)', 'Chinese Simplified'],
    'zh-HK' => ['Chinese (Traditional Hong Kong)', 'Chinese Traditional Hong Kong'],
    'zh-TW' => ['Chinese (Traditional)', 'Chinese Traditional'],
    'Multiple' => ['Multiple']
];