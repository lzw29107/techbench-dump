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

$config = getConfig();

if(is_file('dump.json')) {
    $dump = json_decode(file_get_contents('dump.json'), true);
    if($prodId) $prodItem = $dump['ProdInfo'][$prodId];
    if($config['autoupd'] && $config['php'] && time() - $dump['TechInfo']['LastCheckUpdateTime'] >= 3600) execBackground($config['php'], 'dump.php update --quiet');
}

$productName = $s['unknownName'];

if(isset($prodItem)) {
    $productName = $prodItem['Name'];
    if(in_array('LP', $prodItem['Category'])) $s['langCodeMs'] = 'en-us';
    if(in_array('WIP', $prodItem['Category'])) $forceInsider = true;
}

$notice = $forceInsider ? '<div class="alert alert-danger mt-4 pb-1">
<h4><i class="bi bi-exclamation-triangle"></i> '.$s['warning'].'</h4>
<p>'.sprintf($s['insiderNotice'], '<a class="link-underline link-underline-opacity-0" href="https://www.microsoft.com/en-us/software-download/windowsinsiderpreviewiso">').'</p>
</div>' : '';

$sessionId = genSessionId();
if($prodId) $langsUrl = "https://www.microsoft.com/{$s['langCodeMs']}/api/controls/contentinclude/html?pageId=cd06bda8-ff9c-4a6e-912a-b92a21f42526&host=www.microsoft.com&segments=software-download%2cwindows11&query=&action=getskuinformationbyproductedition&sessionId=$sessionId&productEditionId=$prodId&sdVersion=2";
if($fileName) $downUrl = "https://www.microsoft.com/en-us/api/controls/contentinclude/html?pageId=160bb813-f54e-4e9f-bffc-38c6eb56e061&host=www.microsoft.com&segments=software-download%2cwindows11&query=&action=GetProductDownloadLinkForFriendlyFileName&sessionId=$sessionId&friendlyFileName=".urlencode($fileName)."&sdVersion=2";

styleTop('downloads');

if($prodId == null || $fileName == null) {
    echo <<<EOD
<div class="mt-5 mb-4">
    <h1 class="fs-3">{$s['tbDumpDownload']}</h1>
</div>

<form>
    <div class="mb-3">
    <label for="prodId" class="form-label">{$s['productId']}</label>
    <input type="text" class="form-control" id="prodId" name="id" placeholder="{$s['idName']}">
    </div>
    <div class="mb-3">
        <label for="file" class="form-label">{$s['fileName']}</label>
        <input type="text" class="form-control" id="file" name="file" placeholder="{$s['fileName']}">
    </div>
    <button type="submit" class="btn btn-primary d-grid">OK</button>
</form>
EOD;
    styleBottom();
    exit();
}

echo <<<HTML
<div class="mt-5 mb-4">
    <h1 class="fs-3">{$s['tbDumpDownload']}</h1>
</div>

$notice

<h3><i class="bi bi-file-earmark"></i> $fileName</h3>

<div id="msContent" style="display: none;">
    <h4>
                {$s['waitTitle']}    </h4>
    </h4>
</div>

<div class="progress" id="progress" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="display: none;">
  <div class="progress-bar progress-bar-striped progress-bar-animated" id="progressBar" style="width: 0%"></div>
</div>

<div id="fileDownload" style="display: none;">
    <h4>
        {$s['fileReady']}
    </h4>
    <a id="downloadBtn" class="btn btn-primary d-grid">
        <p class="mb-0"><i class="bi bi-download"></i>
        {$s['downloadName']}</p>
    </a>
</div>

<noscript>
    <h4>
            {$s['warning']}
    </h4>
    <p>
            {$s['jsRequired']}
    </p>
</noscript>

<script>
var msContent = document.getElementById('msContent');
var fileDownload = document.getElementById('fileDownload');
var progressBar = document.getElementById('progressBar');
var progress = document.getElementById('progress');

msContent.style.display = "block";
progress.style.display = '';

var xhr = new XMLHttpRequest();
xhr.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
        msContent.innerHTML = this.responseText;

        var bottom = document.getElementsByClassName('row-padded-bottom row-fluid')[0];
        if(typeof bottom !== 'undefined') {
            bottom.style.display = "none";
        }

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
	progress.setAttribute('aria-valuenow', 50);
        getDownload();
    }
};

xhr.open('POST', '$langsUrl');

xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded; charset=UTF-8");
xhr.withCredentials = true;
xhr.send('controlAttributeMapping=');


function getDownload() {
    msContent.style.display = "block";
    msContent.innerHTML = "<h4>{$s['waitTitle']}</h4>";

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
	    progress.setAttribute('aria-valuenow', 100);

            document.getElementById('downloadBtn').href = encodeURI(url);

            fileDownload.style.display = "block";
            msContent.style.display = "none";
            progress.style.display = "none";

            window.location.href = url;
        }
    };

    xhr.open('POST', '$downUrl');
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded; charset=UTF-8");
    xhr.withCredentials = true;
    xhr.send('controlAttributeMapping=');
}
</script>
HTML;
?>
<?php styleBottom(); ?>