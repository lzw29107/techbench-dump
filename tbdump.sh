#!/bin/bash

# Copyright 2017 mkuba50

# Licensed under the Apache License, Version 2.0 (the "License");
# you may not use this file except in compliance with the License.
# You may obtain a copy of the License at

#    http://www.apache.org/licenses/LICENSE-2.0

# Unless required by applicable law or agreed to in writing, software
# distributed under the License is distributed on an "AS IS" BASIS,
# WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
# See the License for the specific language governing permissions and
# limitations under the License.

minProdID=1
maxProdID=400

legacyGen=0

helpUsage="Usage: $0 generator [first_id] [last_id]

Supported generators:
* web\t\t- generates dump for web purposes"

if [ -z "$1" ]; then
	echo -e "$helpUsage"
	exit
elif [ "$1" = "web" ]; then
	useGen="web"
else
	echo "Unknown generator specified"
	exit
fi

if [ -n "$2" -a -z "$3" ]; then echo -e "$helpUsage"; exit; fi

if [ -n "$2" -a -n "$3" ]; then
	let minProdID=$2+0
	let maxProdID=$3+0
	
	if [ $minProdID -le 0 ]; then echo "First Product ID needs to be larger than 0"; exit 1; fi
	if [ $maxProdID -le 0 ]; then echo "Last Product ID needs to be larger than 0"; exit 1; fi
	if [ $maxProdID -lt $minProdID ]; then echo "Last Product ID needs to be larger or equal to First Product ID"; exit 1; fi
fi

tbdumpVersion="web"

infoHead="[INFO]"
warnHead="[WARNING]"
errorHead="[ERROR]"

noProductErr="The product key you provided is for a product not currently supported by this site or may be invalid"

#URLs to all needed things
getLangUrl="https://www.microsoft.com/en-us/api/controls/contentinclude/html?pageId=a8f8f489-4c7f-463a-9ca6-5cff94d8d041&host=www.microsoft.com&segments=software-download,windows10ISO&query=&action=getskuinformationbyproductedition"
getDownUrlLong="https://www.microsoft.com/en-us/api/controls/contentinclude/html?pageId=cfa9e580-a81e-4a4b-a846-7b21bf4e2e5b&host=www.microsoft.com&segments=software-download,windows10ISO&query=&action=GetProductDownloadLinksBySku"
getDownUrlShort="https://mdl-tb.ct8.pl/get.php"
refererUrl="https://www.microsoft.com/en-us/software-download/windows10ISO"

if ! type curl > /dev/null; then
	echo "$errorHead This scripts needs cUrl to be installed! Exiting" >&2
	exit
fi

if [ $legacyGen -eq 1 ]; then
	getDownUrl="$getDownUrlLong&skuId="
else
	getDownUrl="$getDownUrlShort?skuId="
fi

#############################
# Generic functions section #
#############################

function getLangs {
	local result=$(curl -s "$getLangUrl&productEditionId=$1" -H "Referer: $refererUrl")

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
	if [ $productID -ge 361 -a $productID -le 364 ]; then local appendVer=" (Redstone 2)"; fi
	
	echo "$appendVer"
}

