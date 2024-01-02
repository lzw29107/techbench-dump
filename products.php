<?php
/*
TechBench dump
Copyright (C) 2024 TechBench dump website authors and contributors

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

   http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
*/

$prodName = isset($_GET['prod']) ? $_GET['prod'] : 'all';
$search = isset($_GET['search']) ? $_GET['search'] : null;

require_once 'shared/lang.php';
require_once 'shared/style.php';

$out = @file_get_contents('dump.json');
if(empty($out)) {
    $out = array('genTime' => null, 'productNumber' => '?', 'products' => array());
} else {
    $out = json_decode($out, true);
}

if(isset($s[$prodName])) $selectedCategory = $s[$prodName];
switch ($prodName) {
    case 'win7':
        $products = preg_grep('/Windows.7/',$out['products']);
        break;
    case 'win81':
        $products = preg_grep('/Windows.8\.1/',$out['products']);
        break;
    case 'win10':
        $products = preg_grep('/Windows.10/',$out['products']);
        break;
    case 'win10th1':
        $products = preg_grep('/Windows.10.*?Threshold.1/',$out['products']);
        break;
    case 'win10th2':
        $products = preg_grep('/Windows.10.*?Threshold.2/',$out['products']);
        break;
    case 'win10rs1':
        $products = preg_grep('/Windows.10.*?Redstone.1|Windows.*?Build 14393/',$out['products']);
        break;
    case 'win10rs2':
        $products = preg_grep('/Windows.10.*?Redstone.2|Windows.*?Build 15063/',$out['products']);
        break;
    case 'win10rs3':
        $products = preg_grep('/Windows.10.*?1709|Windows.*?Build 16299/',$out['products']);
        break;
    case 'win10rs4':
        $products = preg_grep('/Windows.10.*?1803|Windows.*?Build 17134/',$out['products']);
        break;
    case 'win10rs5':
        $products = preg_grep('/Windows.10.*?1809|Windows.*?Build 17763/',$out['products']);
        break;
    case 'win10rs6':
        $products = preg_grep('/Windows.10.*?1903|Windows.*?Build 18362/',$out['products']);
        break;
    case 'win10_19h2':
        $products = preg_grep('/Windows.10.*?1909|Windows.*?Build 18363/',$out['products']);
        break;
    case 'win10vb':
        $products = preg_grep('/Windows.10.*?2004|Windows.*?Build 19041/',$out['products']);
        break;
    case 'win10_20h2':
        $products = preg_grep('/Windows.10.*?20H2|Windows.*?Build 19042/',$out['products']);
        break;
    case 'win10_21h1':
        $products = preg_grep('/Windows.10.*?21H1|Windows.*?Build 19043/',$out['products']);
        break;
    case 'win10_21h2':
        $products = preg_grep('/Windows.10.*?21H2|Windows.*?Build 19044/',$out['products']);
        break;
    case 'win10_22h2':
        $products = preg_grep('/Windows.10.*?22H2|Windows.*?Build 19045/',$out['products']);
        break;
    case 'win10ip':
        $products = preg_grep('/Windows.*10.*?Insider.?Preview/',$out['products']);
        break;
    case 'win11':
        $products = preg_grep('/Windows.11/',$out['products']);
        break;
    case 'win11co':
        $products = preg_grep('/Windows.11.*?21H2|Windows.*?Build 22000/',$out['products']);
        break;
    case 'win11ni':
        $products = preg_grep('/Windows.11.*?22H2|Windows.*?Build 22621/',$out['products']);
        break;
    case 'win11_23h2':
        $products = preg_grep('/Windows.11.*?23H2|Windows.*?Build 22631/',$out['products']);
        break;
    case 'win11ip':
        $products = preg_grep('/Windows.*11.*?Insider.?Preview/',$out['products']);
        break;
    case 'winsrvip':
        $products = preg_grep('/Windows.*Server.*/',$out['products']);
        break;
    case 'office2007':
        $products = preg_grep('/ 2007/',$out['products']);
        $selectedCategory = $s['office2007'];
        break;
    case 'office2010':
        $products = preg_grep('/ 2010/',$out['products']);
        $selectedCategory = $s['office2010'];
        break;
    case 'office2011':
        $products = preg_grep('/ 2011/',$out['products']);
        $selectedCategory = $s['office2011'];
        break;
    case 'all':
        $selectedCategory = $s['allProd'];
        $products = $out['products'];
        break;
    case 'other':
        $selectedCategory = $s['otherProd'];
        $products = $out['products'];
        foreach($products as $key => &$curr){
            $check = preg_match('/Windows.7|Windows.8\.1|Windows.10|Windows.11| 2007| 2010| 2011/', $curr);
            if($check) {
              unset($products[$key]);
            }
        }
        break;
    default:
        $selectedCategory = $s['allProd'];
        $products = $out['products'];
        break;
}

if(!empty($search)) {
    $searchSafe = preg_quote($search, '/');
    if (!preg_match('/^".*"$/', $searchSafe)) {
        $searchSafe = str_replace(' ', '.*', $searchSafe);
    } else {
        $searchSafe = preg_replace('/^"|"$/', '', $searchSafe);
    }

    $products = preg_grep('/.*'.$searchSafe.'.*/i',$products);

    $tableTitle = $s['searchResults'].': '.$search;
    $noItems = $s['searchNoResults'];
} else {
    $tableTitle = $s['prodSelect'];
    $noItems = $s['noProducts'];
}

styleTop('downloads');

echo '<h1>'.$s['tbDumpDownload']."</h1>\n";
echo "<h3><span class=\"glyphicon glyphicon-th-list\" aria-hidden=\"true\"></span> $selectedCategory</h3>\n";
echo '<table class="table table-striped">';
echo '<thead><tr><th>'.$tableTitle."</th></tr></thead>\n";

if(empty($products)) {
    echo '<tr><td>'.$noItems.'</td></tr>';
} else {
    foreach ($products as $key => &$curr) {
        echo '<tr><td><a href="./get.php?id='.$key.'">'.$curr .' ['.$s['idName'].': '.$key."]</a></td></tr>\n";
    }
}

echo '</table>';
styleBottom();
?>
