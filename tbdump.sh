#!/bin/bash

# TechBench dump
# Copyright (C) 2023 Techbench dump website authors and contributors

# Licensed under the Apache License, Version 2.0 (the "License");
# you may not use this file except in compliance with the License.
# You may obtain a copy of the License at

#    http://www.apache.org/licenses/LICENSE-2.0

# Unless required by applicable law or agreed to in writing, software
# distributed under the License is distributed on an "AS IS" BASIS,
# WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
# See the License for the specific language governing permissions and
# limitations under the License.

ver=v2.11.0

minProdID=1
maxProdID=3000

helpUsage="Usage: \n$0 [-f {Path for -f}] [first_id] [last_id]\n$0 [-f {Path for -f}] [-c] [last_id]\n[-f] Path to dump file.\n[-c] Starting dump from existing dump file."

infoHead="[INFO]"
warnHead="[WARNING]"
errorHead="[ERROR]"

#If the Continue value is set to 1, even if -c is not specified, the dump will start from the existing file.
Continue=0

while getopts ':f:c' OPT; do
    case $OPT in
        f) Path="$OPTARG"; let n=$n+2;;
        c) Continue=1; let n=$n+1;;
        h) echo -e "$helpUsage"; exit 1;;
        ?) echo -e "$helpUsage"; exit 1;;
    esac
done

shift "$n"

if [ -z "$Path" ]; then Path="dump.json"; fi

if [ "$Continue" = 1 ]; then
  if [ -n "$1" ]; then
	  let maxProdID=$1+0
  fi
  if [ -f "$Path"'_bak' ]; then mv "$Path"'_bak' "$Path"; fi

  if [ -f "$Path" ]; then
    productNumber=$(head -3 $Path | grep 'productNumber' | sed 's/.*"productNumber":"\(.*\)".*/\1/')
    if [[ -z $productNumber ]] || [[ $productNumber == *[!0-9]* ]]; then echo -e "$errorHead Invalid file."; exit 1; fi
    lastProdId=$(tail -3 $Path | grep ': ' | sed 's/.*"\(.*\)": .*/\1/')
    if [[ -z $lastProdId ]] || [[ $lastProdId == *[!0-9]* ]]; then echo -e "$errorHead Invalid file."; exit 1; fi
    let minProdID=$lastProdId+1
    let productsFound=$productNumber
  else
    echo -e "$errorHead File is not exist."; exit 1;
  fi
else
  if [ -n "$1" -a -z "$2" ]; then echo -e "$helpUsage"; exit; fi

  if [ -n "$1" -a -n "$2" ]; then
	  let minProdID=$1+0
	  let maxProdID=$2+0
  fi
fi
if [ $minProdID -le 0 ]; then echo -e "$errorHead First Product ID needs to be larger than 0."; exit 1; fi
if [ $maxProdID -le 0 ]; then echo -e "$errorHead Last Product ID needs to be larger than 0."; exit 1; fi
if [ $maxProdID -lt $minProdID ]; then echo -e "$errorHead Last Product ID needs to be larger or equal to First Product ID."; exit 1; fi

noProductErr="The product key you entered is invalid or not supported by this site."

#URLs to all needed things
SessionInitUrl="https://vlscppe.microsoft.com/fp/tags.js?org_id=y6jn8c31"
getLangUrl="https://www.microsoft.com/en-us/api/controls/contentinclude/html?pageId=cd06bda8-ff9c-4a6e-912a-b92a21f42526&host=www.microsoft.com&segments=software-download,windows11&query=&action=getskuinformationbyproductedition"
getDownUrl="https://www.microsoft.com/en-us/api/controls/contentinclude/html?pageId=cfa9e580-a81e-4a4b-a846-7b21bf4e2e5b&host=www.microsoft.com&segments=software-download,windows11&query=&action=GetProductDownloadLinksBySku"
refererUrl="https://www.microsoft.com/en-us/software-download/windows11"

if ! type curl > /dev/null; then
	echo "$errorHead This scripts needs cUrl to be installed! Exiting" >&2
	exit
fi

