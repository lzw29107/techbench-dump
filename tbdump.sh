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
maxProdID=575

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

tbdumpVersion="21"

infoHead="[INFO]"
warnHead="[WARNING]"
errorHead="[ERROR]"

noProductErr="The product key you entered is invalid or not supported by this site"
prodInfoErr="We encountered a problem processing your request."

#URLs to all needed things
getLangUrlClean="http://www.microsoft.com/en-us/api/controls/contentinclude/html?pageId=cd06bda8-ff9c-4a6e-912a-b92a21f42526&host=www.microsoft.com&segments=software-download,windows10ISO&query=&action=getskuinformationbyproductedition"
getDownUrlLongClean="http://www.microsoft.com/en-us/api/controls/contentinclude/html?pageId=cfa9e580-a81e-4a4b-a846-7b21bf4e2e5b&host=www.microsoft.com&segments=software-download,windows10ISO&query=&action=GetProductDownloadLinksBySku"
refererUrl="https://www.microsoft.com/en-us/software-download/windows10ISO"

#Fix redirection on Windows and warn user, that Control-C is broken
if [ "$WIN_WRAPPED" == "1" ]; then
	nullRedirect="NUL"
	echo -e "$warnHead Control-C does not work when using this script on Windows!\n"
else
	nullRedirect="/dev/null"
fi

if ! type curl > $nullRedirect; then
	echo "$errorHead This scripts needs cUrl to be installed! Exiting" >&2
	exit
fi

#############################
# Generic functions section #
#############################

function getLangs {
	langsPage=$(curl -s "$getLangUrl&productEditionId=$1" -H "Referer: $refererUrl")
	local result="$langsPage"

	if echo "$result" | grep "$noProductErr" > $nullRedirect; then
		return 1
	fi

	echo "$result" | grep 'option value=.{&quot;id' > $nullRedirect
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
	if [ $productID -ge 484 -a $productID -le 489 ]; then local appendVer=" (Redstone 3)"; fi

	echo "$appendVer"
}

