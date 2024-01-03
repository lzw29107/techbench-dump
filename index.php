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

require_once 'shared/lang.php';
require_once 'shared/style.php';


if(is_file('dump.xml')) {
    $dom = new DOMDocument('1.0', 'UTF-8');
    @$dom->load('dump.xml');
    if(libxml_get_last_error()) {
        usleep(10000);
        @$dom->load('dump.xml');
    }
    if(libxml_get_last_error()) exit('XML Load Error');
    $Tech = $dom->getElementsByTagName('TechInfo')->item(0);
    $Prod = $dom->getElementsByTagName('ProdInfo')->item(0);
    $ProductNumber = $Prod->childElementCount;
    $LastUpdateTime = date("Y-m-d H:i:s T", $Tech->getAttribute('LastUpdateTime'));
    if(time() - $Tech->getAttribute('LastCheckUpdateTime') >= 3600) popen('php dump.php update &', 'r');
} else {
    $LastUpdateTime = '';
    $ProductNumber = 0;
}

styleTop('home');

echo <<<HTML
<div class="my-5 text-center">
    <h1 class="fw-bold">{$s['tbDump']} 
        <span class="badge rounded-pill bg-primary position-absolute">v$websiteVersion</span>
    </h1>
</div>

<div class="alert alert-info mt-4">
    <h4><i class="bi bi-info"></i> {$s['techInfo']}</h4>
    <p class="mb-0"> {$s['lastUpdate']}: <b> $LastUpdateTime</b><br>
    {$s['productsNumber']}: <b>$ProductNumber</b></p>
</div>

<div class="card text-bg-light border-light">
    <div class="card-body pb-1">
        <form action="./products.php">
            <div class="input-group">
                <input type="text" class="form-control input-lg" name="search" placeholder="{$s['searchBar']}">
                <span class="input-group-btn">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-search"></i>
                    </button>
                </span>
            </div>
            <div class="row mt-2 ms-1">
                <div class="form-check col-me">
                    <input class="form-check-input" type="radio" name="prod" id="Radio" value="all" checked>
                    <label class="form-check-label text-nowrap" for="Radio">{$s['allProd']}</label>
                </div>
                <div class="form-check form-check-inline col-md">
                    <input class="form-check-input" type="radio" name="prod" id="inlineRadio1" value="win81">
                    <label class="form-check-label text-nowrap" for="inlineRadio1">{$s['win81']}</label>
                </div>
                <div class="form-check form-check-inline col-md">
                    <input class="form-check-input" type="radio" name="prod" id="inlineRadio2" value="win10">
                    <label class="form-check-label text-nowrap" for="inlineRadio2">{$s['win10']}</label>
                </div>
                <div class="form-check form-check-inline col-md">
                    <input class="form-check-input" type="radio" name="prod" id="inlineRadio3" value="win11">
                    <label class="form-check-label text-nowrap" for="inlineRadio3">{$s['win11']}</label>
                </div>
                <div class="form-check form-check-inline col-md">
                    <input class="form-check-input" type="radio" name="prod" id="inlineRadio4" value="winsrvip">
                    <label class="form-check-label text-nowrap" for="inlineRadio4">{$s['winsrvip']}</label>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-md prod-btn mt-3 btn-group">
        <a class="btn btn-lg btn-info" href="./products.php?prod=win81">
            <div class="prod-btn-title">{$s['win81']}</div>
            <div class="prod-btn-desc text-opacity-75">{$s['win81_desc']}</div>
        </a>
    </div>
    <div class="col-md prod-btn mt-3 btn-group">
        <button type="button" class="btn btn-lg btn-info dropdown-toggle dropd-toggle-btn" data-bs-toggle="dropdown" aria-expanded="false">
            <div class="prod-btn-title">{$s['win10']}</div>
            <div class="prod-btn-desc text-opacity-75">{$s['win10_desc']}</div>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item" href="./products.php?prod=win10">{$s['win10']}</a></li>
          <li role="separator" class="dropdown-divider"></li>
          <li><a class="dropdown-item" href="./products.php?prod=win10th1">{$s['win10th1']}</a></li>
          <li><a class="dropdown-item" href="./products.php?prod=win10th2">{$s['win10th2']}</a></li>
          <li><a class="dropdown-item" href="./products.php?prod=win10rs1">{$s['win10rs1']}</a></li>
          <li><a class="dropdown-item" href="./products.php?prod=win10rs2">{$s['win10rs2']}</a></li>
          <li><a class="dropdown-item" href="./products.php?prod=win10rs3">{$s['win10rs3']}</a></li>
          <li><a class="dropdown-item" href="./products.php?prod=win10rs4">{$s['win10rs4']}</a></li>
          <li><a class="dropdown-item" href="./products.php?prod=win10rs5">{$s['win10rs5']}</a></li>
          <li><a class="dropdown-item" href="./products.php?prod=win10rs6">{$s['win10rs6']}</a></li>
          <li><a class="dropdown-item" href="./products.php?prod=win1019h2">{$s['win10_19h2']}</a></li>
          <li><a class="dropdown-item" href="./products.php?prod=win10vb">{$s['win10vb']}</a></li>
          <li><a class="dropdown-item" href="./products.php?prod=win1020h2">{$s['win10_20h2']}</a></li>
          <li><a class="dropdown-item" href="./products.php?prod=win1021h1">{$s['win10_21h1']}</a></li>
          <li><a class="dropdown-item" href="./products.php?prod=win1021h2">{$s['win10_21h2']}</a></li>
          <li><a class="dropdown-item" href="./products.php?prod=win1022h2">{$s['win10_22h2']}</a></li>
          <li><a class="dropdown-item" href="./products.php?prod=win10ip">{$s['win10ip']}</a></li>
        </ul>
    </div>
