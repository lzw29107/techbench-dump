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
if(__DIR__ == getcwd() && basename($_SERVER['SCRIPT_NAME']) == basename(__FILE__)) {
    $v = $_SERVER['SERVER_PROTOCOL'];
    header("$v 403 Forbidden");
    exit();
}

require_once join(DIRECTORY_SEPARATOR, [dirname(__FILE__), '..', 'contrib', 'langconf.php']);

function getDomain() {
    if(isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
        $domain = $_SERVER['HTTP_X_FORWARDED_HOST'];
    } else if(isset($_SERVER['HTTP_HOST'])) {
        $domain = strstr($_SERVER['HTTP_HOST'], ':', true);
        if(!$domain) $domain = $_SERVER['HTTP_HOST'];
    } else {
        $domain = $_SERVER['SERVER_NAME'];
    }

    return $domain;
}

function getBaseUrl() {
    $baseUrl = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
    if(isset($_SERVER['HTTP_HOST'])) {
        $port = strstr($_SERVER['HTTP_HOST'], ':');
        if($port) $port = substr($port, 1);
    } else {
        $port = $_SERVER['SERVER_PORT'];
    }
    if(!is_numeric($port)) $port = 80;

    $baseUrl .= getDomain() . (in_array($port, ['80', '443']) ? '' : ':' . $port);

    return $baseUrl;
}

function getUrlWithoutParam($param = null) {
    $params = '';
    $separator = '?';
    foreach($_GET as $key => $val) {
        if($key == $param) continue;
        $params .= $separator . $key . '=' . urlencode($val);
        $separator = '&';
    }
    $params .= $separator;

    $shelf = explode('?', $_SERVER['REQUEST_URI']);
    $url = getBaseUrl() . $shelf[0] . $params;

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
    $orgId = 'y6jn8c31';
    $url = sprintf('https://vlscppe.microsoft.com/fp/tags.js?org_id=%s&session_id=%s', $orgId, urlencode($sessionId));

    for ($count = 1; $count < 5; $count++) {
        set_time_limit(35);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        //curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
        curl_setopt($ch, CURLOPT_REFERER, 'https://www.microsoft.com/en-us/software-download/windows11');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $response = curl_exec($ch);
        if(curl_getinfo($ch, CURLINFO_HTTP_CODE) == 200) {
            curl_close($ch);
            break;
        }
        curl_close($ch);
    }

    return $sessionId;
}

function getConfig($key = null) {
    $config = json_decode(@file_get_contents('config.json'), true);
    if(!$config || $config['version'] != '1.0' || !array_key_exists('php', $config) || !array_key_exists('autoupd', $config)) {
        setConfig('init');
        return getConfig($key);
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
        $config = [
            'version' => '1.0',
            'php' => findPHP(),
            'autoupd' => true
        ];
        file_put_contents('config.json', json_encode($config));
    }
}

