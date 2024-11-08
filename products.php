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

$page = isset($_GET['p']) ? intval($_GET['p']) : 1;
$prodName = isset($_GET['prod']) ? $_GET['prod'] : 'all';
$search = isset($_GET['search']) ? $_GET['search'] : '';

require_once 'shared/lang.php';
require_once 'shared/style.php';

$config = getConfig();

$options = '';
foreach(array('all', 'win81', 'win10', 'win11', 'winsrvip') as $opt) {
   if($prodName == $opt) continue;
   $optdesc = $opt == 'all' ? 'allProd' : $opt;
   $options .= '<div class="form-check form-check-inline col-md">
                   <input class="form-check-input" type="radio" name="prod" id="inlineRadio'.$opt.'" value="'.$opt.'">
                   <label class="form-check-label text-nowrap" for="inlineRadio'.$opt.'">'.$s[$optdesc].'</label>
               </div>';
}

if(is_file('dump.json')) {
    $dump = json_decode(file_get_contents('dump.json'), true);
    if($config['autoupd'] && $config['php'] && time() - $dump['TechInfo']['LastCheckUpdateTime'] >= 3600) execBackground($config['php'], 'dump.php update --quiet');
    $out = [];
    $out['products'] = $dump['ProdInfo'];
    foreach($out['products'] as $id => $Prod) {
        if(isset($_GET['ignoreUnavailable']) && $Prod['Status'] == 'Unavailable') unset($out['products'][$id]);
    }
    if(isset($_GET['reverse'])) $out['products'] = array_reverse($out['products'], true);
}

$products = $out['products'];

switch ($prodName) {
    case 'win7':
        foreach($products as $key => $curr) if(!in_array('Win7', $curr['Category'])) unset($products[$key]);
        break;
    case 'win81':
        foreach($products as $key => $curr) if(!in_array('Win81', $curr['Category'])) unset($products[$key]);
        break;
    case 'win10':
        foreach($products as $key => $curr) if(!in_array('Win10', $curr['Category'])) unset($products[$key]);
        break;
    case 'win10th1':
        foreach($products as $key => $curr) if(!in_array('th1', $curr['Category'])) unset($products[$key]);
        break;
    case 'win10th2':
        foreach($products as $key => $curr) if(!in_array('th2', $curr['Category'])) unset($products[$key]);
        break;
    case 'win10rs1':
        foreach($products as $key => $curr) if(!in_array('rs1', $curr['Category'])) unset($products[$key]);
        break;
    case 'win10rs2':
        foreach($products as $key => $curr) if(!in_array('rs2', $curr['Category'])) unset($products[$key]);
        break;
    case 'win10rs3':
        foreach($products as $key => $curr) if(!in_array('rs3', $curr['Category'])) unset($products[$key]);
        break;
    case 'win10rs4':
        foreach($products as $key => $curr) if(!in_array('rs4', $curr['Category'])) unset($products[$key]);
        break;
    case 'win10rs5':
        foreach($products as $key => $curr) if(!in_array('rs5', $curr['Category'])) unset($products[$key]);
        break;
    case 'win10rs6':
        foreach($products as $key => $curr) if(!in_array('19H1', $curr['Category'])) unset($products[$key]);
        break;
    case 'win10_19h2':
        foreach($products as $key => $curr) if(!in_array('19H2', $curr['Category'])) unset($products[$key]);
        break;
    case 'win10vb':
        foreach($products as $key => $curr) if(!in_array('vb', $curr['Category'])) unset($products[$key]);
        break;
    case 'win10_20h2':
        foreach($products as $key => $curr) if(!in_array('20H2', $curr['Category'])) unset($products[$key]);
        break;
    case 'win10_21h1':
        foreach($products as $key => $curr) if(!in_array('21H1', $curr['Category'])) unset($products[$key]);
        break;
    case 'win10_21h2':
        foreach($products as $key => $curr) if(!in_array('21H2', $curr['Category'])) unset($products[$key]);
        break;
    case 'win10_22h2':
        foreach($products as $key => $curr) if(!in_array('22H2', $curr['Category'])) unset($products[$key]);
        break;
    case 'win10ip':
        foreach($products as $key => $curr) if(!in_array('WIP', $curr['Category']) || !in_array('Win10', $curr['Category'])) unset($products[$key]);
        break;
    case 'win11':
        foreach($products as $key => $curr) if(!in_array('Win11', $curr['Category'])) unset($products[$key]);
        break;
    case 'win11co':
        foreach($products as $key => $curr) if(!in_array('co', $curr['Category'])) unset($products[$key]);
        break;
    case 'win11ni':
        foreach($products as $key => $curr) if(!in_array('ni', $curr['Category'])) unset($products[$key]);
        break;
    case 'win11_23h2':
        foreach($products as $key => $curr) if(!in_array('23H2', $curr['Category'])) unset($products[$key]);
        break;
    case 'win11ge':
        foreach($products as $key => $curr) if(!in_array('ge', $curr['Category'])) unset($products[$key]);
        break;
    case 'win11ip':
        foreach($products as $key => $curr) if(!in_array('WIP', $curr['Category']) || !in_array('Win11', $curr['Category'])) unset($products[$key]);
        break;
    case 'winsrvip':
        foreach($products as $key => $curr) if(!in_array('WIP', $curr['Category']) || !in_array('WinSrv', $curr['Category'])) unset($products[$key]);
        break;
    case 'office2007':
        foreach($products as $key => $curr) if(!in_array('2007', $curr['Category'])) unset($products[$key]);
        break;
    case 'office2010':
        foreach($products as $key => $curr) if(!in_array('2010', $curr['Category'])) unset($products[$key]);
        break;
    case 'office2011':
        foreach($products as $key => $curr) if(!in_array('2011', $curr['Category'])) unset($products[$key]);
        break;
    case 'all':
        $prodName = 'allProd';
        break;
    case 'other':
        $prodName = 'otherProd';
        foreach($products as $key => $curr) if(!in_array('Other', $curr['Category'])) unset($products[$key]);
        break;
    default:
        $prodName = 'allProd';
        break;
}

