#!/bin/bash

# TechBench dump
# Copyright (C) 2017  mkuba50

# This program is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 3 of the License, or
# (at your option) any later version.

# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.

# You should have received a copy of the GNU General Public License
# along with this program.  If not, see <http://www.gnu.org/licenses/>.

minProdID=1
maxProdID=350

legacyGen=0

helpUsage="Usage: $0 generator [first_id] [last_id]

Supported generators:
* html\t\t- generates HTML based dump
* bootstrap\t- generates Bootstrap based HTML dump
* md\t\t- generates markdown based dump
* html_legacy\t- generates HTML based dump (legacy links)
* md_legacy\t- generates markdown based dump (legacy links)"

if [ -z "$1" ]; then
	echo -e "$helpUsage"
	exit
elif [ "$1" = "html" ]; then
	useGen="html"
elif [ "$1" = "md" ]; then
	useGen="md"
elif [ "$1" = "html_legacy" ]; then
	useGen="html"
	legacyGen=1
elif [ "$1" = "md_legacy" ]; then
	useGen="md"
	legacyGen=1
elif [ "$1" = "bootstrap" ]; then
	useGen="bootstrap"
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

tbdumpVersion="master"

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
	
	echo "$appendVer [ID: $productID]"
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
	
	echo "$productName" | sed "s/<h2>/### /g;s/ $tempLang.*<\/h2>/<\/h2>/g;s/<\/h2>/$appendVer/g" >> "Techbench dump.md"
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

function mainMarkdown {
	headerMarkdown
	
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
				writeMarkdown
				
				let productsFound=productsFound+1
				echo "$infoHead OK!"
			elif [ $getLangErr -eq 1 ]; then
				echo "$errorHead Product does not exist!"
			fi
		done;
		echo ""
	done;

	sed s/!!productsNumberPlaceholder!!/$productsFound/g "Techbench dump.md" > "Techbench dump.tmp"
	mv -f "Techbench dump.tmp" "Techbench dump.md"

	return 0
}

####################################
# HTML generator functions section #
####################################

function writeHtml {
	local appendVer="$(identProduct)"
	echo "" >> "Techbench dump.html"
	
	echo "$productName" | sed "s/<h2>/<h3>/g;s/ $tempLang.*<\/h2>/<\/h2>/g;s/<\/h2>/$appendVer<\/h3>/g" >> "Techbench dump.html"

	echo "<ul>" >> "Techbench dump.html"
	echo "$langList" | tr -d '\r' | awk -v url="$getDownUrl" -F'[&=]' '{print "<li><a href=\""url $2"\">"$4"</a></li>"}' >> "Techbench dump.html"
	echo "</ul>" >> "Techbench dump.html"
	return 0
}

function runGenHtml {
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
				writeHtml
				
				let productsFound=productsFound+1
				echo "$infoHead OK!"
			elif [ $getLangErr -eq 1 ]; then
				echo "$errorHead Product does not exist!"
			fi
		done;
		echo ""
	done;
}

function mainHtml {
	echo "<html>" > "Techbench dump.html"
	echo "<head>" >> "Techbench dump.html"
	echo "<title>TechBench dump</title>" >> "Techbench dump.html"
	echo "<style>body{font-family: \"Segoe UI\", \"Tahoma\", \"Arial\", sans-serif; font-size: 10pt} h1{font-weight: 600} h3{font-weight: 600} a{text-decoration: none; color: #0060A5;} a:hover{text-decoration: underline}</style>" >> "Techbench dump.html"
	echo "</head>" >> "Techbench dump.html"
	echo "<body>" >> "Techbench dump.html"
	echo "<h1>TechBench dump</h1>" >> "Techbench dump.html"
	echo "Generated on $(date "+%Y-%m-%dT%H:%M:%S%z") using:<br>" >> "Techbench dump.html"
	echo "- TechBench dump script (tbdump-$tbdumpVersion)<br>" >> "Techbench dump.html"
	echo "- $(uname -mrsio)<br>" >> "Techbench dump.html"
	echo "- $(curl -V | head -n1)<br>" >> "Techbench dump.html"
	echo "" >> "Techbench dump.html"
	echo "<br>Number of products: !!productsNumberPlaceholder!!<br>" >> "Techbench dump.html"

	runGenHtml
	
	echo "</body>" >> "Techbench dump.html"
	echo "</html>" >> "Techbench dump.html"

	sed s/!!productsNumberPlaceholder!!/$productsFound/g "Techbench dump.html" > "Techbench dump.tmp"
	mv -f "Techbench dump.tmp" "Techbench dump.html"

	return 0
}

##########################################
# Bootstrap based HTML generator section #
##########################################

function headerBootstrap {

	echo '<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>TechBench dump</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <style>body{font-family: "Segoe UI", "Helvetica Neue", Helvetica, Arial, sans-serif; padding-top: 50px;} .content {padding: 30px 15px;} .modal-content {padding: 20px;}</style>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>

    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">TechBench dump</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">TechBench dump</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li class="active"><a href="#">Home</a></li>
            <li><a href="https://gist.github.com/mkuba50/27c909501cbc2a4f169be4b4075a66ff">Gist</a></li>
            <li><a href="https://github.com/mkuba50/techbench-dump">GitHub repository</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>

    <div class="container">

      <div class="content">' > "Techbench dump.html"
	echo "<h1>TechBench dump</h1>" >> "Techbench dump.html"
	echo "<pre><code>Generated on $(date "+%Y-%m-%dT%H:%M:%S%z") using:" >> "Techbench dump.html"
	echo "- TechBench dump script (tbdump-$tbdumpVersion)" >> "Techbench dump.html"
	echo "- $(uname -mrsio)" >> "Techbench dump.html"
	echo "- $(curl -V | head -n1)" >> "Techbench dump.html"
	echo "" >> "Techbench dump.html"
	echo "Number of products: !!productsNumberPlaceholder!!</code></pre>" >> "Techbench dump.html"
}

function footerBootstrap {
	echo '      </div>

    </div><!-- /.container -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
  </body>
</html>' >> "Techbench dump.html"
}

function mainBootstrap {
	headerBootstrap
	runGenHtml
	footerBootstrap

	sed s/!!productsNumberPlaceholder!!/$productsFound/g "Techbench dump.html" > "Techbench dump.tmp"
	mv -f "Techbench dump.tmp" "Techbench dump.html"

	return 0
}

#######################
# Main script section #
#######################

echo "$infoHead TechBench dump script (tbdump-$tbdumpVersion)"
echo "$infoHead Using Product ID range from $minProdID to $maxProdID"

if [ "$useGen" = "html" ]; then
	echo "$infoHead Using HTML generator"
	mainHtml
elif [ "$useGen" = "md" ]; then
	echo "$infoHead Using Markdown generator"
	mainMarkdown
elif [ "$useGen" = "bootstrap" ]; then
	echo "$infoHead Using Bootstrap based HTML generator"
	mainBootstrap
fi

echo "$infoHead Number of products: $productsFound"
echo "$infoHead Done"
