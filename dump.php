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
require_once 'shared/utils.php';
require_once 'shared/dump.php';

global $dump;

if(!isset($argv[1]) || !in_array($argv[1], array('update', 'recheck')) || (isset($argv[2]) && $argv[2] != '--force') || isset($argv[3])) {
    exit("Usage: php dump.php [update|recheck] [--force]\n");
}

if(file_exists('dump.json') && file_exists('dump.json.lock')) {
    $lockfile = file_get_contents('dump.json.lock');
    $lock = json_decode($lockfile);
 if(time() - $lock->time < 600 && ($argv[1] != 'update' || (!isset($argv[2]) || $argv[2] != '--force'))) {
        exit('Dumping in progress.');
    } else unlink('dump.json.lock');
} else if(file_exists('dump.json.lock')) {
    unlink('dump.json.lock');
}

set_time_limit(10000);
ignore_user_abort(true);

$Continue = false;
$minProdID = 0;
$maxProdID = 3500;

$lock = array();
$lock['time'] = time();
file_put_contents('dump.json.lock', json_encode($lock));

if(is_file('dump.json')) {
    $dump = json_decode(file_get_contents('dump.json'), true);
    if(isset($dump['TechInfo']['Version']) && $dump['TechInfo']['Version'] == '1.0') $Continue = true;
}

if(!$Continue) $dump = [
    'TechInfo' => [
        'Name' => 'Techbench dump',
        'Version' => '1.0',
        'CreationTime' => time(),
        'LastUpdateTime' => time(),
        'LastCheckUpdateTime' => time()        
    ],
    'ProdInfo' => []
];

$ProductNumber = count($dump['ProdInfo']);

if($ProductNumber > 0) {
    if(end($dump['ProdInfo'])) {
        $lastProdID = key($dump['ProdInfo']);
    } else $lastProdID = 0;
} else $lastProdID = 0;

if(!checkStrNum($lastProdID) || !checkStrNum($maxProdID)) exit('Invalid ID');

if($lastProdID) $minProdID = $lastProdID + 1;

if($argv[1] == 'update') {
    if($minProdID > $maxProdID) {
        $maxProdID = $lastProdID + 100;
    }
  
    dump($minProdID, $maxProdID);
  
    if(count($dump['ProdInfo']) > $ProductNumber) $dump['TechInfo']['LastUpdateTime'] = time();
    $dump['TechInfo']['LastCheckUpdateTime'] = time();
}
if($argv[1] == 'recheck' && is_file('dump.json')) {
    if(isset($dump['TechInfo']['LastBlocked'])) $LastBlocked = $dump['TechInfo']['LastBlocked'];
    if(checkStrNum($LastBlocked)) {
        $lastBlocked = recheck($lastProdID, $lastBlocked);
    } else $lastBlocked = recheck($lastProdID);
  
    if(isset($lastBlocked)) {
        echo 'Please try again later.';
        $dump['TechInfo']['LastBlocked'] = $lastBlocked;
    } else if(isset($dump['TechInfo']['LastBlocked'])) unset($dump['TechInfo']['LastBlocked']);
}

file_put_contents('dump.json', json_encode($dump, JSON_PRETTY_PRINT));
$lock['status'] = 'Successful';
file_put_contents('dump.json.lock', json_encode($lock));
sleep(1);
unlink('dump.json.lock');
?>
