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
$lang = isset($_GET['lang']) ? $_GET['lang'] : 'en-us';
$base_url=$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['REQUEST_URI'].'?').'/';
require 'lang/core.php';
require 'shared/get.php';
require 'shared/style.php';

$downList = getDownloadByName($fileName);
if(isset($downList['error'])) {
    echo 'There was an error processing your request.';
    die();
}

switch ($downList['architecture']) {
case 'x64':
    $btnText = $translation['archx64'];
    break;
case 'x86':
    $btnText = $translation['archx86'];
    break;
default:
    $btnText = $translation['downloadName'];
    break;
}

styleTop('downloads');

echo '<h1>'.$translation['tbDumpDownload']."</h1>\n";
echo "<h3><span class=\"glyphicon glyphicon-file\" aria-hidden=\"true\"></span> ".$downList['fileName']."</h3>\n";
echo '<a class="btn btn-primary" href="'.$downList['downloadLink'].'"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> '.$btnText."</a>\n";
?> 

<div class="alert alert-success" style="margin-top: 1.5em">
    <h4><span class="glyphicon glyphicon-time" aria-hidden="true"></span> <?php echo $translation['linkExpireTitle'];?></h4>
    <p><?php echo $translation['linkExpire1'];?><br>
    <?php echo $translation['linkExpire2'].': <b>'.date("Y-m-d H:i:s T", $downList['expiration']); ?></b></p>
</div>

<div class="alert alert-info" style="margin-top: 1.5em">
    <h4><span class="glyphicon glyphicon-link" aria-hidden="true"></span> <?php echo $translation['directLinksTitle'];?></h4>
    <p><?php echo $translation['directLinksLine1'];?></p>
    <pre style="margin-top: 1em"><code><?php 
        echo "{$base_url}getDirect.php?fileName=".$downList['fileName']."\n";
    ?></code></pre>
</div>

<?php styleBottom(); ?>
