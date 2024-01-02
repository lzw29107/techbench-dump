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

$out = @file_get_contents('dump.json');
if(empty($out)) {
    $out = array('genTime' => null, 'productNumber' => '?', 'products' => null);
} else {
    $out = json_decode($out, true);
}

styleTop('home');

$genTime = date("Y-m-d H:i:s T", $out['genTime']);

echo <<<HTML
<h1>{$s['tbDump']} <span class="badge">v$websiteVersion</span></h1>


<div class="alert alert-info" style="margin-top: 1.5em">
    <h4><span class="glyphicon glyphicon-dashboard" aria-hidden="true"></span> {$s['techInfo']}</h4>
    <p> {$s['lastUpdate']}: <b> $genTime</b><br>
     {$s['productsNumber']}: <b>{$out['productNumber']}</b></p>
</div>

<div class="well">
    <form action="./products.php">
        <div class="input-group">
            <input type="text" class="form-control input-lg" name="search" placeholder="{$s['searchBar']}">
            <span class="input-group-btn">
                <button type="submit" class="btn btn-primary btn-lg">
                    <span class="glyphicon glyphicon-search"></span>
                </button>
            </span>
        </div>
        <div class="row" style="margin-top: 0.5em;">
            <div class="col-md-12">
                <label class="radio-inline">
                    <input type="radio" name="prod" value="all" checked>{$s['allProd']}</label>
            </div>
            <div class="col-md-3">
                <label class="radio-inline">
                    <input type="radio" name="prod" value="win81">{$s['win81']}</label>
            </div>
            <div class="col-md-3">
                <label class="radio-inline">
                    <input type="radio" name="prod" value="win10">{$s['win10']}</label>
            </div>
            <div class="col-md-3">
                <label class="radio-inline">
                    <input type="radio" name="prod" value="win11">{$s['win11']}</label>
            </div>
            <div class="col-md-3">
                <label class="radio-inline">
                    <input type="radio" name="prod" value="winsrvip">{$s['winsrvip']}</label>
            </div>
        </div>
    </form>
</div>

<div class="row" style="margin-top: -1.25em;">
    <div class="col-md-6 prod-btn"><a class="btn btn-primary btn-lg btn-block" href="./products.php?prod=win81">
        <div class="prod-btn-title">{$s['win81']}</div>
        <div class="prod-btn-desc">{$s['win81_desc']}</div>
    </a></div>
    <div class="col-md-6 prod-btn">
        <button class="btn btn-primary btn-block btn-lg dropdown-toggle dropd-toggle-btn" data-toggle="dropdown">
            <div class="prod-btn-title">{$s['win10']}</div>
            <div class="prod-btn-desc">{$s['win10_desc']}</div>
            <span class="caret dropd-icon"></span>
        </button>
        <ul class="dropdown-menu dropd-menu-right">
            <li><a href="./products.php?prod=win10">{$s['win10']}</a></li>
            <li role="separator" class="divider"></li>
            <li><a href="./products.php?prod=win10th1">{$s['win10th1']}</a></li>
            <li><a href="./products.php?prod=win10th2">{$s['win10th2']}</a></li>
            <li><a href="./products.php?prod=win10rs1">{$s['win10rs1']}</a></li> <!-- Windows 10 Redmond 1 -->
            <li><a href="./products.php?prod=win10rs2">{$s['win10rs2']}</a></li> <!-- Windows 10 Redmond 2 -->
            <li><a href="./products.php?prod=win10rs3">{$s['win10rs3']}</a></li> <!-- Windows 10 Redmond 3 -->
            <li><a href="./products.php?prod=win10rs4">{$s['win10rs4']}</a></li> <!-- Windows 10 Redmond 4 -->
            <li><a href="./products.php?prod=win10rs5">{$s['win10rs5']}</a></li> <!-- Windows 10 Redmond 5 -->
            <li><a href="./products.php?prod=win10rs6">{$s['win10rs6']}</a></li> <!-- Windows 10 Redmond 6 -->
            <li><a href="./products.php?prod=win10_19h2">{$s['win10_19h2']}</a></li> <!-- Windows 10 Scam Edition -->
            <li><a href="./products.php?prod=win10vb">{$s['win10vb']}</a></li>
            <li><a href="./products.php?prod=win10_20h2">{$s['win10_20h2']}</a></li>
            <li><a href="./products.php?prod=win10_21h1">{$s['win10_21h1']}</a></li>
            <li><a href="./products.php?prod=win10_21h2">{$s['win10_21h2']}</a></li>
            <li><a href="./products.php?prod=win10_22h2">{$s['win10_22h2']}</a></li>
            <li><a href="./products.php?prod=win10ip">{$s['win10ip']}</a></li>
        </ul>
    </div>
</div>

<div class="row">
    <div class="col-md-6 prod-btn">
        <button class="btn btn-primary btn-block btn-lg dropdown-toggle dropd-toggle-btn" data-toggle="dropdown">
            <div class="prod-btn-title">{$s['win11']}</div>
            <div class="prod-btn-desc">{$s['win11_desc']}</div>
            <span class="caret dropd-icon"></span>
        </button>
        <ul class="dropdown-menu dropd-menu-right">
            <li><a href="./products.php?prod=win11">{$s['win11']}</a></li>
            <li role="separator" class="divider"></li>
            <li><a href="./products.php?prod=win11co">{$s['win11co']}</a></li>
            <li><a href="./products.php?prod=win11ni">{$s['win11ni']}</a></li>
            <li><a href="./products.php?prod=win11_23h2">{$s['win11_23h2']}</a></li>
            <li><a href="./products.php?prod=win11ip">{$s['win11ip']}</a></li>
        </ul>
    </div>
    <div class="col-md-6 prod-btn"><a class="btn btn-primary btn-lg btn-block" href="./products.php?prod=winsrvip">
        <div class="prod-btn-title">{$s['winsrvip']}</div>
        <div class="prod-btn-desc">{$s['winsrvip_desc']}</div>
    </a></div>
</div>

<hr>

<div class="row" style="margin-top: -1.25em;">
    <div class="col-md-6 prod-btn"><a class="btn btn-default btn-lg btn-block" href="./products.php?prod=all">
        <div class="prod-btn-title">{$s['allProd']}</div>
        <div class="prod-btn-desc">{$s['allProd_desc']}</div>
    </a></div>
    <div class="col-md-6 prod-btn"><a class="btn btn-default btn-lg btn-block" href="./products.php?prod=other">
        <div class="prod-btn-title">{$s['otherProd']}</div>
        <div class="prod-btn-desc">{$s['otherProd_desc']}</div>
    </a></div>
</div>
HTML;?>
<?php styleBottom(); ?>
