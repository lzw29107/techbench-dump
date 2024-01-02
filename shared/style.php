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

$websiteVersion = '2.10.0';
function styleTop($pageType = 'home') {
    global $s, $langCore_menu;

    switch ($pageType) {
    case 'home':
        $pageTitle = $s['tbDump'];
        $navbarLink1 = '<li class="active"><a href="#">'.$s['homePage'].'</a></li>';
        $navbarLink2 = '<li><a href="./about.php">'.$s['aboutPage'].'</a></li>';
        break;
    case 'downloads':
        $pageTitle = $s['tbDumpDownload'];
        $navbarLink1 = '<li><a href="./">'.$s['homePage'].'</a></li>
                        <li class="active"><a href="#">'.$s['downloads'].'</a></li>';
        $navbarLink2 = '<li><a href="./about.php">'.$s['aboutPage'].'</a></li>';
        break;
    case 'about':
        $pageTitle = $s['tbDump'];
        $navbarLink1 = '<li><a href="./">'.$s['homePage'].'</a></li>';
        $navbarLink2 = '<li class="active"><a href="#">'.$s['aboutPage'].'</a></li>';
        break;
    default:
        $menuBtn = '<li class="active"><a href="#">'.$s['homePage'].'</a></li>';
        break;
    }
    
    $iso639lang = preg_replace("/-.*/i", "", $s['langCode']);

    echo <<<HTML
<!DOCTYPE html>
<html lang="$iso639lang">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        
        <meta property="og:title" content="$pageTitle">
        <meta property="og:type" content="website">
        <meta property="og:description" content="{$s['aboutPageContent']}">
        <meta property="og:image" content="https://i.imgur.com/ES6JymB.png">

        <title>$pageTitle</title>
        <link rel="preconnect" href="https://cdn.jsdelivr.net/" crossorigin>
        <link rel="preload" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/fonts/glyphicons-halflings-regular.woff2" as="font" type="font/woff2" crossorigin />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" fetchpriority="high" crossorigin="anonymous">
        <link rel="stylesheet" href="css/style.css">
        <script defer="defer" src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js" integrity="sha384-1H217gwSVyLSIfaLxHbE7dRb3v4mYCKbpQvzx0cegeju1MVsGrX5xXxAvs/HgeFs" crossorigin="anonymous"></script>
        <script defer="defer" src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js" integrity="sha384-aJ21OjlMXNL5UyIl/XNwTMqvzeRMZH2w8c5cRVpzpU8Y5bApTppSuUkhZXN0VxHd" crossorigin="anonymous"></script>

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>

    <body>

        <nav class="navbar navbar-default navbar-fixed-top">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                        <span class="sr-only">$pageTitle</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="./">{$s['tbDump']}</a>
                </div>
                <div id="navbar" class="collapse navbar-collapse">
                    <ul class="nav navbar-nav navbar-left">
                        $navbarLink1
                        $navbarLink2
                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                        $langCore_menu
                    </ul>
                </div><!--/.nav-collapse -->
            </div>
        </nav>

        <div class="container">

            <div class="content">
HTML;
}

function styleBottom() {
    global $s, $websiteVersion;

    $copyright = sprintf(
        $s['footerNotice'],
        date('Y'),
        '<a href="https://forums.mydigitallife.net/threads/72165">'.$s['contributors'].'</a>'
    );

    echo <<<HTML
            <div class="footer">
            <hr>
                <p>{$s['tbDump']} v$websiteVersion $copyright</p>
            </div></div>
        </div><!-- /.container -->
    </body>
</html>
HTML;
}    
?>