#############################
# Generic functions section #
#############################
function genSessionID {
  time=$(date +%s)
  SessionId='xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'
  for num in $(seq 1 ${#SessionId}); do
      random=$(printf %x $(($(($time + $(($RANDOM % 16)))) % 16)))
      time=$(($time/16))
      str=${SessionId:(($num-1)):1}
      if [ "$str" = "x" ]; then
          SessionId=$(echo $SessionId | sed "s/x/$random/")
      elif [ "$str" = "y" ]; then
          random=$(($random & 3 | 8))
          SessionId=$(echo $SessionId | sed "s/y/$random/")
      fi
  done;
}

function SessionIDInit {
  local result=$(curl -s "$SessionInitUrl&session_id=$SessionId" -H "Referer: $refererUrl")
}

function getLangs {
	local result=$(curl -s "$getLangUrl&sessionid=$SessionId&productEditionId=$1&sdVersion=2" -H "Referer: $refererUrl")

  productName=$(echo "$result" | grep 'The product key is eligible for' | sed 's/.*<i>The product key is eligible for //g;s/<\/i>.*//g')

  if echo "$result" | grep "$noProductErr" > /dev/null; then
		return 1
	fi

	echo "$result" | grep 'option value=.{&quot;id' > "/dev/null"
	if [ $? -ne 0 ]; then
		return 2
	fi

	local result=$(echo "$result" | grep 'option value=.{&quot;id')
	langList=$(echo "$result" | sed 's/.*<option value=.{//g;s/}.>.*<\/option>//g;s/&quot;//g;s/id:/skuId=/g;s/,language:/\&language=/g')
	return 0
}

function identProduct {
	local appendVer=""
	
	#Windows 10 identification
	if [ $productID -ge 75 -a $productID -le 82 ]; then local appendVer=" (Threshold 1)"; fi
	if [ $productID -ge 99 -a $productID -le 106 ]; then local appendVer=" (Threshold 2)"; fi
	if [ $productID -ge 109 -a $productID -le 116 ]; then local appendVer=" (Threshold 2, February 2016 Update)"; fi
	if [ $productID -ge 178 -a $productID -le 185 ]; then local appendVer=" (Threshold 2, April 2016 Update)"; fi
	if [ $productID -ge 242 -a $productID -le 247 ]; then local appendVer=" (Redstone 1)"; fi
  if [ $productID -ge 361 -a $productID -le 372 ]; then local appendVer=" (Redstone 2)"; fi
  if [ $productID -ge 484 -a $productID -le 489 ]; then local appendVer=" (Redstone 3)"; fi
  if [ $productID -ge 637 -a $productID -le 641 ]; then local appendVer=" (Redstone 4)"; fi
  if [ $productID -ge 1019 -a $productID -le 1021 ]; then local appendVer=" (Redstone 5)"; fi
  if [ $productID -ge 1202 -a $productID -le 1204 ]; then local appendVer=" (Redstone 5, October 2019 Refresh)"; fi
  if [ $productID -ge 2069 -a $productID -le 2070 ]; then local appendVer=" (21H2 Original release)"; fi

	echo "$appendVer"
}

#####################################
# Web generator section (json) #
#####################################

function writeJson {
	local appendVer="$(identProduct)"

	echo '       "'"$productID"'": "'"$productName""$appendVer"'"', >> "$Path"
	if [ $? -ne 0 ]; then
		return 1
	fi

	return 0
}

function main {
  genSessionID
  SessionIDInit

	if [ "$Continue" = 1 ]; then cat "$Path" | grep ": " | sed  's/\([^,]\)$/\1,/' > "Techbench dump.tmp"; fi
  if [ -f "$Path" ]; then mv -f "$Path" "$Path"'_bak'; fi
  echo -e '{\n    "genTime":"'$(date "+%s")'",\n    "productNumber":"!!productsNumberPlaceholder!!",\n    "products":{' > $Path
  
  if [ "$Continue" = 1 ]; then cat "Techbench dump.tmp" >> "$Path"; fi
  
	echo -e "\n$infoHead Checking for languages using Product ID..."

  if [ -z $productsFound ]; then productsFound=0; fi

	for productID in $(seq $minProdID $maxProdID); do
		echo "$infoHead Checking product ID: $productID"
		getLangErr=2
		while [ $getLangErr -gt 1 ]; do
			getLangs $productID
			getLangErr=$?
			if [ $getLangErr -eq 0 ]; then
				echo "$infoHead Got language list!"			
				echo "$infoHead Writing..."
				writeJson				
				let productsFound=productsFound+1
				echo "$infoHead OK!"
			elif [ $getLangErr -eq 1 ]; then
				echo "$errorHead Product does not exist!"
			fi
		done;
		echo ""
	done;
	
	sed "$ s/,$/\n    }\n}/;s/!!productsNumberPlaceholder!!/$productsFound/g" "$Path" > "Techbench dump.tmp"
	mv -f "Techbench dump.tmp" "$Path"
  if [ -f "$Path"'_bak' ]; then rm "$Path"'_bak'; fi
	return 0
}

#######################
# Main script section #
#######################

echo "$infoHead TechBench dump script (tbdump-$tbdumpVersion)"
echo "$infoHead Copyright 2023 Techbench dump website authors and contributors

$infoHead Licensed under the Apache License, Version 2.0 (the \"License\");
$infoHead you may not use this file except in compliance with the License.
$infoHead You may obtain a copy of the License at

$infoHead    http://www.apache.org/licenses/LICENSE-2.0

$infoHead Unless required by applicable law or agreed to in writing, software
$infoHead distributed under the License is distributed on an \"AS IS\" BASIS,
$infoHead WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
$infoHead See the License for the specific language governing permissions and
$infoHead limitations under the License."
echo ""
echo "$infoHead Using Product ID range from $minProdID to $maxProdID"
main
echo "$infoHead Number of products: $productsFound"
echo "$infoHead Done"
