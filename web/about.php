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

<h3><span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span> <?php echo $translation['aboutPageTitle'];?></p></h3>
<p><?php echo $translation['aboutPageContent'];?></p>

<h3><span class="glyphicon glyphicon-thumbs-up" aria-hidden="true"></span> <?php echo $translation['aboutThanksTitle'];?></p></h3>
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
        <td><img src="lang/flags/US.png"> English</td>
        <td>mkuba50</td>
    </tr>
    <tr>
        <td><img src="lang/flags/PL.png"> Polski</td>
        <td>mkuba50</td>
    </tr>
</table>

<?php styleBottom(); ?>