function getProductName {
	local tempLine=$(printf "$langList" | tail -n1 | tr -d '\r')
	local tempLink=$(printf "$tempLine" | sed s/.language=.*//g)

	local result=$(curl -s "$getDownUrlLong&$(echo -n $tempLink)" -H "Referer: $refererUrl")
	local result2="$langsPage"

	productName=$(echo "$result2" | grep -o '<i>The product key is eligible for.*<\/i>' | sed 's/The product key is eligible for //g')

	if [ "$productName" == "<i></i>" ]; then
		echo "$warnHead Got empty product name!"
		productName="<i>Unknown</i>"
	fi

	if echo "$result" | grep "$prodInfoErr" > $nullRedirect; then
		if echo "$productName" | grep -E "Windows.*?Insider.?Preview" > $nullRedirect; then
			return 0
		fi
		return 1
	fi

	return 0
}

function uuidGen {
local tmp
local i
for i in $(seq 1 32); do
	let tmp=$RANDOM%16

	if [ $tmp == 10 ]; then tmp='a'; fi
	if [ $tmp == 11 ]; then tmp='b'; fi
	if [ $tmp == 12 ]; then tmp='c'; fi
	if [ $tmp == 13 ]; then tmp='d'; fi
	if [ $tmp == 14 ]; then tmp='e'; fi
	if [ $tmp == 15 ]; then tmp='f'; fi
	printf "$tmp"

	if [ $i == 8 -o $i == 12 -o $i == 16 -o $i == 20 ]; then printf "-"; fi
done
}

########################################
# HTML generator functions section #
########################################

function writeHTML {
	local appendVer="$(identProduct)"
	echo "$productName" | sed "s/<i>/<option value=\"$productID\">/g;s/<\/i>/$appendVer [ID: $productID]<\/option>/g" >> "dump.html"
	if [ $? -ne 0 ]; then
		return 1
	fi
	return 0
}

function headerHTML {
	echo '<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">

        <script type="text/javascript" src="https://ajax.aspnetcdn.com/ajax/jQuery/jquery-1.9.1.min.js"></script>
        <script type="text/javascript" src="https://c.s-microsoft.com/en-us/CMSScripts/script.jsx?k=517a7087-9636-e078-8b13-a173049192f5_4c905457-169a-061a-e153-0372577f2998_742bd11f-3d7c-9955-3df5-f02b66689699_525283c5-3d35-4dd2-5a96-acaf933fab61_49488e0d-6ae2-5101-c995-f4d56443b1d8_7dea7b90-4334-c043-b252-9f132d19ee19_38aa9ffb-ddb5-75be-6536-a58628f435f5_e3e65a0a-c133-43e7-571d-2293e03f85e6_4ca0e9dc-a4de-17ba-f0de-d1d346cb99e2_06310cd8-41c6-3b11-4645-b4884789ed70_5c27e8aa-9347-969e-39ac-37a4de428a8d_bedcf502-0395-ae0a-d3d4-b72978f0a6d9_be92d794-4118-193f-9871-58b72092a5ac_64c742e2-b29c-b6c1-fdd9-accf33ec40bd_cf2ceca9-3467-a5b3-d095-68958eee6d4c_cec39dd8-f1d3-56f1-abfc-a7db34ff7b46_ec5fa2c9-3950-ff57-a5c3-1fa77e0db190_d19f9592-65df-bcc9-e30e-439b875c3381_76a3d06f-f11f-77ef-9bfd-6227ba750200_28ef6180-55bc-102c-3ba8-678e92875e6b_c2dceda8-20b4-7d3f-13b6-9cac67d7df17_914fa41b-cc86-d3b0-4e15-2fdfa357bcc7_40c6c884-da6e-7c2c-081f-4a7dfe7c7245_35f9df4f-1b4f-752c-4522-e2f2a8d2a77f_dd708766-2c4c-f068-79b0-121081b8621c_a5201e55-aa32-d778-3300-0a557fd39f8c_26d1ef17-d0f5-2db9-fe2d-ced935bb409f_8653737a-ece8-1b56-0c26-ac582c3738d4_ef37e36f-3037-c8f0-eaa1-a5f4a643fc0d_f8a0d07f-49e8-dca2-07d3-a7b0861d21f9_1fa77585-d5dc-d975-bd87-48d017a6c87e"></script>

        <style>
            body {
                background-color: #fff;
            }

            body, .button-purple, #product-languages, #product-edition {
                font-family: "Segoe UI", Tahoma, Arial;
                font-size: 9pt;
            }

            p, h2 {
                margin-top: 0.5em;
                margin-bottom: 0.5em;
            }

            .title-text {
                margin-top: -1em;
                margin-bottom: 1em;
            }

            .row-padded {
                padding-bottom: 0.5em;
            }

            .button-purple {
                display: inline-block;
                color: #fff;
                background-color: #4d3cb5;
                cursor: default;
                border: 0px;
                border-radius: 4px;
                padding: 0.5em 2em 0.5em 2em;
                margin-bottom: 0.5em !important;
                margin-right: 0.25em;
                text-align: center;
                text-decoration: none;
                transition: background-color 150ms;
            }

            .button-purple:hover {
                background-color: #7264cd;
            }

            #progress-modal {
                display: none;
                position: fixed;
                background-color: #000;
                color: #fff;
                top: 0px;
                padding: 25px;
                left: 0px;
                width: 100%;
            }

            #product-languages, #product-edition {
                margin-top: 0.25em;
                margin-bottom: 1em;
                min-width: 100%;
                color: #000;
                background-color: #fff;
                cursor: default;
                border: 1px;
                border-color: #aaa;
                border-style: solid;
                border-radius: 4px;
                padding: 0.5em;
                text-decoration: none;
                transition: border-color 150ms;
            }

            #product-languages:hover, #product-edition:hover {
                border-color: #555;
            }

            #product-languages-error {
                font-weight: bold;
            }
        </style>

        <title>TechBench Minidump</title>
    </head>

    <body>
        <h1>TechBench Minidump</h1>
        <p class="title-text"><i>Generated using TechBench dump script (tbdump-'$tbdumpVersion')</i></p>

        <p>Last update: <b>'$(date "+%Y-%m-%dT%H:%M:%S%z")'</b><br>
        Number of products: <b>!!productsNumberPlaceholder!!</b></p>

        <h2>Select product</h2>
        <p>Select an edition from the drop down menu. To be able to successfully retrieve download links for Windows Insider products you need to be logged on <b><a href="https://www.microsoft.com/en-us/software-download/windowsinsiderpreviewadvanced">Windows Insider page</a></b>.</p>

        <select id="product-edition" href="#product-info-content">
            <option value="" selected="selected">Select edition</option>' > "dump.html"
}

