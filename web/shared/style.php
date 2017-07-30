<?php
// Copyright 2017 mkuba50

// Licensed under the Apache License, Version 2.0 (the "License");
// you may not use this file except in compliance with the License.
// You may obtain a copy of the License at

//    http://www.apache.org/licenses/LICENSE-2.0

// Unless required by applicable law or agreed to in writing, software
// distributed under the License is distributed on an "AS IS" BASIS,
// WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
// See the License for the specific language governing permissions and
// limitations under the License.

$websiteVersion = '2.5.1';
function styleTop($pageType = 'home') {
    global $translation, $langCore_menu, $langParam;

    switch ($pageType) {
    case 'home':
        $pageTitle = $translation['tbDump'];
        $navbarLink1 = '<li class="active"><a href="#">'.$translation['homePage'].'</a></li>';
        $navbarLink2 = '<li><a href="./about.php?'.$langParam.'">'.$translation['aboutPage'].'</a></li>';
        break;
    case 'downloads':
        $pageTitle = $translation['tbDumpDownload'];
        $navbarLink1 = '<li><a href="./?'.$langParam.'">'.$translation['homePage'].'</a></li>
                        <li class="active"><a href="#">'.$translation['downloads'].'</a></li>';
        $navbarLink2 = '<li><a href="./about.php?'.$langParam.'">'.$translation['aboutPage'].'</a></li>';
        break;
    case 'about':
        $pageTitle = $translation['tbDump'];
        $navbarLink1 = '<li><a href="./?'.$langParam.'">'.$translation['homePage'].'</a></li>';
        $navbarLink2 = '<li class="active"><a href="#">'.$translation['aboutPage'].'</a></li>';
        break;
    default:
        $menuBtn = '<li class="active"><a href="#">'.$translation['homePage'].'</a></li>';
        break;
    }

    echo '<!DOCTYPE html>
<html>
    <!--
    Copyright 2017 mkuba50

    Licensed under the Apache License, Version 2.0 (the "License");
    you may not use this file except in compliance with the License.
    You may obtain a copy of the License at

        http://www.apache.org/licenses/LICENSE-2.0

    Unless required by applicable law or agreed to in writing, software
    distributed under the License is distributed on an "AS IS" BASIS,
    WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
    See the License for the specific language governing permissions and
    limitations under the License.
    -->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <meta property="og:title" content="'.$pageTitle.'">
        <meta property="og:type" content="website">
        <meta property="og:description" content="'.$translation['aboutPageContent'].'">
        <meta property="og:image" content="http://i.imgur.com/ES6JymB.png">

        <title>'.$pageTitle.'</title>

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
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
                    <a class="navbar-brand" href="./?'.$langParam.'">'.$translation['tbDump'].'</a>
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
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    </body>
</html>';
}
?>
