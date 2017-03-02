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

// Get download by SKU ID
function getDownload($skuId = '6PC-00020') {
    $req = curl_init("http://www.microsoft.com/en-us/api/controls/contentinclude/html?pageId=cfa9e580-a81e-4a4b-a846-7b21bf4e2e5b&host=www.microsoft.com&segments=software-download,windows10ISO&query=&action=GetProductDownloadLinksBySku&skuId=" . urlencode($skuId));

    curl_setopt($req, CURLOPT_HEADER, 0);
    curl_setopt($req, CURLOPT_REFERER, "https://www.microsoft.com/en-us/software-download/windows10ISO");
    curl_setopt($req, CURLOPT_RETURNTRANSFER, true); 

    $expire = time() + 86400;
    $out = curl_exec($req);
    curl_close($req);

    if (strpos($out, 'We encountered a problem processing your request') !== false) {
        return array('error' => 'process_error');
    }

    $out = html_entity_decode($out);
    $out = preg_replace('/\n|\r|\t/', '', $out);
    $out = preg_replace('/<div.*?>|<span.*?>|<input.*?>/', '', $out);

    preg_match("/<\/div><\/div><\/div><h2>.*<\/h2>/", $out, $osName);
    $osName = preg_replace('/<\/div><\/div><\/div><h2>|<\/h2>/', '', $osName[0]);
    preg_match_all('/<a class="button.*?href="http.*?">.*?<\/span>/', $out, $isoName);
    if (empty($isoName)) {
        return array('error' => 'process_error');
    }
    $isoName = preg_replace('/<a class="button.*href="|<\/span>/', '', $isoName[0]);

    $downloadArray = array();

    foreach ($isoName as &$curr) {
        $iso = preg_replace('/http.*com\/pr\/|\?t=.*/', '', $curr);
        $arch = preg_replace('/.*">/', '', $curr);
        $arch = strtolower($arch);
        $arch = str_replace('isox64', 'x64', $arch);
        $arch = str_replace('isox86', 'x86', $arch);
        $arch = str_replace('unknown', 'any', $arch);
        $url = preg_replace('/">.*/', '', $curr);
        $downloadArray = array_merge($downloadArray, array(array(
            'fileName' => "$iso",
            'url' => "$url",
            'architecture' => "$arch"
        )));
    }

    unset($curr, $iso, $arch, $url);

    if (empty($osName)) {
        $osName = $downloadArray[0]['fileName'].' [?]';
    }

    return array(
        "osName" => $osName,
        "downloadLinks" => $downloadArray,
        "expiration" => $expire
    );
}

// Get download by File Name
function getDownloadByName($fileName = 'Win7_Pro_SP1_English_x64.iso') {
    $req = curl_init("http://www.microsoft.com/en-us/api/controls/contentinclude/html?pageId=160bb813-f54e-4e9f-bffc-38c6eb56e061&host=www.microsoft.com&segments=software-download%2cwindows10&query=&action=GetProductDownloadLinkForFriendlyFileName&friendlyFileName=" . urlencode($fileName));

    curl_setopt($req, CURLOPT_HEADER, 0);
    curl_setopt($req, CURLOPT_REFERER, "https://www.microsoft.com/en-us/software-download/windows10ISO");
    curl_setopt($req, CURLOPT_RETURNTRANSFER, true); 

    $expire = time() + 86400;
    $out = curl_exec($req);
    curl_close($req);

    if (strpos($out, 'We encountered a problem processing your request') !== false) {
        return array('error' => 'process_error');
        die();
    }

    $out = preg_replace('/\n|\r|\t/', '', $out);

    $out = preg_replace('/.*http/', 'http', $out);
    $arch = preg_replace('/.*downloadType:"|"}.*/', '', $out);
    $arch = preg_replace('/downloadType:"|"}/', '', $arch);
    $arch = strtolower($arch);
    $arch = str_replace('isox64', 'x64', $arch);
    $arch = str_replace('isox86', 'x86', $arch);
    $arch = str_replace('unknown', 'any', $arch);
    $out = preg_replace('/",downloadType:".*<\/div>/', '', $out);
    $name = preg_replace('/http.*com\/pr\/|\?t=.*/', '', $out);

    return array(
        "fileName" => "$name",
        "downloadLink" => "$out",
        "architecture" => "$arch",
        "expiration" => "$expire"
    );
}

// Get Language list
function getLangOut($prodId = "4", $lang = "en-us") {
    $req = curl_init("http://www.microsoft.com/". urlencode($lang) ."/api/controls/contentinclude/html?pageId=a8f8f489-4c7f-463a-9ca6-5cff94d8d041&host=www.microsoft.com&segments=software-download,windows10ISO&query=&action=getskuinformationbyproductedition&productEditionId=" . urlencode($prodId));

    curl_setopt($req, CURLOPT_HEADER, 0);
    curl_setopt($req, CURLOPT_REFERER, "https://www.microsoft.com/en-us/software-download/windows10ISO");
    curl_setopt($req, CURLOPT_RETURNTRANSFER, true); 

    $out = curl_exec($req);
    curl_close($req);

    $out = html_entity_decode($out);
    $out = preg_replace('/\n|\r|\t/', '', $out);
    $out = preg_replace('/<div.*?>|<span.*?>/', '', $out);
    return $out;
}

function getLangList($prodId = "4", $lang = "en-us") {
    $out = getLangOut($prodId, $lang);
    preg_match_all('/<option value="{.*?<\/option>/', $out, $langs);
    $langs = $langs[0];

    if (empty($langs)) {
        if ($lang == "en-us") {
            return array('error' => 'no_product');
        }
        $lang = "en-us";
        $out = getLangOut($prodId, $lang);
        preg_match_all('/<option value="{.*?<\/option>/', $out, $langs);
        $langs = $langs[0];
        if (empty($langs)) {
            return array('error' => 'no_product');
        }
    }

    $langs = preg_replace('/}">/', ',"langLocal":"', $langs);
    $langs = preg_replace('/<option value="/', '', $langs);
    $langs = preg_replace('/<\/option>/', '"}', $langs);

    $langArray = array();

    foreach ($langs as &$curr) {
        $tmp = json_decode($curr, true);
        $langArray = array_merge($langArray, array($tmp));
    }
    unset($curr, $tmp, $langs, $out);

    return array('langs' => $langArray);
}
?>
