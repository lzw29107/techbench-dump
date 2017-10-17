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

$prodName = isset($_GET['prod']) ? $_GET['prod'] : 'all';
$search = isset($_GET['search']) ? $_GET['search'] : null;
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
        $products = preg_grep('/Windows.10.*?Redstone.1|Windows.*?Build 14393/',$out['products']);
        $selectedCategory = $translation['win10rs1'];
        break;
    case 'win10rs2':
        $products = preg_grep('/Windows.10.*?Redstone.2|Windows.*? 15063/',$out['products']);
        $selectedCategory = $translation['win10rs2'];
        break;
    case 'win10rs3':
        $products = preg_grep('/Windows.10.*?1709/',$out['products']);
        $selectedCategory = $translation['win10rs3'];
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

if(!empty($search)) {
    $searchSafe = str_replace('\\', '\\\\', $search);
    $searchSafe = str_replace('/', '\/', $searchSafe);
    $searchSafe = str_replace('.', '\.', $searchSafe);
    $searchSafe = str_replace('^', '\^', $searchSafe);
    $searchSafe = str_replace('$', '\$', $searchSafe);
    $searchSafe = str_replace('*', '\*', $searchSafe);
    $searchSafe = str_replace('+', '\+', $searchSafe);
    $searchSafe = str_replace('-', '\-', $searchSafe);
    $searchSafe = str_replace('?', '\?', $searchSafe);
    $searchSafe = str_replace('(', '\(', $searchSafe);
    $searchSafe = str_replace(')', '\)', $searchSafe);
    $searchSafe = str_replace('[', '\[', $searchSafe);
    $searchSafe = str_replace(']', '\]', $searchSafe);
    $searchSafe = str_replace('{', '\{', $searchSafe);
    $searchSafe = str_replace('}', '\}', $searchSafe);
    $searchSafe = str_replace('|', '\|', $searchSafe);

    if (!preg_match('/^".*"$/', $searchSafe)) {
        $searchSafe = str_replace(' ', '.*', $searchSafe);
    } else {
        $searchSafe = preg_replace('/^"|"$/', '', $searchSafe);
    }

    $products = preg_grep('/.*'.$searchSafe.'.*/i',$products);

    $tableTitle = $translation['searchResults'].': '.$search;
    $noItems = $translation['searchNoResults'];
} else {
    $tableTitle = $translation['prodSelect'];
    $noItems = $translation['noProducts'];
}

styleTop('downloads');

echo '<h1>'.$translation['tbDumpDownload']."</h1>\n";
echo "<h3><span class=\"glyphicon glyphicon-th-list\" aria-hidden=\"true\"></span> $selectedCategory</h3>\n";
echo '<table class="table table-striped">';
echo '<thead><tr><th>'.$tableTitle."</th></tr></thead>\n";

if(empty($products)) {
    echo '<tr><td>'.$noItems.'</td></tr>';
} else {
    foreach ($products as $key => &$curr) {
        echo '<tr><td><a href="./langs.php?id='.$key.'&'.$langParam.'">'.$curr .' ['.$translation['idName'].': '.$key."]</a></td></tr>\n";
    }
}

echo '</table>';
styleBottom();
?>
