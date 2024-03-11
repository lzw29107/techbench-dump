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

require_once 'contrib/langconf.php';

function checkStrNum($str = null) {
    if(!is_numeric($str) || is_double($str) || $str > PHP_INT_MAX || $str < 0) return 0;
    return 1;
}

function WriteProduct($ProductID, $Info) {
    global $dump, $enLangName;

    $Skus = [];
    foreach($Info['Skus'] as $SkuID => $Sku) {
        if(in_array($Sku, $enLangName)) {
            $Sku = array_search($Sku, $enLangName);
            $SkuName = 'Language';
        } else {
            $SkuName = 'Sku';
        }
        $Skus[$SkuID]['Name'] = $Sku;
    }

    $dump['ProdInfo'][$ProductID] = [
        'Name' => $Info['ProductName'],
        'Category' => $Info['Category'],
        'Validity' => $Info['Validity'],
        'Arch' => $Info['Arch'],
        $SkuName => $Skus
    ];
}

function dump($minProdID, $maxProdID) {
    global $dump, $SessionID, $lock;
    $Ignore = array_merge(
        [
            53,
            54,
            84,
            421,
            422,
            552,
            559,
            561,
            563,
            564,
            569,
            577,
            585,
            587,
            588,
            657,
            658,
            764,
            765,
            793,
            848,
            849,
            937,
            938,
            1033,
            1034,
            1134,
            1135,
            1213,
            1371,
            1372,
            1469,
            1470,
            1511,
            1565,
            1687,
            1868,
            1908,
            1909,
            2037,
            2039
        ],
        range(1, 27, 2),
        range(29, 47),
        range(49, 51),
        range(56, 60),
        range(63, 67),
        range(72, 74),
        range(129, 172),
        range(571, 574),
        range(680, 686),
        range(708, 731),
        range(748, 762),
        range(767, 791),
        range(809, 820),
        range(839, 846),
        range(861, 873),
        range(896, 903),
        range(940, 947),
        range(981, 989),
        range(1009, 1011),
        range(1025, 1030),
        range(1040, 1043),
        range(1062, 1069),
        range(1486, 1508),
        range(1527, 1529),
        range(2651, 2658)
    );

    $allProd = array_diff(range($minProdID, $maxProdID), $Ignore);
    $lock['status'] = 'update';
    $errorCount = 0;
    $lock['total'] = count($allProd);
    $lock['current'] = 0;
    foreach($allProd as $ProductID) {
        $lock['current']++;
        $lock['progress'] = number_format(($lock['current'] / $lock['total']) * 100, 2);
        if($ProductID > $maxProdID && $errorCount > 10) break;
        if($lock['current'] % 15 == 1) {
            $SessionID = SessionIDInit();
        }
        $html = new DOMDocument();
        $html->loadHTML(getInfo('Prod', $ProductID));
        if($html->getElementById('errorModalMessage')) {
            //$errorMsg = $html->getElementById('errorModalMessage')->textContent;
            $errorCount++;
        } else {
            WriteProduct($ProductID, parserProdInfo($ProductID, $html));
            $errorCount = 0;
        }
        $lock['time'] = time();
        file_put_contents('dump.json.lock', json_encode($lock));
    }
}

function recheck($lastProdID, $lastBlocked = null) {
    global $dump, $SessionID, $lock, $enLangName;
    $lock['status'] = 'recheck';
    $Time = time();
    $lock['total'] = 0;
    $lock['current'] = 0;
    $Valid = [];

    foreach($dump['ProdInfo'] as $Product) {
        if($Product['Validity'] != 'Valid') continue;
        if($lastBlocked && key($prod) < $lastBlocked) continue;
        $Valid[] = key($prod);
        $lock['total']++;
    }
    
    foreach($Valid as $ProductID) {
        $lock['current']++;
        $lock['progress'] = number_format(($lock['current'] / $lock['total']) * 100, 2);
        if($dump[$ProductID]['Validity'] != 'Valid') continue;
        if($lock['current'] % 15 == 1) $SessionID = SessionIDInit();

        $html = new DOMDocument();
        $html->loadHTML(getInfo('Prod', $ProductID));
        $Info = parserProdInfo($ProductID, $html);
        if($Info['Validity'] != 'Invalid') {
            WriteProduct($ProductID, $Info);
        } else if($Info['Arch'] == 'Unknown') return $ProductID;
        else $dump[$ProductID]['Validity'] = $Info['Validity'];

        $lock['time'] = time();
        file_put_contents('dump.json.lock', json_encode($lock));
    }
}
?>
