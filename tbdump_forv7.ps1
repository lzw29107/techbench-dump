<#
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
#>

<#
.Synopsis

Techbench dump script v2

.Description

Techbench dump script v2.10.0 for PowerShell v7+
Although this script may run normally on earlier versions of PowerShell, it is not recommended to do so.

.Parameter Continue

Starting dump from existing dump file.

.Parameter minProdID

The Request will start from this ID.
If the -Continue parameter is specified, it will be ignored.

.Parameter maxProdID

The request will end at this ID.

.Parameter Path

Path to dump file.

.Inputs

None.

.Outputs

System.String.

.Example

PS> tbdump.ps1
[INFO] TechBench dump script (tbdump-web v2.10.0)
[INFO] Using Product ID range from 1 to 3000

[INFO] Checking for languages using Product ID...
[INFO] Checking product ID: 1
[ERROR] Product does not exist!

[INFO] Checking product ID: 2
[INFO] Got language list!
[INFO] Writing...
[INFO] OK!

    

[INFO] Checking product ID: 2888
[INFO] Got language list!
[INFO] Writing...
[INFO] OK!

    

[INFO] Checking product ID: 3000
[ERROR] Product does not exist!

[INFO] Number of products: 2566
[INFO] Done

.Example

PS> tbdump.ps1 -minProdID 1 -maxProdID 2 -Path "D:\TechBench dump\testdump.json"
[INFO] TechBench dump script (tbdump-web v2.10.0)
[INFO] Using Product ID range from 1 to 2

[INFO] Checking for languages using Product ID...
[INFO] Checking product ID: 1
[ERROR] Product does not exist!

[INFO] Checking product ID: 2
[INFO] Got language list!
[INFO] Writing...
[INFO] OK!

[INFO] Number of products: 1
[INFO] Done

.Example

PS> tbdump.ps1 -Continue -maxProdID 4 -Path "D:\TechBench dump\testdump.json"
[INFO] TechBench dump script (tbdump-web v2.10.0)
[INFO] Using Product ID range from 3 to 4

[INFO] Checking for languages using Product ID...
[INFO] Checking product ID: 3
[ERROR] Product does not exist!

[INFO] Checking product ID: 4
[INFO] Got language list!
[INFO] Writing...
[INFO] OK!

[INFO] Number of products: 2
[INFO] Done

.Link

https://tb.lzw29107.repl.co/
#>

param ([switch]$Continue, [int]$minProdID = 1, [int]$maxProdID = 3000, [string]$Path = "dump.json")

$ver='v2.10.0'

$ProgressPreference='silentlycontinue'

$infoHead = "[INFO]"
$warnHead = "[WARNING]"
$errorHead = "[ERROR]"

$BackupPath = "$($Path)_bak"

if ($Continue) {
    if (Test-Path "$BackupPath" -PathType Leaf) {Move-Item -Path "$BackupPath" -Destination "$Path" -Force}
    if (Test-Path "$Path" -PathType Leaf) {
    $productNumber =(Get-Content $Path -Raw) -replace '[\s\S]*"productNumber":"(.*)"[\s\S]*', '$1'
    if ($productNumber -notmatch "^[\d\.]+$") {"$errorHead Invalid file."; exit 1}
    $lastProdId = (Get-Content $Path -Raw) -replace '[\s\S]*"(.*)": [\s\S]*[^,]', '$1'
    if ($lastProdId -notmatch "^[\d\.]+$") {"$errorHead Invalid file."; exit 1}
    $minProdID = ($lastProdId+1)
    $productsFound = $productNumber
    } else {
    "$errorHead File is not exist."; exit 1
    }
}

if ($minProdID -le 0) {
    "$errorHead First Product ID needs to be larger than 0."
    exit 1
    }
if ($maxProdID -le 0) {
    "$errorHead Last Product ID needs to be larger than 0."
    exit 1
    }
if ($maxProdID -lt $minProdID) {
    "$errorHead Last Product ID needs to be larger or equal to first Product ID."
    exit 1
    }

$noProductErr="The product key you entered is invalid or not supported by this site."

#URLs to all needed things
$SessionInitUrl="https://vlscppe.microsoft.com/fp/tags.js?org_id=y6jn8c31"
$getLangUrl="https://www.microsoft.com/en-us/api/controls/contentinclude/html?pageId=cd06bda8-ff9c-4a6e-912a-b92a21f42526&host=www.microsoft.com&segments=software-download,windows11&query=&action=getskuinformationbyproductedition"
$getDownUrl="https://www.microsoft.com/en-us/api/controls/contentinclude/html?pageId=cfa9e580-a81e-4a4b-a846-7b21bf4e2e5b&host=www.microsoft.com&segments=software-download,windows11&query=&action=GetProductDownloadLinksBySku"

$Headers = @{
    'Referer' = 'https://www.microsoft.com/en-us/software-download/windows11'
}

#############################
# Generic functions section #
#############################
function genSessionID {
  $script:time = Get-Date -UFormat %s
  $SessionId = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx' -replace "[xy]" , {
      $random = (("$time" + (Get-Random -Minimum -0 -Maximum 15)) % 16)
      if ($_.Value -eq 'y') {
          $random = ("$random" -band 3 -bor 8)
      }
      $time = [Math]::Floor("$time"/16)
      $random = $random.ToString('x')
      return $random
  }
  return $SessionId
}