</div>

<div class="row">
    <div class="col-md prod-btn mt-3 btn-group">
        <button type="button" class="btn btn-lg btn-info dropdown-toggle dropd-toggle-btn" data-bs-toggle="dropdown" aria-expanded="false">
            <div class="prod-btn-title">{$s['win11']}</div>
            <div class="prod-btn-desc text-opacity-75">{$s['win11_desc']}</div>
        </button>
           <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="./products.php?prod=win11">{$s['win11']}</a></li>
              <li role="separator" class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="./products.php?prod=win11co">{$s['win11co']}</a></li>
              <li><a class="dropdown-item" href="./products.php?prod=win11ni">{$s['win11ni']}</a></li>
              <li><a class="dropdown-item" href="./products.php?prod=win11_23h2">{$s['win11_23h2']}</a></li>
              <li><a class="dropdown-item" href="./products.php?prod=win11ip">{$s['win11ip']}</a></li>
          </ul>
    </div>
    <div class="col-md prod-btn mt-3 btn-group">
        <a class="btn btn-lg btn-info" href="./products.php?prod=winsrvip">
            <div class="prod-btn-title">{$s['winsrvip']}</div>
            <div class="prod-btn-desc text-opacity-75">{$s['winsrvip_desc']}</div>
        </a>
    </div>
</div>

<hr class="mb-0">

<div class="row">
    <div class="col-md prod-btn mt-3 btn-group"><a class="btn btn-outline-dark btn-lg" href="./products.php?prod=all">
        <div class="prod-btn-title">{$s['allProd']}</div>
        <div class="prod-btn-desc text-opacity-75">{$s['allProd_desc']}</div>
    </a></div>
    <div class="col-md prod-btn mt-3 btn-group"><a class="btn btn-outline-dark btn-lg" href="./products.php?prod=other">
        <div class="prod-btn-title">{$s['otherProd']}</div>
        <div class="prod-btn-desc text-opacity-75">{$s['otherProd_desc']}</div>
    </a></div>
</div>
HTML;?>
<?php styleBottom(); ?>