function footerHTML {
	echo '        </select>
        <br>
        <button class="button-flat button-purple button-main" id="submit-product-edition">Confirm</button>

        <div id="progress-modal"><p><center>Please wait...</center></p></div>

        <div class="row-fluid" data-cols="1" data-view1="1" data-view2="1" data-view3="1" data-view4="1">
            <div id="SoftwareDownload_LanguageSelectionByProductEdition" class="mscom-ajax-contentinclude"
            data-defaultPageId="cd06bda8-ff9c-4a6e-912a-b92a21f42526" data-urlLocale="en-us" data-ProgrammableContentArea=""
            data-ControlAttributesMapping="" data-Host="www.microsoft.com" data-host-segments="software-download%2cwindows10ISO"
            data-host-querystring="" data-AjaxQuery=""></div>
        </div>

        <div class="row-fluid" data-cols="1" data-view1="1" data-view2="1" data-view3="1" data-view4="1">
            <div id="SoftwareDownload_DownloadLinks" class="mscom-ajax-contentinclude"
            data-defaultPageId="cfa9e580-a81e-4a4b-a846-7b21bf4e2e5b" data-urlLocale="en-us" data-ProgrammableContentArea=""
            data-ControlAttributesMapping="" data-Host="www.microsoft.com" data-host-segments="software-download%2cwindows10ISO"
            data-host-querystring="" data-AjaxQuery=""></div>
        </div>

        <script type="text/javascript">
            MSCom.CMS.Mashup.ContentInclude=function(n,t,i,r,u,f,e,o){e||(e="");this._url="https://www.microsoft.com/api/controls/contentinclude/"+e;this._collection=getQueryValue(window.location.href,"CollectionId");this._locale=i;this._pageId=t;this._ppaId=r;this._controlAttributeMapping=u;this._siteContextName=f;this._action=e;this._query=o};MSCom.CMS.Mashup.ContentInclude.prototype={render:function(n){var t,i;this._divToRender=n;t=this._url+"?locale="+this._locale+"&pageId="+this._pageId+"&site="+this._siteContextName;this._collection&&(t+="&CollectionId="+this._collection);this._ppaId&&(t+="&ProgrammableContentArea="+this._ppaId);for(i in this._query)
            t+="&"+i+"="+this._query[i];$.ajax({type:"POST",url:t,data:{controlAttributeMapping:this._controlAttributeMapping},xhrFields:{withCredentials:!0},success:function(t){t!=null&&$(n).html(t)}})}};MSCom.CMS.Mashup.ContentInclude2=function(n,t,i){i||(i="html");this._locale=n.attr("data-urllocale");this._url="https://www.microsoft.com/"+this._locale+"/api/controls/contentinclude/"+i;this._collection=this.getQueryValue(window.location.href,"CollectionId");this._pageId=n.attr("data-defaultPageId");this._ppaId=n.attr("data-ProgrammableContentArea");this._host=n.attr("data-Host");this._hostsegments=n.attr("data-host-segments");this._hostquery=n.attr("data-host-querystring");this._controlAttributeMapping=n.attr("data-ControlAttributesMapping");this._action=i;var r=n.attr("data-ajaxQuery");r&&(this._query=JSON.parse(r));this._divToRender=n};MSCom.CMS.Mashup.ContentInclude2.prototype={getQueryValue:function(n,t){var r=new RegExp("[\\?&]"+t+"=([^&#]*)","gi"),i=r.exec(n);return i==null?"":decodeURIComponent(i[1].replace(/\+/g," "))},render:function(n,t,i){var e=this._divToRender,r=this._url+"?pageId="+this._pageId+"&host="+this._host+"&segments="+this._hostsegments+"&query="+this._hostquery,u,f;this._collection&&(r+="&CollectionId="+this._collection);this._ppaId&&(r+="&ProgrammableContentArea="+this._ppaId);for(u in this._query)
            r+="&"+u+"="+this._query[u];f={type:"POST",url:r,data:{controlAttributeMapping:this._controlAttributeMapping},xhrFields:{withCredentials:!0},success:function(t){t!=null&&(e.html(t),n&&n())},error:function(n,i,r){t&&t(n,i,r)}};i&&(f.timeout=i);$.ajax(f)}};
        </script>
    </body>
</html>' >> "dump.html"
}