function SessionIDInit {
  $SessionId = genSessionID
  $result = Invoke-WebRequest -Uri "$SessionInitUrl&session_id=$SessionId" -HttpVersion 3.0 -SslProtocol Tls12 -Headers $Headers -DisableKeepAlive -UseBasicParsing
  return $SessionId
}

function getLangs {
  $result = Invoke-WebRequest -Uri "$getLangUrl&sessionid=$SessionId&productEditionId=$productID&sdVersion=2" -WebSession $Session -HttpVersion 3.0 -SslProtocol Tls12 -Headers $Headers -UseBasicParsing

  if ($result.Content -match "$noProductErr") {
    return 1
  }

  if ($result.Content -notmatch 'option value=.{&quot;id') {
    return 2
  }
  
  $script:productName = ($result.Content -replace "[\s\S]*<i>The product key is eligible for |</i>[\s\S]*", '')
  $script:langList = ((($result.Content -replace '[\s\S]*<option value="" selected="selected">Choose one</option>|</select>[\s\S]*|<option value=.{|}.>.*<\/option>|&quot;', '') -replace 'id:', 'skuId=') -replace 'language:', '&language=')
  return 0
}

function identProduct {
  $appendVer=""

  #Windows 10 identi}cation
  if ($productID -ge 75 -and $productID -le 82) {$appendVer=" (Threshold 1)"}
  if ($productID -ge 99 -and $productID -le 106) {$appendVer=" (Threshold 2)"}
  if ($productID -ge 109 -and $productID -le 116) {$appendVer=" (Threshold 2, February 2016 Update)"}
  if ($productID -ge 178 -and $productID -le 185) {$appendVer=" (Threshold 2, April 2016 Update)"}
  if ($productID -ge 242 -and $productID -le 247) {$appendVer=" (Redstone 1)"}
  if ($productID -ge 361 -and $productID -le 372) {$appendVer=" (Redstone 2)"}
  if ($productID -ge 484 -and $productID -le 489) {$appendVer=" (Redstone 3)"}
  if ($productID -ge 637 -and $productID -le 641) {$appendVer=" (Redstone 4)"}
  if ($productID -ge 1019 -and $productID -le 1021) {$appendVer=" (Redstone 5)"}
  if ($productID -ge 1202 -and $productID -le 1204) {$appendVer=" (Redstone 5, October 2019 Refresh)"}
  if ($productID -ge 2069 -and $productID -le 2070) {$appendVer=" (21H2 Original release)"}

  return $appendVer
}

#####################################
# Web generator section (json) #
#####################################

function writeJson {
  $appendVer = identProduct

  Write-Output "        ""$productID"": ""$productName$appendVer""," >> "$Path"
  if ($? -ne 0) {
    return 1
  }

  return 0
}

function main {
  $SessionId = SessionIDInit
  if ($Continue) {((Get-Content $Path -Raw) -replace '{[\s\S]*{|\s*}[\s\S]*', '') -replace '"$', '",' > $dump}
  if (Test-Path $Path -PathType Leaf) {Move-Item -Path "$Path" -Destination "$BackupPath" -Force}
  Write-Output "{`n    ""genTime"":$time,`n    ""productNumber"":""!!productsNumberPlaceholder!!"",`n    ""products"":{" > $Path
  if ($Continue) {Write-Output $dump > "$Path"}
  Write-Host "`n$infoHead Checking for languages using Product ID..."
  
  $result = Invoke-WebRequest -Uri "$getLangUrl" -SessionVariable $Session -HttpVersion 3.0 -SslProtocol Tls12 -Headers $Headers -UseBasicParsing

  if($productsFound -lt 0) {$script:productsFound=0}

  for ($productID = $minProdID; $productID -le $maxProdID; $productID++) {
    Write-Host "$infoHead Checking product ID: $productID"
    $getLangErr=2
    while ($getLangErr -gt 1) {
      $getLangErr=getLangs
      if ($getLangErr -eq 0) {
        Write-Host "$infoHead Got language list!"			
        Write-Host "$infoHead Writing..."
        $writeErr = writeJson				
        $script:productsFound=($productsFound+1)
        Write-Host "$infoHead OK!"
        }
      elseif ($getLangErr -eq 1) {
        Write-Host "$errorHead Product does not exist!"
      }
    }
    Write-Host ""
  }

  $dump = (Get-Content "$Path") -replace "!!productsNumberPlaceholder!!", "$productsFound"
  $dump[-1] = $dump[-1] -replace ',', "`n    `}`n`}"
  Write-Output $dump > "$Path"
  if (Test-Path $BackupPath -PathType Leaf) {Remove-Item "$BackupPath" -Force}

  return 0
}

#######################
# Main script section #
#######################

"$infoHead TechBench dump script (tbdump-web $ver)"
"$infoHead Using Product ID range from $minProdID to $maxProdID"
$Err = main
"$infoHead Number of products: $productsFound"
"$infoHead Done"
exit $Err
