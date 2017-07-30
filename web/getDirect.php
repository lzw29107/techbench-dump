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

$fileName = isset($_GET['fileName']) ? $_GET['fileName'] : 'Win7_Pro_SP1_English_x64.iso';
$sessionId = isset($_GET['sessionId']) ? $_GET['sessionId'] : false;
$prodId = isset($_GET['id']) ? $_GET['id'] : '2';
require 'shared/get.php';

if(!$sessionId) {
    $sessionId = randStr(8).'-'.randStr(4).'-'.randStr(4).'-'.randStr(4).'-'.randStr(12);
    $langList = getLangList($prodId, "en-us", $sessionId);
    if(isset($langList['error'])) {
        echo 'There was an error processing your request.';
        die();
    }
}

$downList = getDownloadByName($fileName, $sessionId);
if(isset($downList['error'])) {
    echo 'There was an error processing your request.';
    die();
}

$headers = get_headers($downList['downloadLink']);
$headers = preg_grep('/HTTP\/.+ \d\d\d /', $headers);
$status = preg_replace('/HTTP\/.*? /', '', end($headers));

if($status != '200 OK') {
    echo "<h1>Validation failed!</h1>";
    echo "Your request has been processed successfully, but remote server returned an error during validation stage:<br>";
    echo $status;

    if($status == '404 Not Found') {
        echo "<br><br>Validation script has determined, that requested file does not exist.";
        echo "<br>Please check if entered file name is correct.";
    } elseif($status == '403 Forbidden') {
        echo "<br><br>Validation script has determined, that requested file is protected.";
        echo "<br>Please check back later.";
    }

    echo "<br><br>If you think that this message was shown by a mistake, please try using the following link to check if it works:<br>";
    echo '<a href="'.$downList['downloadLink'].'">'.$downList['downloadLink'].'</a>';
    die();
}

echo '<h1>Moved to <a href="'. $downList['downloadLink'] .'">here</a>';
header('Location: '. $downList['downloadLink']);
die();
?>