#####################################
# Web generator section (json + md) #
#####################################

function writeJson {
	local appendVer="$(identProduct)"

	echo "$productName" | sed "s/<i>/\"$productID\":\"/g;s/<\/i>/$appendVer\",/g" >> "dump.json"
	if [ $? -ne 0 ]; then
		return 1
	fi

	return 0
}

function mainWeb {
	headerHTML

	echo '{"genTime":"'$(date "+%s")'","productNumber":"!!productsNumberPlaceholder!!","products":{' > dump.json

	echo -e "\n$infoHead Checking for languages using Product ID..."

	productsFound=0

	for productID in $(seq $minProdID $maxProdID); do
		echo "$infoHead Checking product ID: $productID"

		uuid=$(uuidGen)
		getLangUrl="$getLangUrlClean&sessionId=$uuid"
		getDownUrlLong="$getDownUrlLongClean&sessionId=$uuid"
		echo "$infoHead Using UUID: $uuid"

		getLangErr=2
		while [ $getLangErr -gt 1 ]; do
			getLangs $productID
			getLangErr=$?
			if [ $getLangErr -eq 0 ]; then
				echo "$infoHead Got language list!"
				getErr=2
				while [ $getErr -gt 1 ]; do
					getProductName
					getErr=$?
				done;

				if [ $getErr -eq 1 ]; then
					echo "$errorHead Error in product info!"
				else
					echo "$infoHead Writing..."
					writeJson
					writeHTML

					let productsFound=productsFound+1
					echo "$infoHead OK!"
				fi

			elif [ $getLangErr -eq 1 ]; then
				echo "$errorHead Product does not exist!"
			fi
		done;
		echo ""
	done;

	footerHTML

	sed "$ s/,$/}}/;s/!!productsNumberPlaceholder!!/$productsFound/g" "dump.json" | tr -d '\n' > "Techbench dump.tmp"
	mv -f "Techbench dump.tmp" "dump.json"

	sed s/!!productsNumberPlaceholder!!/$productsFound/g "dump.html" > "Techbench dump.tmp"
	mv -f "Techbench dump.tmp" "dump.html"

	return 0
}

#######################
# Main script section #
#######################

echo "$infoHead TechBench dump script (tbdump-$tbdumpVersion)"
echo "$infoHead Copyright 2017 mkuba50

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

if [ "$useGen" = "web" ]; then
	echo "$infoHead Using generator for Web"
	mainWeb
fi

echo "$infoHead Number of products: $productsFound"
echo "$infoHead Done"