function findPHP() {
    if(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $delimiter = ';';
        if(strpos(shell_exec('php -v'), 'PHP') !== false) $php = 'php.exe';
    } else {
        $delimiter = ':';
        if(shell_exec('command -v php')) $php = 'php';
    }

    if(!isset($php)) {
        $directory = dirname(php_ini_loaded_file());
        $php = $directory . DIRECTORY_SEPARATOR . $php;
        if(!is_file($php)) $php = false;
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

function in_subArray(mixed $needle, array $haystack, bool $strict = false) {
    foreach($haystack as $key => $subArray) {
        if(in_array($needle, $subArray, $strict)) return $key;
    }
    return false;
}

function identProduct($productId) {
    $appendVer = '';

    switch($productId) {
        case 265:
            $appendVer = 'Windows ADK Insider Preview - Build 14965';
            break;
        case 266:
            $appendVer = 'Windows HLK Insider Preview - Build 14965';
            break;
        case 267:
            $appendVer = 'Windows WDK Insider Preview - Build 14965';
            break;
        case 268:
            $appendVer = 'Windows Mobile Emulator Insider Preview - Build 14965';
            break;
        case 520:
            $appendVer = 'Windows Server Insider Preview - 17035';
            break;
        default:
            if ($productId >= 75 && $productId <= 82) $appendVer = ' (Threshold 1)';
            else if ($productId >= 99 && $productId <= 106) $appendVer = ' (Threshold 2)';
            else if ($productId >= 109 && $productId <= 116) $appendVer = ' (Threshold 2, February 2016 Update)';
            else if ($productId >= 178 && $productId <= 185) $appendVer = ' (Threshold 2, April 2016 Update)';
            else if ($productId >= 242 && $productId <= 247) $appendVer = ' (Redstone 1)';
            else if ($productId == 489) $appendVer = ' (Redstone 3)';
            else if ($productId == 2069 || $productId == 2070) $appendVer = ' (21H2 Original release)';
            break;
    }

    return $appendVer;
}

function checkCategory($apiVersion, $productName, $prodInfo) {
    $category = [];
    if(strpos($productName, 'IOT Core') !== false) {
        $category[] = 'IoTCore';
    }
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
                switch($actualBuild) {
                    case 14393:
                        $category[] = 'rs1';
                        break;
                    case 15063:
                        $category[] = 'rs2';
                        break;
                    case 16299:
                        $category[] = 'rs3';
                        break;
                    case 17134:
                        $category[] = 'rs4';
                        break;
                    case 17763:
                        $category[] = 'rs5';
                        break;
                    case 18362:
                        $category[] = '19H1';
                        break;
                    case 18363:
                        $category[] = '19H2';
                        break;
                    case 19041:
                        $category[] = 'vb';
                        break;
                    case 19042:
                        $category[] = '20H2';
                        break;
                    case 19043:
                        $category[] = '21H1';
                        break;
                    case 19044:
                        $category[] = '21H2';
                        break;
                    case 19045:
                        $category[] = '22H2';
                        break;
                    default:
                        break;
                }
            } else {
                $category[] = 'Win11';
                switch($actualBuild) {
                    case 22000:
                        $category[] = 'co';
                        break;
                    case 22621:
                        $category[] = 'ni';
                        break;
                    case 22631:
                        $category[] = '23H2';
                        break;
                    case 26100:
                        $category[] = 'ge';
                        break;
                    default:
                        break;
                }
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
    
    if(strpos($productName, 'Language Pack And FOD') !== false || strpos($productName, 'Server Language and Optional Features') !== false) $category[] = 'LOF';
    else if(strpos($productName, 'Language Pack') !== false || strpos($productName, 'LIP Pack') !== false) $category[] = 'LP';
    else if(stripos($productName, 'FOD') !== false) $category[] = 'FOD';
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

    if(count($category) == 0) {
        if($apiVersion == 2) {
            foreach($prodInfo['Skus'] as $sku) {
                if(strpos($sku['ProductDisplayName'], 'Xbox') !== false || strpos($sku['ProductDisplayName'], 'Game') !== false || strpos($sku['Description'], 'Xbox') !== false) {
                    $category[] = 'Xbox';
                    break;
                }
            }
            if(count($category) == 0) $category = ['Other'];
        } else {
            $category = ['Other'];
        }
    }
    return $category;
}

function checkInfo_offline($productId, $category) {
    $knownAvailable = array_merge([1145, 1149, 1524, 2198, 2217, 2220, 2221, 2297, 2298, 2299, 2577, 2580, 2581, 2582, 2585, 2588, 2589, 2591], range(2603, 3138));
    $info = [];
    $info['Status'] = 'Unknown';
    $info['Arch'] = ['Unknown'];
    if(in_array('Xbox', $category)) {
        $info['Arch'] = ['x64'];
    } else if(in_array('Office', $category)) {
        $info['Status'] = 'Unavailable';
        $info['Arch'] = ['x86', 'x64'];
        if(in_array('2011', $category)) $info['Arch'] = ['x64'];
    } else if(in_array('WIP', $category)) {
        if(!in_array($productId, $knownAvailable) && $productId < 3139) {
            $info['Status'] = 'Unavailable';
        }
    }
    return $info;
}

function checkInfo($apiVersion, $productName, $productId, $skus, $category, $prodInfo) {
    $knownAvailable = [48, 52, 55, 61, 62, 489, 642, 1057, 1217, 1460, 2378, 2616, 2617, 2618, 2860, 2861, 3113, 3114, 3115, 3131, 3132, 3133];
    $blocked = 'We are unable to complete your request at this time. Some users, entities and locations are banned from using this service. For this reason, leveraging anonymous or location hiding technologies when connecting to this service is not generally allowed. If you believe that you encountered this problem in error, please try again. If the problem persists you may contact  Microsoft Support – Contact Us  page for assistance. Refer to message code 715-123130 and';
    $option = end($skus);
    $id = key($skus);
    $info = ['Arch' => []];

    if(!array_intersect(['WIP', 'Xbox', 'Office'], $category)) {
        if(in_array($productId, $knownAvailable) || $productId > 3138) {
            switch($apiVersion) {
                case 1:
                    $downHTML = new DOMDocument();
                    $downHTML->loadHTML(getInfo($apiVersion, 'Sku', $id, sku: $option));
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
                        foreach($downBtns as $downBtn) {
                            if($downBtn->textContent == 'IsoX86') $info['Arch'][] = 'x86';
                            if($downBtn->textContent == 'IsoX64') $info['Arch'][] = 'x64';
                            if($downBtn->textContent == 'Unknown') $info['Arch'][] = 'neutral';
                        }
                    }
                    break;
                case 2:
                    $downInfo = getInfo($apiVersion, 'Sku', $id);
                    if($downInfo) {
                        $downInfo = json_decode($downInfo, true);
                    } else {
                        return false;
                    }
                    if($downInfo) {
                        if(!isset($downInfo['Errors'])) {
                            $info['Status'] = 'Available';
                            if(isset($downInfo['ProductDownloadOptions'])) {
                                foreach($downInfo['ProductDownloadOptions'] as $option) {
                                    if(isset($option['DownloadType'])) {
                                        switch($option['DownloadType']) {
                                            case 0:
                                                $info['Arch'][] = 'x86';
                                                break;
                                            case 1:
                                                $info['Arch'][] = 'x64';
                                                break;
                                            case 2:
                                                $info['Arch'][] = 'arm64';
                                                break;
                                            default:
                                                $info['Arch'][] = 'Unknown';
                                                break;
                                        }
                                    }
                                }
                            }
                        } else {
                            $info['Status'] = 'Unavailable';
                        }
                    }
                    break;
                default:
                    return false;
            }
        } else {
            $info['Status'] = 'Unavailable';
        }
    } else {
        $info = checkInfo_offline($productId, $category);
    }

    if($apiVersion == 2) {
        foreach($prodInfo['Skus'] as $sku) {
            if(!in_array('arm', $info['Arch']) && (stripos($sku['Description'], 'arm32') !== false || stripos($sku['Description'], 'armfre') !== false)) $info['Arch'][] = 'arm';
            if(!in_array('arm64', $info['Arch']) && stripos($sku['Description'], 'arm64') !== false) $info['Arch'][] = 'arm64';
            if(!in_array('x86', $info['Arch']) && (stripos($sku['Description'], 'x86') !== false || stripos($sku['Description'], '32-bit') !== false || stripos($sku['Description'], 'x32') !== false)) $info['Arch'][] = 'x86';
            if(!in_array('x64', $info['Arch']) && (stripos($sku['Description'], 'x64') !== false || stripos($sku['Description'], '64-bit') !== false)) $info['Arch'][] = 'x64';
            if(count($sku['FriendlyFileNames']) > 1) {
                foreach($sku['FriendlyFileNames'] as $fileName) {
                    if(!in_array('x86', $info['Arch']) && stripos($fileName, 'x32') !== false) $info['Arch'][] = 'x86';
                    if(!in_array('x64', $info['Arch']) && stripos($fileName, 'x64') !== false) $info['Arch'][] = 'x64';    
                }
            }
        }
    }

    if(array_intersect(['Win7', 'Win81', 'Win10', 'RSAT', 'Symbols'], $category)) $info['Arch'] = ['x86', 'x64'];
    if(array_intersect(['Win11', 'WinSrv', 'DAC'], $category)) $info['Arch'] = ['x64'];
    if(stripos($productName, 'ARM64') !== false) $info['Arch'] = ['arm64'];
    
    if(array_intersect(['SDK', 'WDK', 'HLK', 'EWDK'], $category)) {
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
    } else if(in_array('MTBF', $category)) {
        $info['Arch'] = ['arm'];
    }

    if(count($info['Arch']) == 0) $info['Arch'] = ['Unknown'];

    return $info;
}

function getInfo($apiVersion, $type, $id = 0, $lang = 'en-US', $sku = ['Name' => 'English (United States)']) {
    global $sessionId;

    if($type != 'Page') {
        $sku = urlencode($sku['Name']);
        switch ($apiVersion) {
            case 1:
                $baseUrl = "https://www.microsoft.com/$lang/api/controls/contentinclude/html?pageId=%s&host=www.microsoft.com&segments=software-download,windows11&query=&action=%s&sessionid=$sessionId%s&sdVersion=2";
                $prodUrlId = 'cd06bda8-ff9c-4a6e-912a-b92a21f42526';
                $skuUrlId = 'cfa9e580-a81e-4a4b-a846-7b21bf4e2e5b';
                if($type == 'Prod') {
                    $url = sprintf($baseUrl, $prodUrlId, 'getskuinformationbyProductedition', "&ProductEditionId=$id");
                } else if($type == 'Sku') {
                    $url = sprintf($baseUrl, $skuUrlId, 'GetProductDownloadLinksBySku', "&skuId=$id&language=$sku");
                }
                break;
            case 2:
                $baseUrl = "https://www.microsoft.com/software-download-connector/api/%s?profile=606624d44113&ProductEditionId=%s&SKU=%s&friendlyFileName=undefined&Locale=$lang&sessionID=$sessionId";
                if($type == 'Prod') {
                    $url = sprintf($baseUrl, 'getskuinformationbyProductedition', $id, 'undefined');
                } else if($type == 'Sku') {
                    $url = sprintf($baseUrl, 'GetProductDownloadLinksBySku', 'undefined', $id);
                }
                break;
            default:
                return false;
        }
    } else {
        $url = "https://www.microsoft.com/$lang/software-download/windows11";
    }

    $headers = [
        'Referer: https://tb.win-story.cn/'
    ];

    for ($count = 1; $count < 5; $count++) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        if($type == 'Page') curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $response = curl_exec($ch);
        if(curl_getinfo($ch, CURLINFO_HTTP_CODE) == 200) {
            curl_close($ch);
            break;
        }
        curl_close($ch);
    }
    return $response;
}

