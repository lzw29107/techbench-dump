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
if(php_sapi_name() != 'cli') {
    $v = $_SERVER['SERVER_PROTOCOL'];
    header("$v 403 Forbidden");
    exit();
}
require_once 'shared/eHandler.php';
require_once 'shared/utils.php';
require_once 'shared/dump.php';

global $dom, $Tech, $Prod;

if(!isset($argv[1]) || !in_array($argv[1], array('update', 'recheck')) || (isset($argv[2]) && $argv[2] != '--force') || isset($argv[3])) {
    exit("Usage: php dump.php [update|recheck] [--force]\n");
}

if(file_exists('dump.xml') && file_exists('dump.xml.lock')) {
    $lockfile = file_get_contents('dump.xml.lock');
    $lock = json_decode($lockfile);
    if(@in_array($lock->status, array('Exception', 'Error'))) {
        unlink('dump.xml.lock');
    } else if(time() - $lock->time < 600 && ($argv[1] != 'update' || (!isset($argv[2]) || $argv[2] != '--force'))) {
        exit('Dumping in progress.');
    } else unlink('dump.xml.lock');
} else if(file_exists('dump.xml.lock')) {
    unlink('dump.xml.lock');
}

set_time_limit(10000);
ignore_user_abort(true);

$Continue = false;
$minProdID = 0;
$maxProdID = 3000;

$lock = array();
$lock['time'] = time();
file_put_contents('dump.xml.lock', json_encode($lock));

$dom = new DOMDocument('1.0', 'UTF-8');

if(is_file('dump.xml')) {
    @$dom->load('dump.xml');
    if(!libxml_get_last_error()) {
        $Continue = true;
    } else {
        if($argv[1] != 'update') exit();
        unset($dom);
        $dom = new DOMDocument('1.0', 'UTF-8');
    }
}
$dom->formatOutput = true;
$dom->preserveWhiteSpace = false;

if(!$Continue) {
    $root = $dom->createElementNS("urn:TBDW","Data");
    $root->setAttribute('Version', '1.0');
    $dom->appendChild($root);

    $Tech = $dom->createElement('TechInfo');
    $root->appendChild($Tech);

    $Prod = $dom->createElement('ProdInfo');
    $root->appendChild($Prod);
  
    $Tech->setAttribute('CreationTime', time());
    $Tech->setAttribute('LastUpdateTime', time());
    $Tech->setAttribute('LastCheckUpdateTime', time());
}

if($Continue) {
    $root = $dom->getElementsByTagName('Data')->item(0);
    $Tech = $dom->getElementsByTagName('TechInfo')->item(0);
    $Prod = $dom->getElementsByTagName('ProdInfo')->item(0);
}

$ProductNumber = $Prod->childElementCount;

if($ProductNumber > 0) {
    if($Prod->lastElementChild->hasAttribute('ID')) {
        $lastProdID = $Prod->lastElementChild->getAttribute('ID');
    } else {
        $lastProdID = 0;
    }
} else {
    $lastProdID = 0;
}

if(!checkStrNum($lastProdID) || !checkStrNum($maxProdID)) exit('Invalid ID');

if($lastProdID) $minProdID = $lastProdID + 1;

if($argv[1] == 'update') {
    if($minProdID > $maxProdID) {
        $maxProdID = $lastProdID + 100;
    }
  
    dump($minProdID, $maxProdID);
  
    if($Prod->childElementCount > $ProductNumber) $Tech->setAttribute('LastUpdateTime', time());
    $Tech->setAttribute('LastCheckUpdateTime', time());
}
if($argv[1] == 'recheck' && is_file('dump.xml')) {
    if($Tech->hasAttribute('LastBlocked')) $LastBlocked = $Tech->getAttribute('LastBlocked');
    if(isset($LastBlocked)) {
        $lastBlocked = recheck($lastProdID, $lastBlocked);
    } else $lastBlocked = recheck($lastProdID);
  
    if(isset($lastBlocked)) {
        echo 'Please try again later.';
        $Tech->setAttribute('lastBlocked', $lastBlocked);
    } else if($Tech->hasAttribute('LastBlocked')) $Tech->removeAttribute('LastBlocked');
}

$dom->save('dump.xml');
indentContent('dump.xml');
$lock['status'] = 'Successful';
file_put_contents('dump.xml.lock', json_encode($lock));
sleep(1);
unlink('dump.xml.lock');
?>
