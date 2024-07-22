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

require_once 'shared/utils.php';
require_once 'shared/lang.php';
require_once 'shared/dump.php';
require_once 'shared/style.php';

$config = getConfig();

if($_SERVER['REQUEST_METHOD'] == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/x-www-form-urlencoded') {
    if(isset($_POST['Progress'])) {
        if(is_file('dump.json.lock')) echo file_get_contents('dump.json.lock');
        exit();
    } else if(isset($_POST['startDump'])) {
        execBackground($config['php'], 'dump.php update');
        exit();
    } else if(isset($_POST['Info'])) {
        if(is_file('dump.json')) {
            $dump = json_decode(file_get_contents('dump.json'), true);
            echo json_encode([
                'ProductNumber' => count($dump['ProdInfo']),
                'LastUpdateTime' => date("Y-m-d H:i:s T", $dump['TechInfo']['LastUpdateTime'])
            ]);
        }
        exit();
    } else if(isset($_POST['recheck'])) {
        if($_POST['reCheck'] == 'basic') {
            execBackground($config['php'], 'dump.php recheck');
            exit();
        }
        if(is_file('dump.json')) {
            $dump = json_decode(file_get_contents('dump.json'), true);
            $checkList = [];
            switch($_POST['recheck']) {
                case 'WIP':
                    foreach($dump['ProdInfo'] as $productId => $product) if($product['Status'] != 'Unavailable' && in_array('WIP', $product['Category'])) $checkList[] = $productId;
                    break;
                case 'Xbox':
                    foreach($dump['ProdInfo'] as $productId => $product) if($product['Status'] != 'Unavailable' && in_array('Xbox', $product['Category'])) $checkList[] = $productId;
                    break;
                default:
                    exit();
            }
            echo json_encode($checkList);
        }
        exit();
    } else if(isset($_POST['NewSession'])) {
        exit(genSessionId());
    }
} else if($_SERVER['REQUEST_METHOD'] == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json') {
    $json = file_get_contents('php://input');
    if(strpos($json, 'Available') == false) exit();
    $infos = json_decode($json, true);
    $info = array_pop($infos);
    if($info['type'] == 'reCheck' && $info['status'] == 'result') {
        if(is_file('dump.json')) {
            $dump = json_decode('dump.json');
            $ProductNumber = count($dump['ProdInfo']);
            foreach($infos as $info) {
                $productId = $info['ID'];
                $status = $info['Status'];
                $arch = $info['Arch'];
                $dump['ProdInfo'][$ProductId]['Status'] = $status;
                if($status != 'Unavailable' && $arch != ['Unknown'] && ($arch != ['neutral'] || $dump['ProdInfo'][$productId]['Status'] == 'Unknown')) $dump['ProdInfo'][$ProductId]['Arch'] = $arch;
            }
            if(!is_file('dump.bak')) copy('dump.json', 'dump.bak');
            file_put_contents('dump.json', json_encode($dump, JSON_PRETTY_PRINT));
            exit();
        }
    }
}

$notice = 'Normal';
if(is_file('dump.json')) {
    $dump = json_decode(file_get_contents('dump.json'), true);
    $productNumber = count($dump['ProdInfo']);
    $lastUpdateTime = date("Y-m-d H:i:s T", $dump['TechInfo']['LastUpdateTime']);
} else {
    $productNumber = 0;
    $lastUpdateTime = '';
    $notice = 'File not exist';
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
    <p class="mb-0"> {$s['lastUpdate']}: <b> $lastUpdateTime</b><br>
    {$s['productsNumber']}: <b>$productNumber</b></p>
    <p><span>{$s['status']}: </span><span>$notice</span></p>
</div>

<div class="card text-bg">
    <div class="card-body pb-1">
        <div id="progress" class="progress" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="display: none;">
            <div class="progress-bar progress-bar-striped progress-bar-animated bg-info text-dark overflow-visible" style="width: 0%"></div>
            <p class="position-absolute" style="width: calc(50% + 1rem); right: 0">0.00%</p>
        </div>
        <p class="text-center my-3" id="count" style="display: none;"></p>
        <button type="button" id="dumpBtn" class="btn btn-primary mb-3" disabled>Check for Updates</button>
        <button type="button" id="checkBtn" class="btn btn-info mb-3" disabled>Recheck Status</button>
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
var lastUpd = info.getElementsByTagName('b').item(0);
var prodNum = info.getElementsByTagName('b').item(1);

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
            if (xhr.responseText != '') var state = JSON.parse(xhr.responseText);
            if ((progress.getAttribute('aria-valuenow') > 0 && typeof state === 'undefined') || typeof state !== 'undefined' && (state.progress == '100.00' || state.status == 'Successful')) {
                clearInterval(intervalId);
                updInfo();
            } else if (typeof state !== 'undefined' && (state.status == 'Exception' || state.status == 'Error')) {
                clearInterval(intervalId);
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

function updInfo() {
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            var infoJson = JSON.parse(xhr.responseText);
            info.getElementsByTagName('span').item(1).innerText = 'Latest';
            progress.setAttribute('aria-valuenow', '100');
            progressbar.style.width = '100.00%';
            progresstext.innerText = '100.00%';
            progresstext.style.width = 'calc(50% + 1.5rem)';
            dumpBtn.disabled = false;
            count.innerText = max + ' / ' + max;
            lastUpd.innerText = ' ' + infoJson.LastUpdateTime;
            prodNum.innerText = infoJson.ProductNumber;
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
                  intervalId = setInterval(checkProgress, 500);
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
        if (typeof state === 'undefined' || state.status == 'Success' || new Date().getTime() / 1000 - state.time > 600) {
            dumpBtn.disabled = false;
            checkBtn.disabled = false;
        } else {
            intervalId = setInterval(checkProgress, 500);
        }
    }
}
</script>
HTML;?>
<?php styleBottom(); ?>
