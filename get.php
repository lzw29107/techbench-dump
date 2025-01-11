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

require_once 'shared/lang.php';
require_once 'shared/utils.php';
require_once 'shared/style.php';

$apiVersion = 2;

if($_SERVER['REQUEST_METHOD'] == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/x-www-form-urlencoded') {
    if(isset($_POST['prodId'])) {
        $prodId = $_POST['prodId'];
        if(is_file('dump.json')) {
            $dump = json_decode(file_get_contents('dump.json'), true);
            $prodItem = $dump['ProdInfo'][$prodId];
            if(!isset($prodItem)) {
                $prodInfo = getInfo($apiVersion, 'Prod', $prodId);
                if($prodInfo) {
                    $prodItem = parseProdInfo($apiVersion, $prodId, $prodInfo);
                } else {
                    echo json_encode(['Error' => '']);
                    exit();
                }
            }
            $withCookies = array_intersect(['WIP', 'Xbox'], $prodItem['Category']) !== [];
            $info = [
                'withCookies' => $withCookies,
                'select2' => []
            ];
            $skuName = isset($prodItem['Sku']) ? 'Sku' : 'Language';
            foreach($prodItem[$skuName] as $skuId => $sku) {
                $name = $sku['Name'];
                if($skuName == 'Language' && isset($s['langNames'][$name])) {
                    if($apiVersion == 1) {
                        $enName = end($enLangNames[$name]);
                    }
                    $name = $s['langNames'][$name];
                }
                $info['select2'][] = [
                    'id' => $skuId,
                    'text' => $name
                ];
                if(isset($enName)) {
                    $index = count($info['select2']) - 1;
                    $info['select2'][$index]['enName'] = $enName;
                }
            }
            echo json_encode($info, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
        exit();
    }
}

$prodId = $_GET['id'] ?? '0';
$withCookies = false;

$config = getConfig();

if(is_file('dump.json')) {
    $dump = json_decode(file_get_contents('dump.json'), true);
    $prodItem = $dump['ProdInfo'][$prodId];
    if($config['autoupd'] && $config['php'] && time() - $dump['TechInfo']['LastCheckUpdateTime'] >= 3600) execBackground($config['php'], 'dump.php update --quiet');
}

$select = true;

$productName = $s['unknownName'];

if(isset($prodItem)) {
    $productName = $prodItem['Name'];
    if(in_array('LP', $prodItem['Category'])) $s['langCodeMs'] = 'en-US';
    if(array_intersect(['WIP', 'Xbox'], $prodItem['Category']) !== []) {
        $withCookies = true;
    }
}

switch ($apiVersion) {
    case 1:
        $baseUrl = "https://www.microsoft.com/{$s['langCodeMs']}/api/controls/contentinclude/html?pageId=%s&host=www.microsoft.com&segments=software-download,windows11&query=&action=%s&sessionid=%s&sdVersion=2";
        $prodUrlId = 'cd06bda8-ff9c-4a6e-912a-b92a21f42526';
        $skuUrlId = 'cfa9e580-a81e-4a4b-a846-7b21bf4e2e5b';
        $langsUrl = sprintf($baseUrl, $prodUrlId, 'getskuinformationbyProductedition', "&ProductEditionId=$prodId");
        $downUrl = sprintf($baseUrl, $skuUrlId, 'GetProductDownloadLinksBySku', '');
        break;
    case 2:
        $baseUrl = "https://www.microsoft.com/software-download-connector/api/%s?profile=606624d44113&ProductEditionId=%s&SKU=%s&friendlyFileName=undefined&Locale={$s['langCodeMs']}&sessionID=";
        $langsUrl = sprintf($baseUrl, 'getskuinformationbyProductedition', $prodId, 'undefined');
        $downUrl = sprintf($baseUrl, 'GetProductDownloadLinksBySku', 'undefined', 'undefined');
        break;
    default:
        return false;
}

$notice = $withCookies ? '<div class="alert alert-danger mt-4 pb-1">
<h4><i class="bi bi-exclamation-triangle"></i> '.$s['warning'].'</h4>
<p>'.sprintf($s['insiderNotice'], '<a class="link-underline link-underline-opacity-0" href="https://www.microsoft.com/en-us/software-download/windowsinsiderpreviewiso').'</p>
</div>' : '';

styleTop('downloads');

echo <<<HTML
<input type="hidden" id="langCodeMs" value="{$s['langCodeMs']}">
<div class="mt-5 mb-4">
    <h1 class="fs-3">{$s['tbDumpDownload']}</h1>
</div>

$notice

<h3 class="fs-4">
    <i class="bi bi-list-ul"></i>
    $productName [{$s['idName']}: $prodId]
</h3>
<div class="row">
    <div id="msContent" class="col-md-6 mt-3">
        <div class="row-padded row-fluid">
            <div>
                <label for="product-languages">
                    <h2 style="font-size: 1.3rem; margin-top: 1rem;">{$s['selectLang']}</h2>
                </label>
            </div>
            <div>
                {$s['selectLangDesc']}
            </div>
        </div>
        <div class="input-group">
            <div id="pk-language-validation" style="width: 100%;">
                <div>
                    <div>
                        <span class="mt-3 mb-2" style="display: block;">{$s['selectNotice']}</span>
                    </div>
                </div>
                <div style="width: 100%;">
                    <div>
                        <select id="product-languages" class="form-select" style="width: 100%;">
                            <option value="" selected="selected">{$s['selectPlaceholder']}</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div style="width: 100%;">
            <div style="width: 100%;">
                <a id="submit-sku" class="btn d-grid btn-primary my-2" style="width: 100%;" disabled>{$s['confirm']}</a>
            </div>
        </div>
        <noscript>
            <h4>
            {$s['warning']}
            </h4>
            <p>
            {$s['jsRequired']}
            </p>
        </noscript>
    </div>
    <div id="msContent2" class="col-md-6 mt-2" style="display: none;">
        <div class="row-padded row-fluid">
            <div>
                <label for="download-links">
                    <h2 style="font-size: 1.3rem; margin-top: 1rem;">{$s['downLinks']}</h2>
                </label>
            </div>
            <div>
                <div id="down" class="col" style="display: none;">
                    <p class="mb-2">{$s['download']}</p>
                    <a class="btn btn-primary mb-2">
                        <i class="bi bi-download"></i>
                        {$s['downloadName']}
                    </a>
                </div>
                <div id="downx86" class="col" style="display: none;">
                    <p class="mb-2">{$s['download']}</p>
                    <a class="btn btn-primary mb-2">
                        <i class="bi bi-download"></i>
                        {$s['archx86']}
                    </a>
                </div>
                <div id="downx64" class="col" style="display: none;">
                    <p class="mb-2">{$s['download']}</p>
                    <a class="btn btn-primary mb-2">
                        <i class="bi bi-download"></i>
                        {$s['archx64']}
                    </a>
                </div>
                <div id="downarm64" class="col" style="display: none;">
                    <p class="mb-2">{$s['download']}</p>
                    <a class="btn btn-primary mb-2">
                        <i class="bi bi-download"></i>
                        ARM64
                    </a>
                </div>
                <p class="mb-1">{$s['linksNotice']}</p>
                <p id="expireTime" class="mb-0">{$s['linksExpire']}</p>
            </div>
        </div>
    </div>
</div>
<script src="js/download.js" defer="defer"></script>
HTML;
?>
<?php styleBottom(); ?>
