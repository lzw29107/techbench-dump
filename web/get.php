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

$skuId = isset($_GET['skuId']) ? $_GET['skuId'] : '6PC-00020';
$sessionId = isset($_GET['sessionId']) ? $_GET['sessionId'] : 'lol';
$prodId = isset($_GET['id']) ? $_GET['id'] : '2';
$lang = isset($_GET['lang']) ? $_GET['lang'] : 'en-us';

$requestScheme = (isset($_SERVER['HTTPS'])) ? 'https' : 'http';
$baseDir = preg_replace('/\/$|\\\$/', '', dirname($_SERVER['REQUEST_URI']));

$srvPort = $_SERVER['SERVER_PORT'];
$portString = ($srvPort == 80 || $srvPort == 443) ? '' : ':'.$srvPort;

$baseUrl=$requestScheme.'://'.$_SERVER['SERVER_NAME'].$portString.$baseDir.'/';

require 'lang/core.php';
require 'shared/get.php';
require 'shared/style.php';

$downList = getDownload($skuId, $sessionId);
if(isset($downList['error'])) {
    echo 'There was an error processing your request.';
    die();
}

styleTop('downloads');

echo '<h1>'.$translation['tbDumpDownload']."</h1>\n";
echo "<h3><span class=\"glyphicon glyphicon-file\" aria-hidden=\"true\"></span> ".$downList['osName']."</h3>\n";
$index = 0;
foreach ($downList['downloadLinks'] as &$curr) {
    if ($index == 0) {
        $btnType = 'btn-primary';
    } else {
        $btnType = 'btn-default';
    }
    switch ($curr['architecture']) {
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
    echo '<a class="btn '.$btnType.'" href="'.$curr['url'].'"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> '.$btnText."</a>\n";
    $index++;
}
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
        foreach ($downList['downloadLinks'] as &$iso) {
            echo "{$baseUrl}getDirect.php?fileName=".$iso['fileName'].'&id='.$prodId."\n";
        }
    ?></code></pre>
</div>

<?php styleBottom(); ?>
