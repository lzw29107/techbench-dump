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

if(!isset($argv[1]) || !in_array($argv[1], array('init', 'set')) || ($argv[1] == 'init' && isset($argv[2])) || ($argv[1] == 'set') && !isset($argv[3])) {
    exit("Usage:\nphp config.php init\nphp config.php set [key] [value]\n");
}

if($argv[1] == 'init') set_config('init');
if($argv[1] == 'set') {
    $key = $argv[2];
    if(filter_var($argv[3], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) !== null) {
        $value = boolval($argv[3]);
    } else if($key == 'autoupd') {
        exit('Invalid value.');
    } else if($key == 'php' && $argv[3] != 'php' && !is_file($argv[3])) {
        exit('Invalid path.');
    } else $value = $argv[3];
    set_config('set', $key, $value);
}
?>