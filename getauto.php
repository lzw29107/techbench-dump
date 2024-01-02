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

$prodId = isset($_GET['id']) ? $_GET['id'] : null;
$fileName = isset($_GET['file']) ? $_GET['file'] : null;
$forceInsider = isset($_GET['insider']) ? $_GET['insider'] : false;

require_once 'shared/lang.php';
require_once 'shared/style.php';
require_once 'shared/utils.php';

$out = @file_get_contents('dump.json');
if(empty($out)) {
    $out = array('products' => null);
} else {
    $out = json_decode($out, true);
}

$products = $out['products'];
if(empty($products[$prodId]))
{
    $products = $s['unknownName'] .' ['.$s['idName'].': '.$prodId.']';
} else {
    $products = $products[$prodId];
}

if(preg_match('/Windows.*?Insider.?Preview/', $products)) {
    $forceInsider = 1;
}

$SessionID = SessionIDInit();
if($prodId) $langsUrl = "https://www.microsoft.com/{$s['langCodeMs']}/api/controls/contentinclude/html?pageId=cd06bda8-ff9c-4a6e-912a-b92a21f42526&host=www.microsoft.com&segments=software-download%2cwindows11&query=&action=getskuinformationbyproductedition&sessionId=$SessionID&productEditionId=$prodId&sdVersion=2";
if($fileName) $downUrl = "https://www.microsoft.com/en-us/api/controls/contentinclude/html?pageId=160bb813-f54e-4e9f-bffc-38c6eb56e061&host=www.microsoft.com&segments=software-download%2cwindows11&query=&action=GetProductDownloadLinkForFriendlyFileName&sessionId=$SessionID&friendlyFileName=".urlencode($fileName)."&sdVersion=2";

styleTop('downloads');

echo '<h1>'.$s['tbDumpDownload']."</h1>\n";

if($prodId == null || $fileName == null) {
    echo <<<EOD
<form>
    <div class="form-group">
        <label>Product ID</label>
        <input type="text" class="form-control" placeholder="ID" name="id">
    </div>
    <div class="form-group">
        <label>Filename</label>
        <input type="text" class="form-control" placeholder="Filename" name="file">
    </div>
    <button type="submit" class="btn btn-primary btn-block">OK</button>
</form>
EOD;
    styleBottom();
    exit();
}

if($forceInsider) {
    echo '<div class="alert alert-danger" style="margin-top: 1.5em">
    <h4><span class="glyphicon glyphicon glyphicon-warning-sign" aria-hidden="true"></span> '.$s['warning'].'</h4>
    <p>'.sprintf($s['insiderNotice'], 'https://www.microsoft.com/en-us/software-download/windowsinsiderpreviewiso').'</p>
</div>
';
}

echo "<h3><span class=\"glyphicon glyphicon-file\" aria-hidden=\"true\"></span> $fileName</h3>\n";
?>

<div id="msContent" style="display: none;">
    <h4>
        <?php echo $s['waitTitle']; ?>
    </h4>
</div>

<div class="progress" id="progress" style="display: none;">
    <div class="progress-bar progress-bar-striped active" id="progressBar"></div>
</div>

<div id="fileDownload" style="display: none;">
    <h4>
        <?php echo $s['fileReady']; ?>
    </h4>
    <a id="downloadBtn" class="btn btn-primary btn-block btn-lg">
        <span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span>
        <?php echo $s['downloadName']; ?>
    </a>
</div>

<noscript>
    <h4>
        <?php echo $s['warning']; ?>
    </h4>
    <p>
        <?php echo $s['jsRequired']; ?>
    </p>
</noscript>

<script>
var msContent = document.getElementById('msContent');
var fileDownload = document.getElementById('fileDownload');
var progressBar = document.getElementById('progressBar');
var progress = document.getElementById('progress');

msContent.style.display = "block";
progress.style.display = "block";

var xhr = new XMLHttpRequest();
xhr.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
        msContent.innerHTML = this.responseText;

        var errorMessage = document.getElementById('errorModalMessage');

        if(errorMessage) {
            var errorTitle = document.getElementById('errorModalTitle');
            msContent.innerHTML = "<h4>" + errorTitle.innerHTML +
                                  "</h4><p>" + errorMessage.innerHTML +
                                  "</p>";

            progress.style.display = "none";
            return;
        }

        progressBar.style.width = "50%";
        getDownload();
    }
};

xhr.open("GET", "<?php echo $langsUrl; ?>", true);
xhr.send();

function getDownload() {
    msContent.style.display = "block";
    msContent.innerHTML = "<h4><?php echo $s['waitTitle']; ?></h4>";

    var xhr = new XMLHttpRequest();

    xhr.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            msContent.innerHTML = this.responseText;

            var errorMessage = document.getElementById('errorModalMessage');

            if(errorMessage) {
                var errorTitle = document.getElementById('errorModalTitle');
                msContent.innerHTML = "<h4>" + errorTitle.innerHTML +
                                       "</h4><p>" + errorMessage.innerHTML +
                                       "</p>";

                progress.style.display = "none";
                return;
            }

            var msScript = msContent.innerHTML.match(
                /\/\*<!\[CDATA\[\*\/.*\/\*\]\]>\*\//i,
            );

            eval(msScript[0]);
            var url = softwareDownload.productDownload.uri;
            progressBar.style.width = "100%";

            document.getElementById('downloadBtn').href = encodeURI(url);

            fileDownload.style.display = "block";
            msContent.style.display = "none";
            progress.style.display = "none";

            window.location.href = url;
        }
    };

    xhr.open(
        "GET",
        "<?php echo $downUrl; ?>",
        true
    );

    xhr.withCredentials = true;
    xhr.send();
}
</script>

<?php
styleBottom();
