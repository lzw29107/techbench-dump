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
    if(isset($_SERVER['HTTPS'])) $baseUrl .= 'https://';
        else {
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

function genSessionID() {
    $time = time();
    $SessionID = preg_replace_callback('/[xy]/', function ($matches) use ($time) {
        $random = (($time + rand(0, 15)) % 16);
        if ($matches[0] === 'y') $random = ($random & 3) | 8;
        $time = floor($time / 16);
        $random = dechex($random);
        return $random;
    }, 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx');
    return $SessionID;
}

function SessionIDInit() {
    $SessionID = genSessionID();

    $req = curl_init('https://vlscppe.microsoft.com/fp/tags.js?org_id=y6jn8c31&session_id=' . urlencode($SessionID));
    curl_setopt($req, CURLOPT_HEADER, 0);
    curl_setopt($req, CURLOPT_REFERER, 'https://www.microsoft.com/en-us/software-download/windows11');
    curl_setopt($req, CURLOPT_RETURNTRANSFER, true); 

    $out = curl_exec($req);
    curl_close($req);

    return $SessionID;
}


function get_config($key = null) {
    $config = json_decode(@file_get_contents('config.json'), true);
    if(!$config || $config['version'] != '1.0' || !array_key_exists('php', $config) || !array_key_exists('autoupd', $config)) {
        set_config('init');
        return get_config();
    }
    if($key) return $config[$key];
    return $config;
}

function set_config($type, $key = null, $value = null) {
    if($type == 'set') {
        $config = json_decode(@file_get_contents('config.json'), true);
        if(!$config || $config['version'] != '1.0') {
            set_config('init');
            set_config('set', $key, $value);
        }
        if($key && $value) $config[$key] = $value;
        file_put_contents('config.json', json_encode($config));
    } else if($type == 'init') {
        $config = [];
        $config['version'] = '1.0';
        $config['php'] = get_php_location();
        $config['autoupd'] = true;
        file_put_contents('config.json', json_encode($config));
    }
}

function get_php_location() {
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

function exec_background($php, $command) {
    if(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        popen("start /B $php $command >nul 2>nul", 'r');
    } else {
        shell_exec("$php $command >/dev/null 2>&1 &");
    }
}

function identProduct($ProductID) {
    $appendVer = "";

    if ($ProductID >= 75 && $ProductID <= 82) $appendVer = " (Threshold 1)";
    else if ($ProductID >= 99 && $ProductID <= 106) $appendVer = " (Threshold 2)";
    else if ($ProductID >= 109 && $ProductID <= 116) $appendVer = " (Threshold 2, February 2016 Update)";
    else if ($ProductID >= 178 && $ProductID <= 185) $appendVer = " (Threshold 2, April 2016 Update)";
    else if ($ProductID >= 242 && $ProductID <= 247) $appendVer = " (Redstone 1)";
    else if ($ProductID == 489) $appendVer = " (Redstone 3)";
    else if ($ProductID == 2069 || $ProductID == 2070) $appendVer = " (21H2 Original release)";

    return $appendVer;
}

function checkCategory($ProductName) {
    global $firstOption, $Skus;
    $Category = [];
    $build = substr(strstr($ProductName, ' - '), 3);
    if(strpos($ProductName, 'Admin Center') !== false || strpos($ProductName, 'Honolulu') !== false) {
        $Category[] = 'AdminCenter';
        if(strpos($ProductName, 'Preview') !== false) $Category[] = 'WIP';
    } else if($build) {
        if(strpos($build, 'Build ') !== false) {
            $Build = substr(strstr($build, 'Build '), 6);
            if(strpos($ProductName, 'IoT Core') !== false) {
                $Category[] = 'IoTCore';
                if(strpos($Build, '17763') !== false) unset($Build);
            }
        } else if(is_numeric($build)) $Build = $build;
        else if(strpos($ProductName, 'Media Feature') !== false) {
            $Category[] = 'MF';
        }
    }
    if(isset($Build)) {
        if($Build <= 10586) $Category[] = 'Win10';
        else if($Build > 10586) {
            $Category[] = 'WIP';
            if($Build < 22000) {
                $Category[] = 'Win10';
                if($Build == 14393) $Category[] = 'rs1';
                else if($Build == 15063) $Category[] = 'rs2';
                else if($Build == 16299) $Category[] = 'rs3';
                else if($Build == 17134) $Category[] = 'rs4';
                else if($Build == 17763) $Category[] = 'rs5';
                else if($Build == 18362) $Category[] = '19H1';
                else if($Build == 18363) $Category[] = '19H2';
                else if($Build == 19041) $Category[] = 'vb';
                else if($Build == 19042) $Category[] = '20H2';
                else if($Build == 19043) $Category[] = '21H1';
                else if($Build == 19044) $Category[] = '21H2';
                else if($Build == 19045) $Category[] = '22H2';
            } else {
                $Category[] = 'Win11';
                if($Build == 22000) $Category[] = 'co';
                else if($Build == 22621) $Category[] = 'ni';
                else if($Build == 22631) $Category[] = '23H2';
            }
        }
    } else if(strpos($ProductName, 'Windows 7') !== false) $Category[] = 'Win7';
        else if(strpos($ProductName, 'Windows 8.1') !== false) $Category[] = 'Win81';
        else if(strpos($ProductName, 'Windows 10') !== false || strpos($ProductName, 'Media Feature') !== false) {
            $Category[] = 'Win10';
            if(strpos($ProductName, 'Threshold 1') !== false) $Category[] = 'th1';
            else if(strpos($ProductName, 'Threshold 2') !== false) $Category[] = 'th2';
            else if(strpos($ProductName, '1607') !== false || strpos($ProductName, 'Redstone 1') !== false) $Category[] = 'rs1';
            else if(strpos($ProductName, '1703') !== false) $Category[] = 'rs2';
            else if(strpos($ProductName, '1709') !== false || strpos($ProductName, 'Redstone 3') !== false) $Category[] = 'rs3';
            else if(strpos($ProductName, '1803') !== false || strpos($ProductName, 'RS4') !== false) $Category[] = 'rs4';
            else if(strpos($ProductName, '1809') !== false) $Category[] = 'rs5';
            else if(strpos($ProductName, '1903') !== false) $Category[] = '19H1';
            else if(strpos($ProductName, '1909') !== false) $Category[] = '19H2';
            else if(strpos($ProductName, '2004') !== false) $Category[] = 'vb';
            else if(strpos($ProductName, '20H2') !== false) $Category[] = '20H2';
            else if(strpos($ProductName, '21H1') !== false) $Category[] = '21H1';
            else if(strpos($ProductName, '21H2') !== false) $Category[] = '21H2';
            else if(strpos($ProductName, '22H2') !== false) $Category[] = '22H2';
    } else if(strpos($ProductName, 'Windows 11') !== false) {
        $Category[] = 'Win11';
        if(strpos($ProductName, '21H2') !== false || !strpos($ProductName, 'H2') !== false) $Category[] = 'co';
        else if(strpos($ProductName, '22H2') !== false) $Category[] = 'ni';
        else if(strpos($ProductName, '23H2') !== false) $Category[] = '23H2';
        if(strpos($ProductName, 'ARM64') !== false) $Category[] = 'ARM64';
    } else if(strpos($ProductName, ' 2007') !== false) {
        $Category[] = 'Office';
        $Category[] = '2007';
    } else if(strpos($ProductName, ' 2010') !== false) {
        $Category[] = 'Office';
        $Category[] = '2010';
    } else if(strpos($ProductName, ' 2011') !== false) {
        $Category[] = 'Office';
        $Category[] = '2011';
    }

    if(strpos($ProductName, 'Language Pack') !== false || strpos($ProductName, 'LIP Pack') !== false || strpos($ProductName, 'Server Language and Optional Features') !== false || stripos($ProductName, 'FOD') !== false) $Category[] = 'LOF';
    else if(strpos($ProductName, 'SDK') !== false) $Category[] = 'SDK';
    else if(strpos($ProductName, ' WDK') !== false) $Category[] = 'WDK';
    else if(strpos($ProductName, 'EWDK') !== false) $Category[] = 'EWDK';
    else if(strpos($ProductName, 'HLK') !== false) $Category[] = 'HLK';
    else if(strpos($ProductName, 'MTBF') !== false) $Category[] = 'MTBF';
    else if(strpos($ProductName, 'Preinstallation Environment') !== false || strpos($ProductName, 'ADK') !== false) $Category[] = 'ADK';
    else if(strpos($ProductName, 'Desktop App Converter') !== false || strpos($ProductName, 'DAC') !== false) $Category[] = 'DAC';
    else if(strpos($ProductName, 'RSAT') !== false) $Category[] = 'RSAT';
    else if(strpos($ProductName, 'Symbols') !== false) $Category[] = 'Symbols';
    else if(strpos($ProductName, 'Mobile Emulator') !== false) $Category[] = 'MobileEmu';
    else if(strpos($ProductName, 'Inbox Apps') !== false) $Category[] = 'InboxApps';

    if(strpos($ProductName, 'Server') !== false) {
        $Category[] = 'WinSrv';
    }
    if(strpos($ProductName, 'Xbox') !== false || strpos($ProductName, 'GDK') !== false || strpos($ProductName, 'Submission') !== false || strpos($ProductName, ' -  ( only) ') !== false || strpos($ProductName, 'Recovery') !== false || strpos($ProductName, 'Xfest') !== false || strpos($ProductName, 'GSDK') !== false) {
        $Category[] = 'Xbox';
    }
    if(count($Category) == 0) $Category[] = 'Other';
    return $Category;
}

function CheckInfo_offline($ProductID, $Category) {
    $knownValid = array_merge([1145, 1149, 1524, 2198, 2217, 2220, 2221, 2297, 2298, 2299, 2577, 2580, 2581, 2582, 2585, 2588, 2589, 2591], range(2607, 2735));
    $Info = [];
    $Info['Validity'] = 'Unknown';
    $Info['Arch'] = 'Unknown';
    if(in_array('Xbox', $Category)) $Info['Arch'] = ['x64'];
        else if(in_array('Office', $Category)) {
        $Info['Validity'] = 'Invalid';
        $Info['Arch'] = ['x86', 'x64'];
        if(in_array('2011', $Category)) $Info['Arch'] = ['x64'];
    } else if(in_array('WIP', $Category)) {
        if(!in_array($ProductID, $knownValid) && $ProductID < 2736) {
            $Info['Validity'] = 'Invalid';
            return $Info;
        }
    }
    return $Info;
}

function checkInfo($ProductID, $Skus, $Category) {
    $knownValid = [48, 52, 55, 61, 62, 489, 642, 1057, 1217, 1460, 2378, 2616, 2617, 2618, 2860, 2861, 2935, 2936];
    $blocked = 'We are unable to complete your request at this time. Some users, entities and locations are banned from using this service. For this reason, leveraging anonymous or location hiding technologies when connecting to this service is not generally allowed. If you believe that you encountered this problem in error, please try again. If the problem persists you may contact  Microsoft Support – Contact Us  page for assistance. Refer to message code 715-123130 and';
    $Info = [];
    $Option = end($Skus);
    $ID = key($Skus);

    if(!array_intersect(['WIP', 'Xbox', 'Office'], $Category)) {
        if(in_array($ProductID, $knownValid) || $ProductID > 2956) {
            $downhtml = new DOMDocument();
            $downhtml->loadHTML(getInfo('Sku', $ID, $Option));
            if($downhtml->getElementById('errorModalMessage')) {
                $Info['Validity'] = 'Invalid';
                $errorMsg = $downhtml->getElementById('errorModalMessage')->textContent;
                if($errorMsg == $blocked) {
                    $Info['Validity'] = 'Valid';
                    echo "The Server IP is blocked by Microsoft.\n";
                }
                $Info['Arch'] = ['Unknown'];
            } else {
                $Info['Validity'] = 'Valid';
                $xpath = new DOMXPath($downhtml);
                $downBtns = $xpath->query('//span[contains(@class, "product-download-type")]');
                $Arch = [];
                foreach($downBtns as $downBtn) {
                    if($downBtn->textContent == 'IsoX86') $Arch[] = 'x86';
                    if($downBtn->textContent == 'IsoX64') $Arch[] = 'x64';
                    if($downBtn->textContent == 'Unknown') $Arch[] = 'neutral';
                    if(count($Arch) == 0) $Arch[] = 'Unknown';
                }
            }
        } else {
            $Info['Validity'] = 'Invalid';
            $Info['Arch'] = ['Unknown'];
        }
    } else {
        $Info = CheckInfo_offline($ProductID, $Category);
    }
    if(array_intersect(['Win7', 'Win81', 'Win10', 'RSAT', 'Symbols'], $Category)) $Info['Arch'] = ['x86', 'x64'];
    if(array_intersect(['Win11', 'WinSrv', 'DAC'], $Category)) {
        $Info['Arch'] = ['x64'];
    }
    if(in_array('ARM64', $Category)) $Info['Arch'] = ['arm64'];

    if(array_intersect(['LOF', 'SDK', 'WDK', 'HLK', 'EWDK', 'InboxApps'], $Category)) $Info['Arch'] = ['neutral'];
        else if(array_intersect(['ADK', 'IoTCore'], $Category)) {
        if(in_array('Win10', $Category)) $Info['Arch'] = ['x86', 'x64', 'arm', 'arm64'];
        else if(in_array('Win11', $Category)) {
            $Info['Arch'] = ['x64', 'arm64'];
        }
    } else if(in_array('MobileEmu', $Category)) $Info['Arch'] = ['x86'];
        else if(in_array('AdminCenter', $Category)) {
        $Info['Arch'] = ['x64'];
    }
    return $Info;
}

function getInfo($Type, $ID, $Sku = 'English (United States)') {
    global $SessionID;
    $Sku = urlencode($Sku);
    $baseUrl = "https://www.microsoft.com/en-us/api/controls/contentinclude/html?pageId=%s&host=www.microsoft.com&segments=software-download,windows11&query=&action=%s&sessionid=$SessionID%s&sdVersion=2";
    $ProdUrlID = 'cd06bda8-ff9c-4a6e-912a-b92a21f42526';
    $SkuUrlID = 'cfa9e580-a81e-4a4b-a846-7b21bf4e2e5b';
    if($Type == 'Prod') $url = sprintf($baseUrl, $ProdUrlID, 'getskuinformationbyProductedition', "&ProductEditionId=$ID");
        else if($Type == 'Sku') {
        $url = sprintf($baseUrl, $SkuUrlID, 'GetProductDownloadLinksBySku', "&skuId=$ID&language=$Sku");
    }
    $headers = array(
        //'sec-ch-ua: "Chromium";v="122", "Not(A:Brand";v="24", "Microsoft Edge";v="122"',
        //'sec-ch-ua-platform: "Windows"',
        //'DNT: 1',
        //'sec-ch-ua-mobile: ?0',
        //'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36 Edg/122.0.0.0',
        'Content-type: application/x-www-form-urlencoded; charset=UTF-8',
        //'Accept: */*',
        //'Origin: https://tb.lzw29107.repl.co',
        //'X-Edge-Shopping-Flag: 1',
        //'Sec-Fetch-Site: cross-site',
        //'Sec-Fetch-Mode: cors',
        //'Sec-Fetch-Dest: empty',
        'Referer: https://tb.lzw29107.repl.co/',
        //'Accept-Encoding: gzip, deflate, br',
        //'Accept-Language: en-US;q=1',
        //'X-Requested-With: XMLHttpRequest'
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

function parserProdInfo($ProductID, $html) {
    $Info = [];
    $ProductName = substr($html->getElementsByTagName('i')->item(0)->textContent, 32) . identProduct($ProductID);
    if($ProductName == '') $ProductName = 'Unknown';
    $Skus = [];
    $Options = $html->getElementsByTagName('option');
    $OptionCount = -1;
    foreach($Options as $Option) {
        if($OptionCount++ == -1) continue;
        $ID = explode('"', $Option->getAttribute('value'))[3];
        $Skus[$ID] = $Option->textContent;
    }

    if(!ksort($Skus, SORT_NUMERIC)) ksort($Skus);
    $Category = checkCategory($ProductName);
    $info = checkInfo($ProductID, $Skus, $Category);

    $Info['ProductName'] = $ProductName;
    $Info['Skus'] = $Skus;
    $Info['Category'] = $Category;
    $Info['Validity'] = $info['Validity'];
    $Info['Arch'] = $info['Arch'];
    return $Info;
}
?>
