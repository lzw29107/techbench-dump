<?php
/*
TechBench dump
Copyright (C) 2023 TechBench dump website authors and contributors

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

require_once 'shared/utils.php';
require_once 'shared/lang.php';
require_once 'shared/dump.php';
require_once 'shared/style.php';

$config = get_config();

if($_SERVER['REQUEST_METHOD'] == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/x-www-form-urlencoded') {
    if(isset($_POST['Progress'])) {
        if(is_file('dump.xml.lock')) {
            echo file_get_contents('dump.xml.lock');
        }
        exit();
    } else if(isset($_POST['startDump'])) {
        exec_background($config['php'], 'dump.php update');
        exit();
    } else if(isset($_POST['Info'])) {
        if(is_file('dump.xml')) {
            $dom = new DOMDocument('1.0', 'UTF-8');
            @$dom->load('dump.xml');
            if(libxml_get_last_error()) {
                usleep(10000);
                @$dom->load('dump.xml');
            }
            if(libxml_get_last_error()) exit();
            $Tech = $dom->getElementsByTagName('TechInfo')->item(0);
            $Prod = $dom->getElementsByTagName('ProdInfo')->item(0);
            $Info = array();
            $Info['ProductNumber'] = $Prod->childElementCount;
            $Info['LastUpdateTime'] = date("Y-m-d H:i:s T", $Tech->getAttribute('LastUpdateTime'));
            echo json_encode($Info);
        }
        exit();
    } else if(isset($_POST['reCheck'])) {
        if($_POST['reCheck'] == 'basic') {
            exec_background($config['php'], 'dump.php recheck');
            exit();
        }
        if(is_file('dump.xml')) {
            $dom = new DOMDocument('1.0', 'UTF-8');
            @$dom->load('dump.xml');
            if(libxml_get_last_error()) {
                usleep(10000);
                @$dom->load('dump.xml');
            }
            if(libxml_get_last_error()) exit();
            $Prod = $dom->getElementsByTagName('ProdInfo')->item(0);
            $xpath = new DOMXPath($dom);
            $Unknown = array();
            switch($_POST['reCheck']) {
                case 'WIP':
                    foreach($xpath->query("./*[@Validity!='Invalid' and contains(@Category,'WIP')]", $Prod) as $prod) $Unknown[] = $prod->getAttribute('ID');
                    break;
                case 'Xbox':
                    foreach($xpath->query("./*[@Validity!='Invalid' and contains(@Category,'Xbox')]", $Prod) as $prod) $Unknown[] = $prod->getAttribute('ID');
                    break;
                default:
                    exit();
            }
            echo json_encode($Unknown);
        }
        exit();
    } else if(isset($_POST['NewSession'])) {
        echo SessionIDInit();
        exit();
    }
} else if($_SERVER['REQUEST_METHOD'] == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json') {
    $json = file_get_contents('php://input');
    if(strpos($json, 'Valid') == false) exit();
    $Info = json_decode($json, true);
    $info = array_pop($Info);
    if($info['type'] == 'reCheck' && $info['status'] == 'result') {
        if(is_file('dump.xml')) {
            $dom = new DOMDocument('1.0', 'UTF-8');
            @$dom->load('dump.xml');
            if(libxml_get_last_error()) {
                sleep(0.1);
                @$dom->load('dump.xml');
            }
            if(libxml_get_last_error()) exit();
            $Prod = $dom->getElementsByTagName('ProdInfo')->item(0);
            $ProductNumber = $Prod->childElementCount;
            $xpath = new DOMXPath($dom);
            foreach($Info as $info) {
                $ProductID = $info['ID'];
                $Validity = $info['Validity'];
                $Arch = $info['Arch'];
                $prod = $xpath->query("./*[@ID=$ProductID]", $Prod);
                $prod->item(0)->setAttribute('Validity', $Validity);
                if($Validity != 'Invalid' && $Arch != 'Unknown') {
                    if($Arch != 'neutral' || $prod->item(0)->getAttribute('Validity') == 'Unknown') $prod->item(0)->setAttribute('Arch', $Arch);
                }
            }
            if(!is_file('dump.bak')) copy('dump.xml', 'dump.bak');
            $dom->save('dump.xml');
            indentContent('dump.xml');
            exit();
        }
    }
}

$Notice = 'Normal';
if(is_file('dump.xml')) {
    $dom = new DOMDocument('1.0', 'UTF-8');
    @$dom->load('dump.xml');
    if(libxml_get_last_error()) {
        sleep(0.1);
        @$dom->load('dump.xml');
    }
    if(libxml_get_last_error()) $Notice = 'Load Error';
    $Tech = $dom->getElementsByTagName('TechInfo')->item(0);
    $Prod = $dom->getElementsByTagName('ProdInfo')->item(0);
    $ProductNumber = $Prod->childElementCount;
    $LastUpdateTime = date("Y-m-d H:i:s T", $Tech->getAttribute('LastUpdateTime'));
} else {
    $ProductNumber = '';
    $LastUpdateTime = '';
    $Notice = 'File not exist';
}

styleTop();

echo <<<HTML
<script defer="defer" src="js/update.js"></script>
<div class="my-5 text-center">
    <h1 class="fw-bold">{$s['tbDump']} 
        <span class="badge rounded-pill bg-primary position-absolute">v$websiteVersion</span>
    </h1>
</div>

<div class="alert alert-info mt-4" id="info">
    <h4><i class="bi bi-info"></i> {$s['techInfo']}</h4>
    <p class="mb-0"> {$s['lastUpdate']}: <b> $LastUpdateTime</b><br>
    {$s['productsNumber']}: <b>$ProductNumber</b></p>
    <p><span>{$s['status']}</span><span>$Notice</span></p>
</div>

<div class="card text-bg-light border-light">
    <div class="card-body pb-1">
        <div id="progress" class="progress" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="display: none;">
            <div class="progress-bar progress-bar-striped progress-bar-animated bg-info text-dark overflow-visible" style="width: 0%"></div>
            <p class="position-absolute" style="width: calc(50% + 1rem); right: 0">0.00%</p>
        </div>
        <p class="text-center my-3" id="count" style="display: none;"></p>
        <button type="button" id="dumpBtn" class="btn btn-primary mb-3" disabled>Ckeck for Updates</button>
        <button type="button" id="checkBtn" class="btn btn-info mb-3" disabled>Recheck Validity</button>
    </div>
</div>

<script>
var info = document.getElementById('info');
var dumpBtn = document.getElementById('dumpBtn');
var checkBtn = document.getElementById('checkBtn');
var progress = document.getElementById('progress');
var progressbar = progress.getElementsByTagName('div').item(0);
var progresstext = progress.getElementsByTagName('p').item(0);
var count = document.getElementById('count');
var LastUpd = info.getElementsByTagName('b').item(0);
var ProdNum = info.getElementsByTagName('b').item(1);

function checkProgress() {
    progress.style.display = '';
    count.style.display = '';
    info.getElementsByTagName('span').item(1).innerText = '{$s['checking']}';
    var xhr = new XMLHttpRequest();
    xhr.open('POST',window.location.href);
    xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
    xhr.send('Progress=1');
    xhr.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            if(xhr.responseText != '') var state = JSON.parse(xhr.responseText);
            if((progress.getAttribute('aria-valuenow') > 0 && typeof state === 'undefined') || state.progress == '100.00' || state.status == 'Successful') {
                clearInterval(IntervalID);
                UpdInfo();
            } else if (state.status == 'Exception' || state.status == 'Error') {
                clearInterval(IntervalID);
                info.getElementsByTagName('span').item(1).innerText = '{$s['error']}';
                progressbar.classList.add("bg-warning");
                progressbar.classList.remove("progress-bar-animated");
            } else if (typeof state !== 'undefined' && state.progress != '0.00' && !isNaN(state.progress)) {
                if(state.status == 'recheck') progressbar.classList.add("bg-info");
                progress.setAttribute('aria-valuenow', state.progress);
                progressbar.style.width = state.progress + '%';
                progresstext.innerText = state.progress + '%';
                if(state.progress >= 10.00) {
                    progresstext.style.width = 'calc(50% + 1.25rem)';
                }
                count.innerText = state.current + ' / ' + state.total;
                max = state.total;
            }
        }
    }
}

function UpdInfo() {
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            var Info = JSON.parse(xhr.responseText);
            info.getElementsByTagName('span').item(1).innerText = 'Latest';
            progress.setAttribute('aria-valuenow', '100');
            progressbar.style.width = '100.00%';
            progresstext.innerText = '100.00%';
            progresstext.style.width = 'calc(50% + 1.5rem)';
            dumpBtn.disabled = false;
            count.innerText = max + ' / ' + max;
            LastUpd.innerText = ' ' + Info.LastUpdateTime;
            ProdNum.innerText = Info.ProductNumber;
        }
    }
    xhr.open('POST',window.location.href);
    xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
    xhr.send('Info=1');
}

dumpBtn.onclick = function () {
      dumpBtn.disabled = true;
      checkBtn.disabled = true;
      var xhr = new XMLHttpRequest();
      xhr.onreadystatechange = function () {
          if (this.readyState == 4) {
              if(this.status == 200) {
                  IntervalID = setInterval(checkProgress, 500);
              } else {
                  dumpBtn.disabled = false;
                  checkBtn.disabled = false;
              }
          }
      }
      xhr.open('POST',window.location.href);
      xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
      xhr.send('startDump=1');
  };

var xhr = new XMLHttpRequest();
xhr.open('POST',window.location.href);
xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
xhr.send('Progress=1');
xhr.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
        if(xhr.responseText != '') var state = JSON.parse(xhr.responseText);
        if (typeof state === 'undefined' || state.status == 'Success') {
            dumpBtn.disabled = false;
            checkBtn.disabled = false;
        } else {
            IntervalID = setInterval(checkProgress, 500);
        }
    }
}
</script>
HTML;?>
<?php styleBottom(); ?>
