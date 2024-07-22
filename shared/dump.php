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

function writeProduct($productId, $info) {
    global $dump, $enLangName;

    $skus = [];
    $langCount = 0;
    $skuCount = 0;

    foreach($info['Skus'] as $skuId => $sku) {
        if(in_array($sku, $enLangName)) {
            $sku = array_search($sku, $enLangName);
            $langCount++;
        } else {
            $skuCount++;
        }
        $skus[$skuId]['Name'] = $sku;
    }

    $skuName = $langCount > $skuCount ? 'Language' : 'Sku';

    $dump['ProdInfo'][$productId] = [
        'Name' => $info['ProductName'],
        'Category' => $info['Category'],
        'Status' => $info['Status'],
        'Arch' => $info['Arch'],
        $skuName => $skus
    ];
}

function dump($minProdId, $maxProdId) {
    global $dump, $sessionId, $lock;
    $ignoreList = array_merge(
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

    $allProd = array_diff(range($minProdId, $maxProdId), $ignoreList);
    $lock['status'] = 'update';
    $errorCount = 0;
    $lock['total'] = count($allProd);
    $lock['current'] = 0;
    foreach($allProd as $productId) {
        $lock['current']++;
        $lock['progress'] = number_format(($lock['current'] / $lock['total']) * 100, 2);
        if($productId > $maxProdId && $errorCount > 10) break;
        if($lock['current'] % 15 == 1) {
            if($lock['current'] > 15 && $errorCount < 15) file_put_contents('dump.json', json_encode($dump, JSON_PRETTY_PRINT));
            $sessionId = genSessionId();
        }

        $htmlText = getInfo('Prod', $productId);
        if(!$htmlText) $htmlText = getInfo('Prod', $productId);
        $html = new DOMDocument();
        $html->loadHTML($htmlText);
        if($html->getElementById('errorModalMessage')) {
            //$errorMsg = $html->getElementById('errorModalMessage')->textContent;
            $errorCount++;
        } else {
            writeProduct($productId, parserProdInfo($productId, $html));
            $errorCount = 0;
        }
        $lock['time'] = time();
        file_put_contents('dump.json.lock', json_encode($lock));
    }
}

function recheck($lastProdId, $lastBlocked = null, $type = 'Basic') {
    global $dump, $sessionId, $lock, $enLangName;
    $lock['status'] = 'recheck';
    $lock['total'] = 0;
    $lock['current'] = 0;
    $checkList = [];

    foreach($dump['ProdInfo'] as $productId => $product) {
        if($product['Status'] == 'Unavailable') continue;
        if($lastBlocked && $productId < $lastBlocked) continue;
        if($type != 'Basic' && !in_array($type, $product['Category'])) continue;
        $checkList[] = $productId;
        $lock['total']++;
    }

    foreach($checkList as $productId) {
        $lock['current']++;
        $lock['progress'] = number_format(($lock['current'] / $lock['total']) * 100, 2);

        if($lock['current'] % 15 == 1) $sessionId = genSessionId();

        $htmlText = getInfo('Prod', $productId);
        if(!$htmlText) $htmlText = getInfo('Prod', $productId);
        $html = new DOMDocument();
        $html->loadHTML($htmlText);
        $info = parserProdInfo($productId, $html);
        if($info['Status'] != 'Unavailable') {
            writeProduct($productId, $info);
        } else if($info['Arch'] == 'Unknown') {
            return $productId;
        } else if(!in_array('WIP', $dump['ProdInfo'][$productId]['Category']) && !in_array('Xbox', $dump['ProdInfo'][$productId]['Category'])) {
            $dump['ProdInfo'][$productId]['Status'] = $info['Status'];
        }

        $lock['time'] = time();
        file_put_contents('dump.json.lock', json_encode($lock));
    }
}
?>
