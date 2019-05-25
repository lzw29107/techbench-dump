<?php
/*
Copyright 2019 whatever127

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

$prodId = isset($_GET['id']) ? $_GET['id'] : '52';
$fileName = isset($_GET['file']) ? $_GET['file'] : 'Win8.1_English_x64.iso';
$forceInsider = isset($_GET['insider']) ? $_GET['insider'] : false;

require 'lang/core.php';
require 'shared/style.php';
require 'shared/utils.php';

$out = @file_get_contents('dump.json');
if(empty($out)) {
    $out = array('products' => null);
} else {
    $out = json_decode($out, true);
}

$products = $out['products'];
if(empty($products[$prodId]))
{
    $products = $translation['unknownName'] .' ['.$translation['idName'].': '.$prodId.']';
} else {
    $products = $products[$prodId];
}

$guid = genUUID();
$langsUrl = "https://www.microsoft.com/{$translation['langCodeMs']}/api/controls/contentinclude/html?pageId=cd06bda8-ff9c-4a6e-912a-b92a21f42526&host=www.microsoft.com&segments=software-download%2cwindows10ISO&query=&action=getskuinformationbyproductedition&sessionId=$guid&productEditionId=$prodId&sdVersion=2";
$downUrl = "https://www.microsoft.com/en-us/api/controls/contentinclude/html?pageId=160bb813-f54e-4e9f-bffc-38c6eb56e061&host=www.microsoft.com&segments=software-download,dac&query=&action=GetProductDownloadLinkForFriendlyFileName&sessionId=$guid&friendlyFileName=".urlencode($fileName)."&sdVersion=2";

if(preg_match('/Windows.*?Insider.?Preview/', $products)) {
    $forceInsider = 1;
}

styleTop('downloads');

echo '<h1>'.$translation['tbDumpDownload']."</h1>\n";

if($forceInsider) {
    echo '<div class="alert alert-danger" style="margin-top: 1.5em">
    <h4><span class="glyphicon glyphicon glyphicon-warning-sign" aria-hidden="true"></span> '.$translation['warning'].'</h4>
    <p>'.$translation['insiderNotice'].'</p>
</div>'."\n";
}

echo "<h3><span class=\"glyphicon glyphicon-file\" aria-hidden=\"true\"></span> $fileName</h3>\n";
?>

<div id="msContent" style="display: none;">
    <h4>
        <?php echo $translation['waitTitle']; ?>
    </h4>
</div>

<div class="progress" id="progress">
    <div class="progress-bar progress-bar-striped active" id="progressBar"></div>
</div>

<div id="fileDownload" style="display: none;">
    <h4>
        <?php echo $translation['fileReady']; ?>
    </h4>
    <a id="downloadBtn" class="btn btn-primary btn-block btn-lg">
        <span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span>
        <?php echo $translation['downloadName']; ?>
    </a>
</div>

<noscript>
    <h4>
        <?php echo $translation['warning']; ?>
    </h4>
    <p>
        <?php echo $translation['jsRequired']; ?>
    </p>
</noscript>

<script>
var msContent = document.getElementById('msContent');
var fileDownload = document.getElementById('fileDownload');
var progressBar = document.getElementById('progressBar');
var progress = document.getElementById('progress');

msContent.style.display = "block";

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
    msContent.innerHTML = "<h4><?php echo $translation['waitTitle']; ?></h4>";

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
