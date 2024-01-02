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
if(strpos($products, 'Language Pack')) {
    $s['langCodeMs'] = 'en-us';
}

$SessionID = SessionIDInit();
$langsUrl = "https://www.microsoft.com/{$s['langCodeMs']}/api/controls/contentinclude/html?pageId=cd06bda8-ff9c-4a6e-912a-b92a21f42526&host=www.microsoft.com&segments=software-download%2cwindows11&query=&action=getskuinformationbyproductedition&sessionId=$SessionID&productEditionId=$prodId&sdVersion=2";
$downUrl = "https://www.microsoft.com/{$s['langCodeMs']}/api/controls/contentinclude/html?pageId=cfa9e580-a81e-4a4b-a846-7b21bf4e2e5b&host=www.microsoft.com&segments=software-download%2Cwindows11&query=&action=GetProductDownloadLinksBySku&sessionId=$SessionID&sdVersion=2";

if(preg_match('/Windows.*?Insider.?Preview/', $products)) {
    $forceInsider = 1;
}

$top = '<h1>'.$s['tbDumpDownload']."</h1>\n";

if($forceInsider) {
    $top .= '<div class="alert alert-danger" style="margin-top: 1.5em">
    <h4><span class="glyphicon glyphicon glyphicon-warning-sign" aria-hidden="true"></span> '.$s['warning'].'</h4>
    <p>'.sprintf($s['insiderNotice'], 'https://www.microsoft.com/en-us/software-download/windowsinsiderpreviewiso').'</p>
</div>
';
}

styleTop('downloads');

echo <<<HTML
$top

<h3><span class="glyphicon glyphicon-th-list" aria-hidden="true"></span> $products</h3>

<div id="msContent" style="display: none;">
    <h4>
        {$s['waitTitle']}    </h4>
    <p>
        {$s['waitLangText']}    </p>
</div>

<div id="msContent2" style="display: none;"></div>

<noscript>
    <h4>
    {$s['warning']}    </h4>
    <p>
    {$s['jsRequired']}    </p>
</noscript>

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
        prodErr.style = "margin-top: 1em;";
        prodErr.style.display = "block";

        document.getElementById('submit-sku').setAttribute(
            "onClick",
            "getDownload()"
        );

        prodLang.setAttribute("onChange", "updateVars()");
        prodLang.classList.add("form-control");
        prodLang.style = "margin-top: 0.5em;";

        submitSku.classList.add("btn");
        submitSku.classList.add("btn-block");
        submitSku.classList.add("btn-primary");
        submitSku.style = "margin-top: 0.5rem;";

        updateVars();
    }
};
xhr.open('GET', '$langsUrl');
xhr.send();

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
                    "<span class=\"glyphicon glyphicon-download-alt\""+
                    "aria-hidden=\"true\"></span> $1"
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
        'GET',
        '$downUrl&skuId=' + encodeURIComponent(id['id']) +
        "&language=" + encodeURIComponent(id['language'])
    );

    xhr.withCredentials = true;
    xhr.send();
}
</script>
HTML;
?>
<?php styleBottom(); ?>
