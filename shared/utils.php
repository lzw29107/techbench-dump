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

function genSessionID() {
    $time = time();
    $SessionID = preg_replace_callback('/[xy]/', function ($matches) use ($time) {
        $random = (($time + rand(0, 15)) % 16);
        if ($matches[0] === 'y') {
            $random = ($random & 3) | 8;
        }
        $time = floor($time / 16);
        $random = dechex($random);
        return $random;
    }, 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx');
    return $SessionID;
}

function SessionIDInit() {
    $SessionID = genSessionID();

    $req = curl_init("https://vlscppe.microsoft.com/fp/tags.js?org_id=y6jn8c31&session_id=" . urlencode($SessionID));
    curl_setopt($req, CURLOPT_HEADER, 0);
    curl_setopt($req, CURLOPT_REFERER, "https://www.microsoft.com/en-us/software-download/windows11");
    curl_setopt($req, CURLOPT_RETURNTRANSFER, true); 

    $out = curl_exec($req);
    curl_close($req);

    return $SessionID;
}

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
?>