if(isset($s[$prodName])) {
   $selectedCategory = $s[$prodName];
}

if($search != '') {
    $searchSafe = preg_quote($search, '/');
    if (!preg_match('/^".*"$/', $searchSafe)) {
        $searchSafe = str_replace(' ', '.*', $searchSafe);
    } else {
        $searchSafe = preg_replace('/^"|"$/', '', $searchSafe);
    }

    foreach($products as $prodId => $product) {
        if(stripos($product['Name'], $searchSafe) === false) unset($products[$prodId]);
    }

    $tableTitle = $s['searchResults'].': '.$search;
    $noItems = $s['searchNoResults'];
} else {
    $tableTitle = $s['prodSelect'];
    $noItems = $s['noProducts'];
}

styleTop('downloads');

echo <<<HTML
<div class="mt-5 mb-4">
   <h1 class="fs-3">{$s['tbDumpDownload']}</h1>
</div>

<div class="card text-bg mb-3">
   <div class="card-body pb-1">
       <form action="./products.php">
           <div class="input-group">
               <input type="text" class="form-control input-lg" name="search"$search placeholder="{$s['searchBar']}">
               <button type="submit" class="btn btn-primary btn-lg">
                   <i class="bi bi-search"></i>
               </button>
           </div>
           <div class="row mt-2 ms-1">
               <div class="form-check col-me">
                   <input class="form-check-input" type="radio" name="prod" id="Radio" value="$prodName" checked>
                   <label class="form-check-label text-nowrap" for="Radio">{$s['currentProd']} ( $selectedCategory )</label>
               </div>
               $options
         </div>
      </form>
   </div>
</div>

<div class="row">
    <h3 class="col-md me-auto fs-4">
        <i class="bi bi-list-ul"></i>
        $selectedCategory
    </h3>

