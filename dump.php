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

function printUsage() {
    echo "Usage: php dump.php [update|recheck] [--force] [--quiet]\n";
    exit();
}

function parseArgs($argv) {
    $args = [
        'mode' => false,
        'flags' => [
            'force' => false,
            'quiet' => false
        ]
    ];

    foreach($argv as $index => $arg) {
        $arg = strtolower($arg);
        switch($index) {
            case 0:
                if(count($argv) <= 1) {
                    printUsage();
                }
                continue 2;
            case 1:
                switch($arg) {
                    case 'update':
                        $args['mode'] = 'update';
                        break;
                    case 'recheck':
                        $args['mode'] = 'recheck';
                        break;
                    default:
                        printUsage();
                }
                break;
            case 2:
            case 3:
                switch($arg) {
                    case '--force':
                    case '-f':
                        $args['flags']['force'] = true;
                        break;
                    case '--quiet':
                    case '--q':
                        $args['mode']['quiet'] = true;
                        break;
                    default:
                        printUsage();
                }
                
                break;
        }
    }
    return $args;
}

$args = parseArgs($argv);

$wait = 600;

if(file_exists('dump.json') && file_exists('dump.json.lock')) {
    $lockfile = file_get_contents('dump.json.lock');
    $lock = json_decode($lockfile);
 if(time() - $lock->time < $wait && ($args['mode'] != 'update' || !$args['flags']['force'])) {
        exit("Error: Dumping in progress\n");
    } else unlink('dump.json.lock');
} else if(file_exists('dump.json.lock')) {
    unlink('dump.json.lock');
}

set_time_limit(10000);
ignore_user_abort(true);

// API v1 is deprecated.
$apiVersion = 2;

$continue = false;
$minProdId = 0;
$maxProdId = 3500;

$lock = array();
$lock['time'] = time();
$lock['status'] = 'Starting';
file_put_contents('dump.json.lock', json_encode($lock));

if(is_file('dump.json')) {
    $dump = json_decode(file_get_contents('dump.json'), true);
    if(isset($dump['TechInfo']['Version']) && $dump['TechInfo']['Version'] == '1.0') $continue = true;
}

if(!$continue) $dump = [
    'TechInfo' => [
        'Name' => 'Techbench dump',
        'Version' => '1.0',
        'CreationTime' => time(),
        'LastUpdateTime' => time(),
        'LastCheckUpdateTime' => time()        
    ],
    'ProdInfo' => []
];

$productNumber = count($dump['ProdInfo']);

if($productNumber > 0) {
    if(end($dump['ProdInfo'])) {
        $lastProdId = key($dump['ProdInfo']);
    } else $lastProdId = 0;
} else $lastProdId = 0;

if(!checkStrNum($lastProdId) || !checkStrNum($maxProdId)) exit("Error: Invalid ID\n");

if($lastProdId) $minProdId = $lastProdId + 1;

if($args['mode'] == 'update') {
    if($minProdId > $maxProdId) {
        $maxProdId = $lastProdId + 100;
    }

    dump($apiVersion, $minProdId, $maxProdId, $args['flags']);
  
    if(count($dump['ProdInfo']) > $productNumber) $dump['TechInfo']['LastUpdateTime'] = time();
    $dump['TechInfo']['LastCheckUpdateTime'] = time();
} else if($args['mode'] == 'recheck' && is_file('dump.json')) {
    if(isset($dump['TechInfo']['LastBlocked']) && checkStrNum($dump['TechInfo']['LastBlocked'])) {
        $lastBlocked = recheck($apiVersion, $lastProdId, $args['flags'], $lastBlocked);
    } else $lastBlocked = recheck($apiVersion, $lastProdId, $args['flags']);

    if(isset($lastBlocked)) {
        echo 'Please try again later.';
        $dump['TechInfo']['LastBlocked'] = $lastBlocked;
    } else if(isset($dump['TechInfo']['LastBlocked'])) unset($dump['TechInfo']['LastBlocked']);
}

file_put_contents('dump.json', json_encode($dump, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
$lock['status'] = 'Successful';
file_put_contents('dump.json.lock', json_encode($lock));
sleep(1);
unlink('dump.json.lock');
?>
