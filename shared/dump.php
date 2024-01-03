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

require_once 'contrib/langconf.php';

// inspired from @ghbarratt, @stan on http://cn.voidcc.com/question/p-nfgudtgw-yd.html
function indentContent($path, $tab="\t") 
{
    $content = file_get_contents($path);
    // add marker linefeeds to aid the pretty-tokeniser (adds a linefeed between all tag-end boundaries) 
    $content = preg_replace('/(>)(<)(\/*)/', "$1\n$2$3", $content); 

    // now indent the tags 
    $token = strtok($content, "\n"); 
    $result = ''; // holds formatted version as it is built 
    $pad = 0; // Original indent 
    $matches = array(); // returns from preg_matches() 

    // scan each line and adjust indent based on opening/closing tags 
    while ($token !== false) 
    { 
     $token = trim($token); 
     // test for the various tag states 

     // 1. open and closing tags on same line - no change 
     if (preg_match('/.+<\/\w[^>]*>$/', $token, $matches)) $indent=0; 
     // 2. closing tag - outdent now 
     elseif (preg_match('/^<\/\w/', $token, $matches)) 
     { 
      $pad--; 
      if($indent>0) $indent=0; 
     } 
     // 3. opening tag - don't pad this one, only subsequent tags (only if it isn't a void tag) 
     elseif (preg_match('/^<\w[^>]*[^\/]>.*$/', $token, $matches)) 
     { 
      $voidTag = false; 
      foreach ($matches as $m) 
      { 
       // Void elements according to http://www.htmlandcsswebdesign.com/articles/voidel.php 
       if (preg_match('/^<(area|base|br|col|command|embed|hr|img|input|keygen|link|meta|param|source|track|wbr)/im', $m)) 
       { 
        $voidTag = true; 
        break; 
       } 
      } 

      if (!$voidTag) $indent=1; 
     } 
     // 4. no indentation needed 
     else $indent = 0; 

     // pad the line with the required number of leading spaces 
     $line = str_pad($token, strlen($token)+$pad, $tab, STR_PAD_LEFT); 
     $result .= $line."\n"; // add to the cumulative result, with linefeed 
     $token = strtok("\n"); // get the next token 
     $pad += $indent; // update the pad size for subsequent lines  
    }  
    file_put_contents($path, $result);
}

function checkStrNum($str = null) {
    if(!is_numeric($str) || is_double($str) || $str > PHP_INT_MAX || $str < 0) return 0;
    return 1;
}

function NewSubItem($ProductID, $Info) {
    global $dom, $Prod;
    $ProdSubItem = $dom->createElement('ProdItem');
    $Prod->appendChild($ProdSubItem);
    $ProdSubItem->setAttribute('ID', $ProductID);
    $ProdSubItem->setAttribute('Name', $Info['ProductName']);
    $ProdSubItem->setAttribute('Category', $Info['Category']);
    $ProdSubItem->setAttribute('Validity', $Info['Validity']);
    $ProdSubItem->setAttribute('Arch', $Info['Arch']);
    foreach($Info['Skus'] as $SkuID => $Sku) NewSku($ProdSubItem, $SkuID, $Sku);
}

function NewSku($ProdSubItem, $SkuID, $Sku) {
    global $dom, $Prod, $enLangName;
    if(in_array($Sku, $enLangName)) {
        $Sku = array_search($Sku, $enLangName);
        $ProdSku = $dom->createElement('Language');
    } else {
        $ProdSku = $dom->createElement('Sku');
    }
    $ProdSubItem->appendChild($ProdSku);
    $ProdSku->setAttribute('ID', $SkuID);
    $ProdSku->setAttribute('Name', $Sku);
}

