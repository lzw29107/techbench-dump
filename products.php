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

$config = get_config();

$options = '';
foreach(array('all', 'win81', 'win10', 'win11', 'winsrvip') as $opt) {
   if($prodName == $opt) continue;
   $optdesc = $opt == 'all' ? 'allProd' : $opt;
   $options .= '<div class="form-check form-check-inline col-md">
                   <input class="form-check-input" type="radio" name="prod" id="inlineRadio'.$opt.'" value="'.$opt.'">
                   <label class="form-check-label text-nowrap" for="inlineRadio'.$opt.'">'.$s[$optdesc].'</label>
               </div>';
}


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
    if($config['autoupd'] && $config['php'] && time() - $Tech->getAttribute('LastCheckUpdateTime') >= 3600) exec_background($config['php'], 'dump.php update');
   $out = array();
   $out['products'] = array();
   foreach($Prod->getElementsByTagName('ProdItem') as $prod) {
       if(isset($_GET['ignoreInvalid']) && $prod->getAttribute('Validity') == 'Invalid') continue;
       $out['products'][$prod->getAttribute('ID')] = array('Name' => $prod->getAttribute('Name'), 'Validity' => $prod->getAttribute('Validity'), 'Arch' => $prod->getAttribute('Arch'));
   }
   if(isset($_GET['reverse'])) $out['products'] = array_reverse($out['products'], true);
}

