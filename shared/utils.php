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

function getBaseUrl() {
    $baseUrl = '';
    if(isset($_SERVER['HTTPS'])) {
        $baseUrl .= 'https://';
    } else {
        $baseUrl .= 'http://';
    }

    $baseUrl .=  $_SERVER['HTTP_HOST'];
    return $baseUrl;
}

function getUrlWithoutParam($param = null) {
    $baseUrl = getBaseUrl();

    $params = '';
    $separator = '?';
    foreach($_GET as $key => $val) {
        if($key == $param) continue;
        $params .= $separator.$key.'='.urlencode($val);
        $separator = '&';
    }
    $params .= $separator;

    $shelf = explode('?', $_SERVER['REQUEST_URI']);
    $url = $baseUrl.$shelf[0].$params;

    return $url;
}

function genSessionId() {
    $time = time();
    $sessionId = preg_replace_callback('/[xy]/', function ($matches) use ($time) {
        $random = (($time + rand(0, 15)) % 16);
        if ($matches[0] === 'y') $random = ($random & 3) | 8;
        $time = floor($time / 16);
        $random = dechex($random);
        return $random;
    }, 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx');

    // Send a request to Microsoft to initialize the session
    $req = curl_init('https://vlscppe.microsoft.com/fp/tags.js?org_id=y6jn8c31&session_id=' . urlencode($sessionId));
    curl_setopt($req, CURLOPT_HEADER, 0);
    curl_setopt($req, CURLOPT_REFERER, 'https://www.microsoft.com/en-us/software-download/windows11');
    curl_setopt($req, CURLOPT_RETURNTRANSFER, true); 

    $out = curl_exec($req);
    curl_close($req);

    return $sessionId;
}

function getConfig($key = null) {
    $config = json_decode(@file_get_contents('config.json'), true);
    if(!$config || $config['version'] != '1.0' || !array_key_exists('php', $config) || !array_key_exists('autoupd', $config)) {
        setConfig('init');
        return getConfig();
    }
    if($key) return $config[$key];
    return $config;
}

function setConfig($type, $key = null, $value = null) {
    if($type == 'set') {
        $config = json_decode(@file_get_contents('config.json'), true);
        if(!$config || $config['version'] != '1.0') {
            setConfig('init');
            setConfig('set', $key, $value);
        }
        if($key && $value) $config[$key] = $value;
        file_put_contents('config.json', json_encode($config));
    } else if($type == 'init') {
        $config = [];
        $config['version'] = '1.0';
        $config['php'] = findPHP();
        $config['autoupd'] = true;
        file_put_contents('config.json', json_encode($config));
    }
}

function findPHP() {
    if(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $delimiter = ';';
        $ext = '.exe';
        $separator = '\\';
        if(strpos(shell_exec('php -v'), 'PHP') !== false) $php = 'php';
    } else {
        $delimiter = ':';
        $ext = '';
        $separator = '/';
        if(shell_exec('command -v php')) $php = 'php';
    }

    if(!isset($php)) {
        $directory = dirname(php_ini_loaded_file());
        if(is_file($directory . $separator . 'php' . $ext)) {
            $php = $directory . $separator . 'php' . $ext;
        } else $php = false;
    }
    return $php;
}

function execBackground($php, $command) {
    if(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        popen("start /B $php $command >nul 2>nul", 'r');
    } else {
        shell_exec("$php $command >/dev/null 2>&1 &");
    }
}

function identProduct($productId) {
    $appendVer = "";

    if ($productId >= 75 && $productId <= 82) $appendVer = " (Threshold 1)";
    else if ($productId >= 99 && $productId <= 106) $appendVer = " (Threshold 2)";
    else if ($productId >= 109 && $productId <= 116) $appendVer = " (Threshold 2, February 2016 Update)";
    else if ($productId >= 178 && $productId <= 185) $appendVer = " (Threshold 2, April 2016 Update)";
    else if ($productId >= 242 && $productId <= 247) $appendVer = " (Redstone 1)";
    else if ($productId == 489) $appendVer = " (Redstone 3)";
    else if ($productId == 2069 || $productId == 2070) $appendVer = " (21H2 Original release)";

    return $appendVer;
}

function checkCategory($productName) {
    global $firstOption, $skus;
    $category = [];
    $build = substr(strstr($productName, ' - '), 3);
    if(strpos($productName, 'Admin Center') !== false || strpos($productName, 'Honolulu') !== false) {
        $category[] = 'AdminCenter';
        if(strpos($productName, 'Preview') !== false) $category[] = 'WIP';
    } else if($build) {
        if(strpos($build, 'Build ') !== false) {
            $actualBuild = intval(substr(strstr($build, 'Build '), 6));
            if(strpos($productName, 'IoT Core') !== false) {
                $category[] = 'IoTCore';
                if(strpos($actualBuild, '17763') !== false) unset($actualBuild);
            }
        } else if(is_numeric($build)) $actualBuild = $build;
        else if(strpos($productName, 'Media Feature') !== false) {
            $category[] = 'MF';
        }
    }
    if(isset($actualBuild)) {
        if($actualBuild <= 10586) $category[] = 'Win10';
        else if($actualBuild > 10586) {
            $category[] = 'WIP';
            if($actualBuild < 22000) {
                $category[] = 'Win10';
                if($actualBuild == 14393) $category[] = 'rs1';
                else if($actualBuild == 15063) $category[] = 'rs2';
                else if($actualBuild == 16299) $category[] = 'rs3';
                else if($actualBuild == 17134) $category[] = 'rs4';
                else if($actualBuild == 17763) $category[] = 'rs5';
                else if($actualBuild == 18362) $category[] = '19H1';
                else if($actualBuild == 18363) $category[] = '19H2';
                else if($actualBuild == 19041) $category[] = 'vb';
                else if($actualBuild == 19042) $category[] = '20H2';
                else if($actualBuild == 19043) $category[] = '21H1';
                else if($actualBuild == 19044) $category[] = '21H2';
                else if($actualBuild == 19045) $category[] = '22H2';
            } else {
                $category[] = 'Win11';
                if($actualBuild == 22000) $category[] = 'co';
                else if($actualBuild == 22621) $category[] = 'ni';
                else if($actualBuild == 22631) $category[] = '23H2';
                else if($actualBuild == 26100) $category[] = 'ge';
            }
        }
    } else if(strpos($productName, 'Windows 7') !== false) $category[] = 'Win7';
        else if(strpos($productName, 'Windows 8.1') !== false) $category[] = 'Win81';
        else if(strpos($productName, 'Windows 10') !== false || strpos($productName, 'Media Feature') !== false) {
            $category[] = 'Win10';
            if(strpos($productName, 'Threshold 1') !== false) $category[] = 'th1';
            else if(strpos($productName, 'Threshold 2') !== false) $category[] = 'th2';
            else if(strpos($productName, '1607') !== false || strpos($productName, 'Redstone 1') !== false) $category[] = 'rs1';
            else if(strpos($productName, '1703') !== false) $category[] = 'rs2';
            else if(strpos($productName, '1709') !== false || strpos($productName, 'Redstone 3') !== false) $category[] = 'rs3';
            else if(strpos($productName, '1803') !== false || strpos($productName, 'RS4') !== false) $category[] = 'rs4';
            else if(strpos($productName, '1809') !== false) $category[] = 'rs5';
            else if(strpos($productName, '1903') !== false) $category[] = '19H1';
            else if(strpos($productName, '1909') !== false) $category[] = '19H2';
            else if(strpos($productName, '2004') !== false) $category[] = 'vb';
            else if(strpos($productName, '20H2') !== false) $category[] = '20H2';
            else if(strpos($productName, '21H1') !== false) $category[] = '21H1';
            else if(strpos($productName, '21H2') !== false) $category[] = '21H2';
            else if(strpos($productName, '22H2') !== false) $category[] = '22H2';
    } else if(strpos($productName, 'Windows 11') !== false) {
        $category[] = 'Win11';
        if(strpos($productName, '21H2') !== false || !strpos($productName, 'H2') !== false) $category[] = 'co';
        else if(strpos($productName, '22H2') !== false) $category[] = 'ni';
        else if(strpos($productName, '23H2') !== false) $category[] = '23H2';
        else if(strpos($productName, '24H2') !== false) $category[] = 'ge';
    } else if(strpos($productName, ' 2007') !== false) {
        $category[] = 'Office';
        $category[] = '2007';
    } else if(strpos($productName, ' 2010') !== false) {
        $category[] = 'Office';
        $category[] = '2010';
    } else if(strpos($productName, ' 2011') !== false) {
        $category[] = 'Office';
        $category[] = '2011';
    }

    if(strpos($productName, 'ARM64') !== false) $category[] = 'ARM64';
    if(strpos($productName, 'Language Pack') !== false || strpos($productName, 'LIP Pack') !== false || strpos($productName, 'Server Language and Optional Features') !== false || stripos($productName, 'FOD') !== false) $category[] = 'LOF';
    else if(strpos($productName, 'SDK') !== false) $category[] = 'SDK';
    else if(strpos($productName, ' WDK') !== false || strpos($productName, 'WDK') === 0) $category[] = 'WDK';
    else if(strpos($productName, 'EWDK') !== false) $category[] = 'EWDK';
    else if(strpos($productName, 'HLK') !== false) $category[] = 'HLK';
    else if(strpos($productName, 'MTBF') !== false) $category[] = 'MTBF';
    else if(strpos($productName, 'Preinstallation Environment') !== false || strpos($productName, 'ADK') !== false) $category[] = 'ADK';
    else if(strpos($productName, 'Desktop App Converter') !== false || strpos($productName, 'DAC') !== false) $category[] = 'DAC';
    else if(strpos($productName, 'RSAT') !== false) $category[] = 'RSAT';
    else if(strpos($productName, 'Symbols') !== false) $category[] = 'Symbols';
    else if(strpos($productName, 'Mobile Emulator') !== false) $category[] = 'MobileEmu';
    else if(strpos($productName, 'Inbox Apps') !== false) $category[] = 'InboxApps';

    if(strpos($productName, 'Server') !== false) {
        $category[] = 'WinSrv';
    }
    if(strpos($productName, 'Xbox') !== false || strpos($productName, 'GDK') !== false || strpos($productName, 'Submission') !== false || strpos($productName, ' -  ( only) ') !== false || strpos($productName, 'Recovery') !== false || strpos($productName, 'Xfest') !== false || strpos($productName, 'GSDK') !== false) {
        $category[] = 'Xbox';
    }
    if(count($category) == 0) $category[] = 'Other';
    return $category;
}

function checkInfo_offline($productId, $category) {
    $knownAvailable = array_merge([1145, 1149, 1524, 2198, 2217, 2220, 2221, 2297, 2298, 2299, 2577, 2580, 2581, 2582, 2585, 2588, 2589, 2591], range(2603, 2735));
    $info = [];
    $info['Status'] = 'Unknown';
    $info['Arch'] = 'Unknown';
    if(in_array('Xbox', $category)) $info['Arch'] = ['x64'];
        else if(in_array('Office', $category)) {
        $info['Status'] = 'Unavailable';
        $info['Arch'] = ['x86', 'x64'];
        if(in_array('2011', $category)) $info['Arch'] = ['x64'];
    } else if(in_array('WIP', $category)) {
        if(!in_array($productId, $knownAvailable) && $productId < 2736) {
            $info['Status'] = 'Unavailable';
            return $info;
        }
    }
    return $info;
}

function checkInfo($productId, $skus, $category) {
    $knownAvailable = [48, 52, 55, 61, 62, 489, 642, 1057, 1217, 1460, 2378, 2616, 2617, 2618, 2860, 2861, 2935, 2936];
    $blocked = 'We are unable to complete your request at this time. Some users, entities and locations are banned from using this service. For this reason, leveraging anonymous or location hiding technologies when connecting to this service is not generally allowed. If you believe that you encountered this problem in error, please try again. If the problem persists you may contact  Microsoft Support – Contact Us  page for assistance. Refer to message code 715-123130 and';
    $info = [];
    $option = end($skus);
    $id = key($skus);

    if(!array_intersect(['WIP', 'Xbox', 'Office'], $category)) {
        if(in_array($productId, $knownAvailable) || $productId > 2956) {
            $downHTML = new DOMDocument();
            $downHTML->loadHTML(getInfo('Sku', $id, $option));
            if($downHTML->getElementById('errorModalMessage')) {
                $info['Status'] = 'Unavailable';
                $errorMsg = $downHTML->getElementById('errorModalMessage')->textContent;
                if($errorMsg == $blocked) {
                    $info['Status'] = 'Available';
                    echo "The Server IP is blocked by Microsoft.\n";
                }
                $info['Arch'] = ['Unknown'];
            } else {
                $info['Status'] = 'Available';
                $XPath = new DOMXPath($downHTML);
                $downBtns = $XPath->query('//span[contains(@class, "product-download-type")]');
                $arch = [];
                foreach($downBtns as $downBtn) {
                    if($downBtn->textContent == 'IsoX86') $arch[] = 'x86';
                    if($downBtn->textContent == 'IsoX64') $arch[] = 'x64';
                    if($downBtn->textContent == 'Unknown') $arch[] = 'neutral';
                    if(count($arch) == 0) $arch[] = 'Unknown';
                }
            }
        } else {
            $info['Status'] = 'Unavailable';
            $info['Arch'] = ['Unknown'];
        }
    } else {
        $info = checkInfo_offline($productId, $category);
    }
    if(array_intersect(['Win7', 'Win81', 'Win10', 'RSAT', 'Symbols'], $category)) $info['Arch'] = ['x86', 'x64'];
    if(array_intersect(['Win11', 'WinSrv', 'DAC'], $category)) {
        $info['Arch'] = ['x64'];
    }
    if(in_array('ARM64', $category)) $info['Arch'] = ['arm64'];

    if(array_intersect(['LOF', 'SDK', 'WDK', 'HLK', 'EWDK', 'InboxApps'], $category)) {
        $info['Arch'] = ['neutral'];
    } else if(array_intersect(['ADK', 'IoTCore'], $category)) {
        if(in_array('Win10', $category) || in_array('co', $category)) {
            $info['Arch'] = ['x86', 'x64', 'arm', 'arm64'];
        } else if(in_array('Win11', $category)) {
            $info['Arch'] = ['x64', 'arm64'];
        }
    } else if(in_array('MobileEmu', $category)) {
        $info['Arch'] = ['x86'];
    } else if(in_array('AdminCenter', $category)) {
        $info['Arch'] = ['x64'];
    }
    return $info;
}

function getInfo($type, $id, $sku = 'English (United States)') {
    global $sessionId;
    $sku = urlencode($sku);
    $baseUrl = "https://www.microsoft.com/en-us/api/controls/contentinclude/html?pageId=%s&host=www.microsoft.com&segments=software-download,windows11&query=&action=%s&sessionid=$sessionId%s&sdVersion=2";
    $prodUrlId = 'cd06bda8-ff9c-4a6e-912a-b92a21f42526';
    $skuUrlId = 'cfa9e580-a81e-4a4b-a846-7b21bf4e2e5b';
    if($type == 'Prod') $url = sprintf($baseUrl, $prodUrlId, 'getskuinformationbyProductedition', "&ProductEditionId=$id");
        else if($type == 'Sku') {
        $url = sprintf($baseUrl, $skuUrlId, 'GetProductDownloadLinksBySku', "&skuId=$id&language=$sku");
    }
    $headers = array(
        'Referer: https://tb.win-story.cn/',
    );

    for ($count = 1; $count < 5; $count++) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'controlAttributeMapping=');
        //curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $response = curl_exec($ch);
        if(curl_getinfo($ch, CURLINFO_HTTP_CODE) == 200) {
            curl_close($ch);
            break;
        };
        curl_close($ch);
    }
    return $response;
}

function parserProdInfo($productId, $html) {
    $info = [];
    $productName = substr($html->getElementsByTagName('i')->item(0)->textContent, 32) . identProduct($productId);
    if($productName == '') $productName = 'Unknown';
    $skus = [];
    $options = $html->getElementsByTagName('option');
    $optionCount = -1;
    foreach($options as $option) {
        if($optionCount++ == -1) continue;
        $id = explode('"', $option->getAttribute('value'))[3];
        $skus[$id] = $option->textContent;
    }

    if(!ksort($skus, SORT_NUMERIC)) ksort($skus);
    $category = checkCategory($productName);
    $infoTemp = checkInfo($productId, $skus, $category);

    $info['ProductName'] = $productName;
    $info['Skus'] = $skus;
    $info['Category'] = $category;
    $info['Status'] = $infoTemp['Status'];
    $info['Arch'] = $infoTemp['Arch'];
    return $info;
}
?>
