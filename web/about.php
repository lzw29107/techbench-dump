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

$lang = isset($_GET['lang']) ? $_GET['lang'] : 'en-us';
require 'lang/core.php';
require 'shared/style.php';

styleTop('about');
echo '<h1>'.$translation['tbDump'].'</h1>';
?>

<h3><span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span> <?php echo $translation['aboutPageTitle'];?></h3>
<p><?php echo $translation['aboutPageContent'];?></p>

<h3><span class="glyphicon glyphicon-thumbs-up" aria-hidden="true"></span> <?php echo $translation['aboutThanksTitle'];?></h3>
<p><?php echo $translation['aboutThanksContent'];?></p>

<h3><span class="glyphicon glyphicon-globe" aria-hidden="true"></span> <?php echo $translation['aboutTranslationsTitle'];?></h3>
<table class="table table-striped">
    <thead>
        <tr>
            <th><?php echo $translation['language'];?></th>
            <th><?php echo $translation['authors'];?></th>
        </tr>
    </thead>
    <tr>
        <td><img src="lang/flags/en-US.png"> English (US)</td>
        <td>mkuba50</td>
    </tr>
    <tr>
        <td><img src="lang/flags/es-ES.png"> Español (España)</td>
        <td><a href="https://forums.mydigitallife.info/members/417886-antonio8909">antonio8909</a></td>
    </tr>
    <tr>
        <td><img src="lang/flags/fr-FR.png"> Français</td>
        <td><a href="https://forums.mydigitallife.info/members/476049-NeXtStatioN">NeXtStatioN (@AniMachin3)</a></td>
    </tr>
    <tr>
        <td><img src="lang/flags/it-IT.png"> Italiano</td>
        <td><a href="https://forums.mydigitallife.info/members/6748-garf02">garf02</a></td>
    </tr>
    <tr>
        <td><img src="lang/flags/nl-NL.png"> Nederlands</td>
        <td><a href="https://forums.mydigitallife.info/members/104688-Enthousiast">Enthousiast</a></td>
    </tr>
    <tr>
        <td><img src="lang/flags/pl-PL.png"> Polski</td>
        <td>mkuba50</td>
    </tr>
    <tr>
        <td><img src="lang/flags/ru-RU.png"> Русский</td>
        <td><a href="https://forums.mydigitallife.info/members/381582-adguard">adguard</a></td>
    </tr>
    <tr>
        <td><img src="lang/flags/th-TH.png"> ภาษาไทย</td>
        <td><a href="https://forums.mydigitallife.info/members/418421-Phairat">Phairat</a></td>
    </tr>
    <tr>
        <td><img src="lang/flags/ja-JP.png"> 日本語</td>
        <td><a href="https://forums.mydigitallife.info/members/476049-NeXtStatioN">NeXtStatioN (@AniMachin3)</a></td>
    </tr>
    <tr>
        <td><img src="lang/flags/qps-ploc.png"> [ !!! Ƥşḗŭḓǿ !!! ]</td>
        <td>mkuba50</td>
    </tr>
</table>

<?php styleBottom(); ?>