function parseProdInfo($apiVersion, $productId, $prodInfo) {
    global $enLangNames;
    $skus = [];
    $parsedSkus = [];
    $langCount = 0;
    $skuCount = 0;

    switch($apiVersion) {
        case 1:
            $productName = substr($prodInfo->getElementsByTagName('i')->item(0)->textContent, 32) . identProduct($productId);
            if($productName == '') $productName = 'Unknown';
            $options = $prodInfo->getElementsByTagName('option');
            $optionCount = -1;
            $category = checkCategory($apiVersion, $productName, $prodInfo);
            foreach($options as $option) {
                if($optionCount++ == -1) continue;
                $id = explode('"', $option->getAttribute('value'))[3];
                $skus[$id] = [];
                $skus[$id]['Name'] = $option->textContent;
            }
            break;
        case 2:
            $productName = $prodInfo['Skus'][0]['ProductDisplayName'] . identProduct($productId);
            if($productName == '') $productName = 'Unknown';
            $category = checkCategory($apiVersion, $productName, $prodInfo);
            $skuName = in_array('Xbox', $category) ? 'ProductDisplayName' : 'Language';
            foreach($prodInfo['Skus'] as $sku) {
                $id = $sku['Id'];
                $skus[$id] = [];
                // $skus[$id] = $sku['LocalizedLanguage'];
                $skus[$id]['Name'] = $sku[$skuName];
                $skus[$id]['FileNames'] = $sku['FriendlyFileNames'];
                $skus[$id]['Description'] = $sku['Description'];
            }
            break;
        default:
            return false;
    }
    if(!ksort($skus, SORT_NUMERIC)) ksort($skus);            

    $info = checkInfo($apiVersion, $productName, $productId, $skus, $category, $prodInfo);
    if(!$info) return false;

    foreach($skus as $skuId => $sku) {
        $lang = in_subArray($sku['Name'], $enLangNames);
        if($lang) {
            $sku['Name'] = $lang;
            $langCount++;
        } else {
            $skuCount++;
        }
        $parsedSkus[$skuId]['Name'] = $sku['Name'];
        if($apiVersion == 2) {
            $parsedSkus[$skuId]['FileNames'] = $sku['FileNames'];
            $parsedSkus[$skuId]['Description'] = $sku['Description'];
        }
    }

    $skuName = $langCount > $skuCount ? 'Language' : 'Sku';

    $parsedInfo = [
        'Name' => $productName,
        'Category' => $category,
        'Status' => $info['Status'],
        'Arch' => $info['Arch'],
        $skuName => $parsedSkus,
    ];

    return $parsedInfo;
}
?>
