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

$prodId = isset($_GET['id']) ? $_GET['id'] : '2';
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
$downUrl = "https://www.microsoft.com/{$translation['langCodeMs']}/api/controls/contentinclude/html?pageId=cfa9e580-a81e-4a4b-a846-7b21bf4e2e5b&host=www.microsoft.com&segments=software-download%2Cwindows10ISO&query=&action=GetProductDownloadLinksBySku&sessionId=$guid&sdVersion=2";

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

echo "<h3><span class=\"glyphicon glyphicon-th-list\" aria-hidden=\"true\"></span> $products</h3>\n";
?>

<div id="msContent" style="display: none;">
    <h4>
        <?php echo $translation['waitTitle']; ?>
    </h4>
    <p>
        <?php echo $translation['waitLangText']; ?>
    </p>
</div>

<div id="msContent2" style="display: none;"></div>

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
var msContent2 = document.getElementById('msContent2');
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
xhr.open("GET", "<?php echo $langsUrl; ?>", true);
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
    msContent2.innerHTML = "<h4><?php echo $translation['waitTitle']; ?></h4>" +
                           "<p><?php echo $translation['waitDlText']; ?></p>";

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
                    btn[i].classList.add("btn-default");
                }
            }

            var type = msContent2.querySelectorAll(".product-download-type");
            for(i = 0; i < type.length; i++) {
                type[i].innerHTML = type[i].innerHTML.replace(
                    /.*X86/i,
                    "<?php echo $translation['archx86']; ?>"
                );

                type[i].innerHTML = type[i].innerHTML.replace(
                    /.*X64/i,
                    "<?php echo $translation['archx64']; ?>"
                );
            }
        }
    };

    xhr.open(
        "GET",
        "<?php echo $downUrl; ?>&skuId=" + encodeURIComponent(id['id']) +
        "&language=" + encodeURIComponent(id['language']),
        true
    );

    xhr.withCredentials = true;
    xhr.send();
}
</script>

<?php
styleBottom();