function getProductName {
	local tempLine=$(printf "$langList" | tail -n1 | tr -d '\r')
	local tempLink=$(printf "$tempLine" | sed s/.language=.*//g)
	tempLang=$(printf "$tempLine" | awk -F'[&= ]' '{print $4}')

	local result=$(curl -s "$getDownUrlLong&$(echo -n $tempLink)" -H "Referer: $refererUrl")

	echo "$result" | grep "Choose a link below to begin the download" > /dev/null
	if [ $? -ne 0 ]; then
		return 1
	fi

	productName=$(echo "$result" | grep -o '<h2>.*<\/h2>' | sed 's/.*<h2>/<h2>/g')
	
	if [ "$productName" == "<h2></h2>" ]; then
		echo "$warnHead Got empty product name!"
		productName=$(echo "$result" | grep -o "https:..software.*\/pr\/.*\?t=" | sed "s/.*https.*\/pr\//<h2>/g;s/\?t=/ [?]<\/h2>/g")
	fi
	
	return 0
}

########################################
# Markdown generator functions section #
########################################

function writeMarkdown {
	local appendVer="$(identProduct)"
	echo "" >> "Techbench dump.md"
	
	echo "$productName" | sed "s/<h2>/### /g;s/ $tempLang.*<\/h2>/<\/h2>/g;s/<\/h2>/$appendVer [ID: $productID]/g" >> "Techbench dump.md"
	if [ $? -ne 0 ]; then
		return 1
	fi

	echo "" >> "Techbench dump.md"
	echo "$langList" | tr -d '\r' | awk -v url="$getDownUrl" -F'[&=]' '{print "* ["$4"]("url $2")"}' >> "Techbench dump.md"
	return 0
}

function headerMarkdown {
	echo "# TechBench dump" > "Techbench dump.md"
	echo "" >> "Techbench dump.md"
	echo '```' >> "Techbench dump.md"
	echo "Generated on $(date "+%Y-%m-%dT%H:%M:%S%z") using:" >> "Techbench dump.md"
	echo "- TechBench dump script (tbdump-$tbdumpVersion)" >> "Techbench dump.md"
	echo "- $(uname -mrsio)" >> "Techbench dump.md"
	echo "- $(curl -V | head -n1)" >> "Techbench dump.md"
	echo "" >> "Techbench dump.md"
	echo "Number of products: !!productsNumberPlaceholder!!" >> "Techbench dump.md"
	echo '```' >> "Techbench dump.md"
}

#####################################
# Web generator section (json + md) #
#####################################

function writeJson {
	local appendVer="$(identProduct)"

	echo "$productName" | sed "s/<h2>/\"$productID\":\"/g;s/ $tempLang.*<\/h2>/<\/h2>/g;s/<\/h2>/$appendVer\",/g" >> "dump.json"
	if [ $? -ne 0 ]; then
		return 1
	fi

	return 0
}

function mainWeb {
	headerMarkdown
	
	echo '{"genTime":"'$(date "+%s")'","productNumber":"!!productsNumberPlaceholder!!","products":{' > dump.json
	
	echo -e "\n$infoHead Checking for languages using Product ID..."
	
	productsFound=0

	for productID in $(seq $minProdID $maxProdID); do
		echo "$infoHead Checking product ID: $productID"
		getLangErr=2
		while [ $getLangErr -gt 1 ]; do
			getLangs $productID
			getLangErr=$?
			if [ $getLangErr -eq 0 ]; then
				echo "$infoHead Got language list!"
				getErr=1
				while [ $getErr -ne 0 ]; do
					getProductName
					getErr=$?
				done;
				
				echo "$infoHead Writing..."
				writeJson
				writeMarkdown
				
				let productsFound=productsFound+1
				echo "$infoHead OK!"
			elif [ $getLangErr -eq 1 ]; then
				echo "$errorHead Product does not exist!"
			fi
		done;
		echo ""
	done;
	
	sed "$ s/,$/}}/;s/!!productsNumberPlaceholder!!/$productsFound/g" "dump.json" | tr -d '\n' > "Techbench dump.tmp"
	mv -f "Techbench dump.tmp" "dump.json"
	
	sed s/!!productsNumberPlaceholder!!/$productsFound/g "Techbench dump.md" > "Techbench dump.tmp"
	mv -f "Techbench dump.tmp" "Techbench dump.md"

	return 0
}

#######################
# Main script section #
#######################

echo "$infoHead TechBench dump script (tbdump-$tbdumpVersion)"
echo "$infoHead Copyright 2017 mkuba50

$infoHead Licensed under the Apache License, Version 2.0 (the "License");
$infoHead you may not use this file except in compliance with the License.
$infoHead You may obtain a copy of the License at

$infoHead    http://www.apache.org/licenses/LICENSE-2.0

$infoHead Unless required by applicable law or agreed to in writing, software
$infoHead distributed under the License is distributed on an "AS IS" BASIS,
$infoHead WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
$infoHead See the License for the specific language governing permissions and
$infoHead limitations under the License."
echo ""
echo "$infoHead Using Product ID range from $minProdID to $maxProdID"

if [ "$useGen" = "web" ]; then
	echo "$infoHead Using generator for Web"
	mainWeb
fi

echo "$infoHead Number of products: $productsFound"
echo "$infoHead Done"