function dump($minProdID, $maxProdID) {
    global $dom, $Prod, $SessionID, $lock;
    $Ignore = array_merge(array(53, 54, 84, 421, 422, 552, 559, 561, 563, 564, 569, 577, 585, 587, 588, 657, 658, 764, 765, 793, 848, 849, 937, 938, 1033, 1034, 1134, 1135, 1213, 1371, 1372, 1469, 1470, 1511, 1565, 1687, 1868, 1908, 1909, 2037, 2039), range(1, 27, 2), range(29, 47), range(49, 51), range(56, 60), range(63, 67), range(72, 74), range(129, 172), range(571, 574), range(680, 686), range(708, 731), range(748, 762), range(767, 791), range(809, 820), range(839, 846), range(861, 873), range(896, 903), range(940, 947), range(981, 989), range(1009, 1011), range(1025, 1030), range(1040, 1043), range(1062, 1069), range(1486, 1508), range(1527, 1529), range(2651, 2658));

    $allProd = array_diff(range($minProdID, $maxProdID), $Ignore);
    $lock['status'] = 'update';
    $errorCount = 0;
    $lock['total'] = count($allProd);
    $lock['current'] = 0;
    foreach($allProd as $ProductID) {
        $lock['current']++;
        $lock['progress'] = number_format(($lock['current'] / $lock['total']) * 100, 2);
        if($ProductID > $maxProdID && $errorCount > 10) break;
        if($lock['current'] % 15 == 1) {
            $SessionID = SessionIDInit();
        }
        $html = new DOMDocument();
        $html->loadHTML(getInfo('Prod', $ProductID));
        if($html->getElementById('errorModalMessage')) {
            //$errorMsg = $html->getElementById('errorModalMessage')->textContent;
            $errorCount++;
        } else {
            NewSubItem($ProductID, parserProdInfo($ProductID, $html));
            $errorCount = 0;
        }
        $lock['time'] = time();
        file_put_contents('dump.xml.lock', json_encode($lock));
    }
}

function recheck($lastProdID, $lastBlocked = null) {
    global $dom, $Prod, $SessionID, $lock, $enLangName;
    $lock['status'] = 'recheck';
    $Time = time();
    $lock['total'] = 0;
    $lock['current'] = 0;
    $Valid = array();

    foreach($Prod->getElementsByTagName('ProdItem') as $prod) {
        if($prod->getAttribute('Validity') != 'Valid') continue;
        if($lastBlocked && $prod->getAttribute('ID') < $lastBlocked) continue;
        $Valid[] = $prod->getAttribute('ID');
        $lock['total']++;
    }
    $xpath = new DOMXPath($dom);
    
    foreach($Valid as $ProductID) {
        $lock['current']++;
        $lock['progress'] = number_format(($lock['current'] / $lock['total']) * 100, 2);
        $prod = $xpath->query("./*[@ID=$ProductID]", $Prod);
        if($prod->item(0)) {
            $ProdItem = $prod->item(0);
            if($ProdItem->getAttribute('Validity') != 'Valid') continue;
        } else continue;
        if($lock['current'] % 15 == 1) {
            $SessionID = SessionIDInit();
        }

        $html = new DOMDocument();
        $html->loadHTML(getInfo('Prod', $ProductID));
        $Info = parserProdInfo($ProductID, $html);
        if($Info['Validity'] != 'Invalid') {
            $ProdItem->setAttribute('Name', $Info['ProductName']);
            $ProdItem->setAttribute('Category', $Info['Category']);
            $ProdItem->setAttribute('Validity', $Info['Validity']);
            if($Info['Arch'] != 'Unknown') {
                $ProdItem->setAttribute('Arch', $Info['Arch']);
            } else return $ProductID;
        } else $ProdItem->setAttribute('Validity', $Info['Validity']);
      
        $type = $ProdItem->firstElementChild->tagName;
        
        foreach($ProdItem->getElementsByTagName($type) as $Sku) {
            $SkuID = $Sku->getAttribute('ID');
            unset($Info['Skus'][$SkuID]);
        }
        foreach($Info['Skus'] as $SkuID => $Sku) {
            if(in_array($Sku, $enLangName)) {
                $Sku = array_search($Sku, $enLangName);
                $ProdSku = $dom->createElement('Language');
            } else {
                $ProdSku = $dom->createElement('Sku');
            }
            $prod->appendChild($ProdSku);
            $ProdSku->setAttribute('ID', $SkuID);
            $ProdSku->setAttribute('Name', $Sku);
        }
        $lock['time'] = time();
        file_put_contents('dump.xml.lock', json_encode($lock));
    }
}
?>
