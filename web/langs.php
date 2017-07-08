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

$guid = randStr(8).'-'.randStr(4).'-'.randStr(4).'-'.randStr(4).'-'.randStr(12);

$langList = getLangList($prodId, $translation['langCodeMs'], $guid);
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
    echo '<tr><td><a href="./get.php?skuId='.$curr['id'].'&sessionId='.$guid.'&'.$langParam.'">' . $curr['langLocal'] . "</a></td></tr>\n";
}
echo '</table>';

styleBottom();
?>
