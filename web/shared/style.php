<?php
/*
Copyright 2019 whatever127

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

$websiteVersion = '2.9.1';
function styleTop($pageType = 'home') {
    global $translation, $langCore_menu;

    switch ($pageType) {
    case 'home':
        $pageTitle = $translation['tbDump'];
        $navbarLink1 = '<li class="active"><a href="#">'.$translation['homePage'].'</a></li>';
        $navbarLink2 = '<li><a href="./about.php">'.$translation['aboutPage'].'</a></li>';
        break;
    case 'downloads':
        $pageTitle = $translation['tbDumpDownload'];
        $navbarLink1 = '<li><a href="./">'.$translation['homePage'].'</a></li>
                        <li class="active"><a href="#">'.$translation['downloads'].'</a></li>';
        $navbarLink2 = '<li><a href="./about.php">'.$translation['aboutPage'].'</a></li>';
        break;
    case 'about':
        $pageTitle = $translation['tbDump'];
        $navbarLink1 = '<li><a href="./">'.$translation['homePage'].'</a></li>';
        $navbarLink2 = '<li class="active"><a href="#">'.$translation['aboutPage'].'</a></li>';
        break;
    default:
        $menuBtn = '<li class="active"><a href="#">'.$translation['homePage'].'</a></li>';
        break;
    }

    echo '<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <meta property="og:title" content="'.$pageTitle.'">
        <meta property="og:type" content="website">
        <meta property="og:description" content="'.$translation['aboutPageContent'].'">
        <meta property="og:image" content="http://i.imgur.com/ES6JymB.png">

        <title>'.$pageTitle.'</title>

        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
        <link rel="stylesheet" href="shared/style.css">

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
                        <span class="sr-only">'.$pageTitle.'</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="./">'.$translation['tbDump'].'</a>
                </div>
                <div id="navbar" class="collapse navbar-collapse">
                    <ul class="nav navbar-nav navbar-left">
                        '.$navbarLink1.'
                        '.$navbarLink2.'
                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                        '.$langCore_menu.'
                    </ul>
                </div><!--/.nav-collapse -->
            </div>
        </nav>

        <div class="container">

            <div class="content">';
}

function styleBottom() {
    global $translation, $websiteVersion;
    echo '
            <div class="footer">
            <hr>
                <p>'.$translation['tbDump'].' v'.$websiteVersion.' &copy; '.date('Y').' '.$translation['footerNotice'].'</p>
            </div></div>
        </div><!-- /.container -->
        <script src="https://code.jquery.com/jquery-1.12.4.min.js" integrity="sha384-nvAa0+6Qg9clwYCGGPpDQLVpLNn0fRaROjHqs13t4Ggj3Ez50XnGQqc/r8MhnRDZ" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js" integrity="sha384-aJ21OjlMXNL5UyIl/XNwTMqvzeRMZH2w8c5cRVpzpU8Y5bApTppSuUkhZXN0VxHd" crossorigin="anonymous"></script>
    </body>
</html>';
}
