<?php
// TechBench dump
// Copyright (C) 2017  mkuba50

// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

// Additional terms to GPLv3 license apply, see LICENSE.txt file or
// <https://github.com/techbench-dump/website/blob/master/LICENSE.txt>.

// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.

// You should have received a copy of the GNU General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.

$prodName = isset($_GET['prod']) ? $_GET['prod'] : 'all';
$lang = isset($_GET['lang']) ? $_GET['lang'] : 'en-us';
require 'lang/core.php';
require 'shared/style.php';

$out = @file_get_contents('dump.json');
if(empty($out)) {
    $out = array('genTime' => null, 'productNumber' => '?', 'products' => array());
} else {
    $out = json_decode($out, true);   
}

$prodName = strtolower($prodName);
switch ($prodName) {
    case 'win7':
        $products = preg_grep('/Windows.7/',$out['products']);
        $selectedCategory = $translation['win7'];
        break;
    case 'win81':
        $products = preg_grep('/Windows.8\.1/',$out['products']);
        $selectedCategory = $translation['win81'];
        break;
    case 'win10':
        $products = preg_grep('/Windows.10/',$out['products']);
        $selectedCategory = $translation['win10'];
        break;
    case 'win10th1':
        $products = preg_grep('/Windows.10.*?Threshold.1/',$out['products']);
        $selectedCategory = $translation['win10th1'];
        break;
    case 'win10th2':
        $products = preg_grep('/Windows.10.*?Threshold.2/',$out['products']);
        $selectedCategory = $translation['win10th2'];
        break;
    case 'win10rs1':
        $products = preg_grep('/Windows.10.*?Redstone.1/',$out['products']);
        $selectedCategory = $translation['win10rs1'];
        break;
    case 'win10ip':
        $products = preg_grep('/Windows.*?Insider.?Preview/',$out['products']);
        $selectedCategory = $translation['win10ip'];
        break;
    case 'office2007':
        $products = preg_grep('/ 2007/',$out['products']);
        $selectedCategory = $translation['office2007'];
        break;
    case 'office2010':
        $products = preg_grep('/ 2010/',$out['products']);
        $selectedCategory = $translation['office2010'];
        break;
    case 'office2011':
        $products = preg_grep('/ 2011/',$out['products']);
        $selectedCategory = $translation['office2011'];
        break;
    case 'all':
        $selectedCategory = $translation['allProd'];
        $products = $out['products'];
        break;
    case 'other':
        $selectedCategory = $translation['otherProd'];
        $products = $out['products'];
        foreach($products as $key => &$curr){
            $check = preg_match('/Windows.7|Windows.8\.1|Windows.10| 2007| 2010| 2011/', $curr);
            if($check){
              unset($products[$key]);
            }
        }
        break;
    default:
        $selectedCategory = $translation['allProd'];
        $products = $out['products'];
        break;
}
styleTop('downloads');

echo '<h1>'.$translation['tbDumpDownload']."</h1>\n";
echo "<h3><span class=\"glyphicon glyphicon-th-list\" aria-hidden=\"true\"></span> $selectedCategory</h3>\n";
echo '<table class="table table-striped">';
echo '<thead><tr><th>'.$translation['prodSelect']."</th></tr></thead>\n";
                
if(empty($products)) {
    echo '<tr><td>'.$translation['noProducts'].'</td></tr>';
} else {                
    foreach ($products as $key => &$curr) {
        echo '<tr><td><a href="./langs.php?id='.$key.'&'.$langParam.'">'.$curr .' ['.$translation['idName'].': '.$key."]</a></td></tr>\n";
    }
}

echo '</table>';
styleBottom();
?>
