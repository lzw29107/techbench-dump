<?php
// Copyright 2017 mkuba50

// Licensed under the Apache License, Version 2.0 (the "License");
// you may not use this file except in compliance with the License.
// You may obtain a copy of the License at

//    http://www.apache.org/licenses/LICENSE-2.0

// Unless required by applicable law or agreed to in writing, software
// distributed under the License is distributed on an "AS IS" BASIS,
// WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
// See the License for the specific language governing permissions and
// limitations under the License.

$lang = isset($_GET['lang']) ? $_GET['lang'] : 'en-us';
require 'lang/core.php';
require 'shared/style.php';

$out = @file_get_contents('dump.json');
if(empty($out)) {
    $out = array('genTime' => null, 'productNumber' => '?', 'products' => null);
} else {
    $out = json_decode($out, true);
}

styleTop('home');

echo '<h1>'.$translation['tbDump'].' <span class="badge">v'.$websiteVersion.'</span></h1>';
?>

<div class="alert alert-info" style="margin-top: 1.5em">
    <h4><span class="glyphicon glyphicon-dashboard" aria-hidden="true"></span> <?php echo $translation['techInfo']; ?></h4>
    <p> <?php echo $translation['lastUpdate']; ?>: <b><?php echo date("Y-m-d H:i:s T", $out['genTime']); ?></b><br>
     <?php echo $translation['productsNumber']; ?>: <b><?php echo $out['productNumber']; ?></b></p>
</div>

<h3><?php echo $translation['catSelect'];?>:</h3>

<div class="row" style="margin-top: -0.5em;">
    <div class="col-md-6 prod-btn"><a class="btn btn-primary btn-lg btn-block" href="./products.php?prod=win7&<?php echo $langParam;?>">
        <div class="prod-btn-title"><?php echo $translation['win7'];?></div>
        <div class="prod-btn-desc"><?php echo $translation['win7_desc'];?></div>
    </a></div>
    <div class="col-md-6 prod-btn"><a class="btn btn-primary btn-lg btn-block" href="./products.php?prod=win81&<?php echo $langParam;?>">
        <div class="prod-btn-title"><?php echo $translation['win81'];?></div>
        <div class="prod-btn-desc"><?php echo $translation['win81_desc'];?></div>
    </a></div>
</div>

<div class="row">
  <div class="col-md-6 prod-btn">
      <button class="btn btn-primary btn-block btn-lg dropdown-toggle dropd-toggle-btn" data-toggle="dropdown">
          <div class="prod-btn-title"><?php echo $translation['win10'];?></div>
          <div class="prod-btn-desc"><?php echo $translation['win10_desc'];?></div>
          <span class="caret dropd-icon"></span>
      </button>
      <ul class="dropdown-menu dropd-menu-right">
          <li><a href="./products.php?prod=win10&<?php echo $langParam;?>"><?php echo $translation['win10'];?></a></li>
          <li role="separator" class="divider"></li>
          <li><a href="./products.php?prod=win10th1&<?php echo $langParam;?>"><?php echo $translation['win10th1'];?></a></li>
          <li><a href="./products.php?prod=win10th2&<?php echo $langParam;?>"><?php echo $translation['win10th2'];?></a></li>
          <li><a href="./products.php?prod=win10rs1&<?php echo $langParam;?>"><?php echo $translation['win10rs1'];?></a></li>
          <li><a href="./products.php?prod=win10ip&<?php echo $langParam;?>"><?php echo $translation['win10ip'];?></a></li>
      </ul>
  </div>
    <div class="col-md-6 prod-btn"><a class="btn btn-primary btn-lg btn-block" href="./products.php?prod=office2007&<?php echo $langParam;?>">
        <div class="prod-btn-title"><?php echo $translation['office2007'];?></div>
        <div class="prod-btn-desc"><?php echo $translation['office2007_desc'];?></div>
    </a></div>
</div>

<div class="row">
    <div class="col-md-6 prod-btn"><a class="btn btn-primary btn-lg btn-block" href="./products.php?prod=office2010&<?php echo $langParam;?>">
        <div class="prod-btn-title"><?php echo $translation['office2010'];?></div>
        <div class="prod-btn-desc"><?php echo $translation['office2010_desc'];?></div>
    </a></div>
    <div class="col-md-6 prod-btn"><a class="btn btn-primary btn-lg btn-block" href="./products.php?prod=office2011&<?php echo $langParam;?>">
        <div class="prod-btn-title"><?php echo $translation['office2011'];?></div>
        <div class="prod-btn-desc"><?php echo $translation['office2011_desc'];?></div>
    </a></div>
</div>

<hr>

<div class="row" style="margin-top: -1.25em;">
    <div class="col-md-6 prod-btn"><a class="btn btn-default btn-lg btn-block" href="./products.php?prod=all&<?php echo $langParam;?>">
        <div class="prod-btn-title"><?php echo $translation['allProd'];?></div>
        <div class="prod-btn-desc"><?php echo $translation['allProd_desc'];?></div>
    </a></div>
    <div class="col-md-6 prod-btn"><a class="btn btn-default btn-lg btn-block" href="./products.php?prod=other&<?php echo $langParam;?>">
        <div class="prod-btn-title"><?php echo $translation['otherProd'];?></div>
        <div class="prod-btn-desc"><?php echo $translation['otherProd_desc'];?></div>
    </a></div>
</div>

<?php styleBottom(); ?>
