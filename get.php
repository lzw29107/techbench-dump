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

$prodId = isset($_GET['id']) ? $_GET['id'] : '0';
$forceInsider = isset($_GET['insider']) ? $_GET['insider'] : false;

require_once 'shared/lang.php';
require_once 'shared/utils.php';
require_once 'shared/style.php';

$config = get_config();

if(is_file('dump.json')) {
    $dump = json_decode(file_get_contents('dump.json'), true);
    $ProdItem = $dump['ProdInfo'][$prodId];
    if($config['autoupd'] && $config['php'] && time() - $dump['TechInfo']['LastCheckUpdateTime'] >= 3600) exec_background($config['php'], 'dump.php update');
}

$select = true;

$ProductName = isset($ProdItem) ? $ProdItem['Name'] : $s['unknownName'];

if(strpos($ProductName, 'Language Pack') !== false) $s['langCodeMs'] = 'en-us';

$SessionID = SessionIDInit();
$langsUrl = "https://www.microsoft.com/{$s['langCodeMs']}/api/controls/contentinclude/html?pageId=cd06bda8-ff9c-4a6e-912a-b92a21f42526&host=www.microsoft.com&segments=software-download%2cwindows11&query=&action=getskuinformationbyproductedition&sessionId=$SessionID&productEditionId=$prodId&sdVersion=2";
$downUrl = "https://www.microsoft.com/{$s['langCodeMs']}/api/controls/contentinclude/html?pageId=cfa9e580-a81e-4a4b-a846-7b21bf4e2e5b&host=www.microsoft.com&segments=software-download%2Cwindows11&query=&action=GetProductDownloadLinksBySku&sessionId=$SessionID&sdVersion=2";

if(strpos($ProductName, 'Build')) $forceInsider = true;

$Notice = $forceInsider ? '<div class="alert alert-danger mt-4 pb-1">
<h4><i class="bi bi-exclamation-triangle"></i> '.$s['warning'].'</h4>
<p>'.sprintf($s['insiderNotice'], '<a class="link-underline link-underline-opacity-0" href="https://www.microsoft.com/en-us/software-download/windowsinsiderpreviewiso').'</p>
</div>' : '';

styleTop('downloads');

echo <<<HTML
<div class="mt-5 mb-4">
    <h1 class="fs-3">{$s['tbDumpDownload']}</h1>
</div>

$Notice

<h3 class="fs-4">
    <i class="bi bi-list-ul"></i>
    $ProductName [{$s['idName']}: $prodId]
</h3>
<div class="row">
    <div class="col">
        <div id="msContent" class="mt-3" style="display: none;">
            <h4 class="fs-5">
                {$s['waitTitle']}    </h4>
            <p>
                {$s['waitLangText']}    </p>
        </div>

        <noscript>
            <h4>
            {$s['warning']}
            </h4>
            <p>
            {$s['jsRequired']}
            </p>
        </noscript>
    </div>
    <div class="col">
        <div id="msContent2" class="mt-2" style="display: none;"></div>
    </div>
</div>
<script>
var msContent = document.getElementById('msContent');
var msContent2 = document.getElementById('msContent2');
msContent.style.display = "block";

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

            return;
        }

        var prodLang = document.getElementById('product-languages');
        var submitSku = document.getElementById('submit-sku');
        var prodErr = document.getElementById('product-languages-error');
        var placeholder = prodLang.options[prodLang.selectedIndex].text;
  
        prodErr.classList.add("mt-3");
        prodErr.classList.add("mb-2");
        prodErr.style.display = "block";

        document.getElementById('submit-sku').setAttribute(
            "onClick",
            "getDownload()"
        );

        prodLang.setAttribute("onChange", "updateVars()");
        prodLang.classList.add("form-select");

        submitSku.classList.add("btn");
        submitSku.classList.add("d-grid");
        submitSku.classList.add("btn-primary");
        submitSku.classList.add("my-2");

        $("select").select2({
            theme: "bootstrap-5",
            minimumResultsForSearch: Infinity,
            placeholder: placeholder
            });
        updateVars();
    }
};
xhr.open('POST', '$langsUrl');
xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded; charset=UTF-8");
xhr.withCredentials = true;
xhr.send('controlAttributeMapping=');

function updateVars() {
    var id = document.getElementById('product-languages').value;
    if(id == "") {
        document.getElementById('submit-sku').disabled = 1;
        return;
    }

    id = JSON.parse(id);
    document.getElementById('submit-sku').disabled = 0;

    return id;
}

function getDownload() {
    msContent2.style.display = "block";
    msContent2.innerHTML = "<h4>{$s['waitTitle']}</h4>" +
                           "<p>{$s['waitDlText']}</p>";

    id = updateVars();
    var xhr = new XMLHttpRequest();

    xhr.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            msContent2.innerHTML = this.responseText;

            var errorMessage = document.getElementById('errorModalMessage');

            if(errorMessage) {
                var errorTitle = document.getElementById('errorModalTitle');
                msContent2.innerHTML = "<h4>" + errorTitle.innerHTML +
                                       "</h4><p>" + errorMessage.innerHTML +
                                       "</p>";

                return;
            }

            var btn = msContent2.querySelectorAll(".button");
            for(i = 0; i < btn.length; i++) {
                btn[i].innerHTML = btn[i].innerHTML.replace(
                    /.*(<span.*\/span>).*/i,
                    "<i class=\"bi bi-download\"></i> $1"
                );

                btn[i].classList.add("btn");
                if(i == 0) {
                    btn[i].classList.add("btn-primary");
                } else {
                    btn[i].classList.add("btn-info");
                }
            }

            var type = msContent2.querySelectorAll(".product-download-type");
            for(i = 0; i < type.length; i++) {
                type[i].innerHTML = type[i].innerHTML.replace(
                    /.*X86/i,
                    "{$s['archx86']}"
                );

                type[i].innerHTML = type[i].innerHTML.replace(
                    /.*X64/i,
                    "{$s['archx64']}"
                );

                type[i].innerHTML = type[i].innerHTML.replace(
                    /.*Unknown/i,
                    "{$s['downloadName']}"
                );
            }
        }
    };

    xhr.open(
        'POST',
        '$downUrl&skuId=' + encodeURIComponent(id['id']) +
        "&language=" + encodeURIComponent(id['language'])
    );
xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded; charset=UTF-8");
xhr.withCredentials = true;
xhr.send('controlAttributeMapping=');
}
</script>
HTML;
?>
<?php styleBottom(); ?>
