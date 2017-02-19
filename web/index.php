<?php
// TechBench dump
// Copyright (C) 2017  mkuba50

// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.

// You should have received a copy of the GNU General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.

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

echo '<h1>'.$translation['tbDump'].'</h1>';
?>

<div class="alert alert-info" style="margin-top: 1.5em">
    <h4><span class="glyphicon glyphicon-dashboard" aria-hidden="true"></span> <?php echo $translation['techInfo']; ?></h4>
    <p> <?php echo $translation['lastUpdate']; ?>: <b><?php echo date("Y-m-d H:i:s T", $out['genTime']); ?></b><br>
     <?php echo $translation['productsNumber']; ?>: <b><?php echo $out['productNumber']; ?></b></p>
</div>

<h3><?php echo $translation['catSelect'];?>:</h3>

<div class="row" style="margin-top: -0.5em;">
    <div class="col-md-6"><a class="btn btn-primary btn-lg btn-block prod-btn" href="./products.php?prod=win7&<?php echo $langParam;?>">
        <div class="prod-btn-title"><?php echo $translation['win7'];?></div>
        <div class="prod-btn-desc"><?php echo $translation['win7_desc'];?></div>
    </a></div>
    <div class="col-md-6"><a class="btn btn-primary btn-lg btn-block prod-btn" href="./products.php?prod=win81&<?php echo $langParam;?>">
        <div class="prod-btn-title"><?php echo $translation['win81'];?></div>
        <div class="prod-btn-desc"><?php echo $translation['win81_desc'];?></div>
    </a></div>
</div>

<div class="row">
    <div class="col-md-6"><a class="btn btn-primary btn-lg btn-block prod-btn" href="./products.php?prod=win10&<?php echo $langParam;?>">
        <div class="prod-btn-title"><?php echo $translation['win10'];?></div>
        <div class="prod-btn-desc"><?php echo $translation['win10_desc'];?></div>
    </a></div>
    <div class="col-md-6"><a class="btn btn-primary btn-lg btn-block prod-btn" href="./products.php?prod=office2007&<?php echo $langParam;?>">
        <div class="prod-btn-title"><?php echo $translation['office2007'];?></div>
        <div class="prod-btn-desc"><?php echo $translation['office2007_desc'];?></div>
    </a></div>
</div>

<div class="row">
    <div class="col-md-6"><a class="btn btn-primary btn-lg btn-block prod-btn" href="./products.php?prod=office2010&<?php echo $langParam;?>">
        <div class="prod-btn-title"><?php echo $translation['office2010'];?></div>
        <div class="prod-btn-desc"><?php echo $translation['office2010_desc'];?></div>
    </a></div>
    <div class="col-md-6"><a class="btn btn-primary btn-lg btn-block prod-btn" href="./products.php?prod=office2011&<?php echo $langParam;?>">
        <div class="prod-btn-title"><?php echo $translation['office2011'];?></div>
        <div class="prod-btn-desc"><?php echo $translation['office2011_desc'];?></div>
    </a></div>
</div>

<hr>

<div class="row" style="margin-top: -1.25em;">
    <div class="col-md-6"><a class="btn btn-default btn-lg btn-block prod-btn" href="./products.php?prod=all&<?php echo $langParam;?>">
        <div class="prod-btn-title"><?php echo $translation['allProd'];?></div>
        <div class="prod-btn-desc"><?php echo $translation['allProd_desc'];?></div>
    </a></div>
    <div class="col-md-6"><a class="btn btn-default btn-lg btn-block prod-btn" href="./products.php?prod=other&<?php echo $langParam;?>">
        <div class="prod-btn-title"><?php echo $translation['otherProd'];?></div>
        <div class="prod-btn-desc"><?php echo $translation['otherProd_desc'];?></div>
    </a></div>
</div>

<?php styleBottom(); ?>
