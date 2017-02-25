<?php
// TechBench dump
// Copyright (C) 2017  mkuba50

// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

// Additional terms to GPLv3 license apply, see LICENSE.txt file or
// <https://gitlab.com/mkuba50/techbench-dump-web/blob/master/LICENSE.txt>.

// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.

// You should have received a copy of the GNU General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.

$prodId = isset($_GET['id']) ? $_GET['id'] : '2';
$lang = isset($_GET['lang']) ? $_GET['lang'] : 'en-us';
require 'lang/core.php';
require 'shared/get.php';
require 'shared/style.php';

$out = @file_get_contents('dump.json');
if(empty($out)) {
    $out = array('products' => null);
} else {
    $out = json_decode($out, true);   
}

$products = $out['products'];
if(empty($products[$prodId]))
{
    $products = $translation['unknownName'] .' ['.$translation['idName'].': '.$prodId.']';
} else {
    $products = $products[$prodId];
}

$langList = getLangList($prodId, $translation['langCodeMs']);
if(isset($langList['error'])) {
    echo 'There was an error processing your request.';
    die();
}

styleTop('downloads');

echo '<h1>'.$translation['tbDumpDownload']."</h1>\n";
echo "<h3><span class=\"glyphicon glyphicon-th-list\" aria-hidden=\"true\"></span> $products</h3>\n";
echo '<table class="table table-striped">';
echo '<thead><tr><th>'.$translation['prodLangSelect']."</th></tr></thead>\n";
foreach ($langList['langs'] as &$curr) {
    echo '<tr><td><a href="./get.php?skuId='.$curr['id'].'&'.$langParam.'">' . $curr['langLocal'] . "</a></td></tr>\n";
}
echo '</table>';

styleBottom();
?>