switch ($prodName) {
    case 'win7':
        $products = preg_grep('/Windows.7/',$out['products']);
        break;
    case 'win81':
        $products = preg_grep('/Windows.8\.1/',$out['products']);
        break;
    case 'win10':
        $products = preg_grep('/Windows.10/',$out['products']);
        break;
    case 'win10th1':
        $products = preg_grep('/Windows.10.*?Threshold.1/',$out['products']);
        break;
    case 'win10th2':
        $products = preg_grep('/Windows.10.*?Threshold.2/',$out['products']);
        break;
    case 'win10rs1':
        $products = preg_grep('/Windows.10.*?Redstone.1|Windows.*?Build 14393/',$out['products']);
        break;
    case 'win10rs2':
        $products = preg_grep('/Windows.10.*?Redstone.2|Windows.*?Build 15063/',$out['products']);
        break;
    case 'win10rs3':
        $products = preg_grep('/Windows.10.*?1709|Windows.*?Build 16299/',$out['products']);
        break;
    case 'win10rs4':
        $products = preg_grep('/Windows.10.*?1803|Windows.*?Build 17134/',$out['products']);
        break;
    case 'win10rs5':
        $products = preg_grep('/Windows.10.*?1809|Windows.*?Build 17763/',$out['products']);
        break;
    case 'win10rs6':
        $products = preg_grep('/Windows.10.*?1903|Windows.*?Build 18362/',$out['products']);
        break;
    case 'win10_19h2':
        $products = preg_grep('/Windows.10.*?1909|Windows.*?Build 18363/',$out['products']);
        break;
    case 'win10vb':
        $products = preg_grep('/Windows.10.*?2004|Windows.*?Build 19041/',$out['products']);
        break;
    case 'win10_20h2':
        $products = preg_grep('/Windows.10.*?20H2|Windows.*?Build 19042/',$out['products']);
        break;
    case 'win10_21h1':
        $products = preg_grep('/Windows.10.*?21H1|Windows.*?Build 19043/',$out['products']);
        break;
    case 'win10_21h2':
        $products = preg_grep('/Windows.10.*?21H2|Windows.*?Build 19044/',$out['products']);
        break;
    case 'win10_22h2':
        $products = preg_grep('/Windows.10.*?22H2|Windows.*?Build 19045/',$out['products']);
        break;
    case 'win10ip':
        $products = preg_grep('/Windows.*10.*?Insider.?Preview/',$out['products']);
        break;
    case 'win11':
        $products = preg_grep('/Windows.11/',$out['products']);
        break;
    case 'win11co':
        $products = preg_grep('/Windows.11.*?21H2|Windows.*?Build 22000/',$out['products']);
        break;
    case 'win11ni':
        $products = preg_grep('/Windows.11.*?22H2|Windows.*?Build 22621/',$out['products']);
        break;
    case 'win11_23h2':
        $products = preg_grep('/Windows.11.*?23H2|Windows.*?Build 22631/',$out['products']);
        break;
    case 'win11ip':
        $products = preg_grep('/Windows.*11.*?Insider.?Preview/',$out['products']);
        break;
    case 'winsrvip':
        $products = preg_grep('/Windows.*Server.*/',$out['products']);
        break;
    case 'office2007':
        $products = preg_grep('/ 2007/',$out['products']);
        break;
    case 'office2010':
        $products = preg_grep('/ 2010/',$out['products']);
        break;
    case 'office2011':
        $products = preg_grep('/ 2011/',$out['products']);
        break;
    case 'all':
       $prodName = 'allProd';
        $products = $out['products'];
        break;
    case 'other':
       $prodName = 'otherProd';
        $products = $out['products'];
       foreach($products as $key => $curr){
           $check = preg_match('/Windows.7|Windows.8\.1|Windows.10|Windows.11| 2007| 2010| 2011/', $curr['Name']);
            if($check) {
              unset($products[$key]);
            }
        }
        break;
    default:
       $prodName = 'allProd';
        $products = $out['products'];
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

    $products = preg_grep('/.*'.$searchSafe.'.*/i',$products);

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

<div class="card text-bg-light border-light mb-3">
   <div class="card-body pb-1">
       <form action="./products.php">
           <div class="input-group">
               <input type="text" class="form-control input-lg" name="search"$search placeholder="{$s['searchBar']}">
               <span class="input-group-btn">
                   <button type="submit" class="btn btn-primary btn-lg">
                       <i class="bi bi-search"></i>
                   </button>
               </span>
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
            <th class="text-center" scope="col">{$s['Name']}</th>
            <th class="text-center" scope="col">{$s['Validity']}</th>
            <th class="text-center" scope="col">{$s['Arch']}</th>
        </tr>
    </thead>
    <tbody class="table-group-divider">
    EOD;
    $perPage = 50;
    $pages = ceil(count($products) / $perPage);
    $startItem = ($page - 1) * $perPage;
    $PageBaseUrl = getUrlWithoutParam('p').'p=';

    $products = array_slice($products, $startItem, $perPage, true);

    foreach ($products as $key => $curr) {
        $Name = $curr['Name'];
        $Validity = $curr['Validity'];
        $Arch = $curr['Arch'];
        echo '<tr>
            <th class="text-center" scope="row">'.$key.'</th>
            <td>
                <a class="link-underline link-underline-opacity-0" href="./get.php?id='.$key.'">'.$Name.'</a>
            </td>
            <td class="text-center">'.$s[$Validity].'</td>
            <td class="text-center">'.$Arch.'</td>
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
       <li class="page-item"><a class="page-link" href="<?= $PageBaseUrl.$p ?>"><?= $p ?></a></li>
           <?php endif; ?>
       <?php endforeach; ?>
   <?php elseif($page <= 3): ?>
       <?php foreach((range(1, 5)) as $p): ?>
           <?php if($page == $p): ?>
       <li class="page-item active" aria-current="page">
           <span class="page-link"><?= $p ?></span>
       </li>
           <?php else: ?>
       <li class="page-item"><a class="page-link" href="<?= $PageBaseUrl.$p ?>"><?= $p ?></a></li>
           <?php endif; ?>
       <?php endforeach; ?>
       <li class="page-item disabled">
           <a class="page-link">...</a>
       </li>
       <li class="page-item"><a class="page-link" href="<?= $PageBaseUrl.$pages ?>"><?= $pages ?></a></li>
   <?php elseif($page >= ($pages - 2)): ?>
       <li class="page-item"><a class="page-link" href="<?= $PageBaseUrl ?>1">1</a></li>
       <li class="page-item disabled">
           <a class="page-link">...</a>
       </li>
       <?php foreach((range($pages-4, $pages)) as $p): ?>
           <?php if($page == $p): ?>
       <li class="page-item active" aria-current="page">
           <span class="page-link"><?= $p ?></span>
       </li>
           <?php else: ?>
       <li class="page-item"><a class="page-link" href="<?= $PageBaseUrl.$p ?>"><?= $p ?></a></li>
           <?php endif; ?>
       <?php endforeach; ?>
   <?php else: ?>
       <li class="page-item"><a class="page-link" href="<?= $PageBaseUrl ?>1">1</a></li>
       <?php if($page == 4): ?>
       <li class="page-item"><a class="page-link" href="<?= $PageBaseUrl ?>2">2</a></li>
       <?php else: ?>
       <li class="page-item disabled">
           <a class="page-link">...</a>
       </li>
       <?php endif; ?>
       <li class="page-item"><a class="page-link" href="<?= $PageBaseUrl.($page - 1) ?>"><?= ($page - 1) ?></a></li>
       <li class="page-item active" aria-current="page">
           <span class="page-link"><?= $page ?></span>
       </li>
       <li class="page-item"><a class="page-link" href="<?= $PageBaseUrl.($page + 1) ?>"><?= ($page + 1) ?></a></li>
       <?php if($page <= ($pages - 4)): ?>
       <li class="page-item disabled">
           <a class="page-link">...</a>
       </li>
       <?php else: ?>
       <li class="page-item"><a class="page-link" href="<?= $PageBaseUrl.($pages - 1) ?>"><?= ($pages - 1) ?></a></li>
       <?php endif; ?>
       <li class="page-item"><a class="page-link" href="<?= $PageBaseUrl.$pages ?>"><?= $pages ?></a></li>
   <?php endif; ?>
   </ul>
</nav>
<?php endif; ?>
<?php styleBottom(); ?>