HTML;?>
<?php if(empty($products)) {
    echo '<p class="fs-1 text-center">'.$noItems.'</p>';
    styleBottom();
    exit();
} else {
    echo <<<EOD
    <table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th class="text-center" scope="col">{$s['idName']}</th>
            <th class="text-center" scope="col">{$s['name']}</th>
            <th class="text-center" scope="col">{$s['status']}</th>
            <th class="text-center" scope="col">{$s['arch']}</th>
        </tr>
    </thead>
    <tbody class="table-group-divider">
    EOD;
    $perPage = 50;
    $pages = ceil(count($products) / $perPage);
    $startItem = ($page - 1) * $perPage;
    $pageBaseUrl = getUrlWithoutParam('p').'p=';

    $products = array_slice($products, $startItem, $perPage, true);

    foreach ($products as $key => $curr) {
        $name = $curr['Name'];
        $status = $curr['Status'];
        $arch = implode(', ', $curr['Arch']);
        echo '<tr>
            <th class="text-center" scope="row">'.$key.'</th>
            <td>
                <a class="link-underline link-underline-opacity-0" href="./get.php?id='.$key.'">'.$name.'</a>
            </td>
            <td class="text-center">'.$s[strtolower($status)].'</td>
            <td class="text-center">'.$arch.'</td>
        </tr>
        ';
    }
}
echo '</tbody>
</table>';
?>
<?php if($pages > 1): ?>
<nav aria-label="Page navigation">
   <ul class="pagination justify-content-center">
   <?php if($pages <= 7): ?>
       <?php foreach((range(1, $pages)) as $p): ?>
           <?php if($page == $p): ?>
       <li class="page-item active" aria-current="page">
           <span class="page-link"><?= $p ?></span>
       </li>
           <?php else: ?>
       <li class="page-item"><a class="page-link" href="<?= $pageBaseUrl.$p ?>"><?= $p ?></a></li>
           <?php endif; ?>
       <?php endforeach; ?>
   <?php elseif($page <= 3): ?>
       <?php foreach((range(1, 5)) as $p): ?>
           <?php if($page == $p): ?>
       <li class="page-item active" aria-current="page">
           <span class="page-link"><?= $p ?></span>
       </li>
           <?php else: ?>
       <li class="page-item"><a class="page-link" href="<?= $pageBaseUrl.$p ?>"><?= $p ?></a></li>
           <?php endif; ?>
       <?php endforeach; ?>
       <li class="page-item disabled">
           <a class="page-link">...</a>
       </li>
       <li class="page-item"><a class="page-link" href="<?= $pageBaseUrl.$pages ?>"><?= $pages ?></a></li>
   <?php elseif($page >= ($pages - 2)): ?>
       <li class="page-item"><a class="page-link" href="<?= $pageBaseUrl ?>1">1</a></li>
       <li class="page-item disabled">
           <a class="page-link">...</a>
       </li>
       <?php foreach((range($pages-4, $pages)) as $p): ?>
           <?php if($page == $p): ?>
       <li class="page-item active" aria-current="page">
           <span class="page-link"><?= $p ?></span>
       </li>
           <?php else: ?>
       <li class="page-item"><a class="page-link" href="<?= $pageBaseUrl.$p ?>"><?= $p ?></a></li>
           <?php endif; ?>
       <?php endforeach; ?>
   <?php else: ?>
       <li class="page-item"><a class="page-link" href="<?= $pageBaseUrl ?>1">1</a></li>
       <?php if($page == 4): ?>
       <li class="page-item"><a class="page-link" href="<?= $pageBaseUrl ?>2">2</a></li>
       <?php else: ?>
       <li class="page-item disabled">
           <a class="page-link">...</a>
       </li>
       <?php endif; ?>
       <li class="page-item"><a class="page-link" href="<?= $pageBaseUrl.($page - 1) ?>"><?= ($page - 1) ?></a></li>
       <li class="page-item active" aria-current="page">
           <span class="page-link"><?= $page ?></span>
       </li>
       <li class="page-item"><a class="page-link" href="<?= $pageBaseUrl.($page + 1) ?>"><?= ($page + 1) ?></a></li>
       <?php if($page <= ($pages - 4)): ?>
       <li class="page-item disabled">
           <a class="page-link">...</a>
       </li>
       <?php else: ?>
       <li class="page-item"><a class="page-link" href="<?= $pageBaseUrl.($pages - 1) ?>"><?= ($pages - 1) ?></a></li>
       <?php endif; ?>
       <li class="page-item"><a class="page-link" href="<?= $pageBaseUrl.$pages ?>"><?= $pages ?></a></li>
   <?php endif; ?>
   </ul>
</nav>
<?php endif; ?>
<?php styleBottom(); ?>