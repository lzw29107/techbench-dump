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

$websiteVersion = '2.2.2.2';
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

        <title>'.$pageTitle.'</title>

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <style>
        body{font-family: "Segoe UI", "Microsoft JhengHei", "Helvetica Neue", Helvetica, Arial, sans-serif; padding-top: 50px;}
        .content {padding: 30px 15px;}
        .prod-btn {margin-top: 18px;}
        .prod-btn-title {text-align: left; white-space: normal;}
        .prod-btn-desc {text-align: left; font-size: 65%; opacity: 0.75; white-space: normal;}
        .dropd-toggle-btn-container {position: absolute; width: 38px; right: 15px; top: 0px; height: 100%; z-index: 1;}
        .dropd-toggle-btn {position: absolute; width: 30px; right: 8px; bottom: 8px; top: 8px; z-index: 1; padding-left: 0px; padding-right: 0px;}
        .dropd-parent-btn {padding-right: 46px; z-index: 0;}
        </style>

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
                        <li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" role="button">'. $translation['moreMenu'] . ' <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="https://gist.github.com/mkuba50/27c909501cbc2a4f169be4b4075a66ff">'.$translation['githubGist'].'</a></li>
                                <li><a href="https://github.com/techbench-dump/script">'.$translation['githubRepoScript'].'</a></li>
                                <li><a href="https://github.com/techbench-dump/website">'.$translation['githubRepoWeb'].'</a></li>
                            </ul>
                        </li>
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
                <p>'.$translation['tbDump'].' v'.$websiteVersion.' &copy; '.date('Y').' <a href="https://forums.mydigitallife.info/threads/72165">The TechBench dump website contributors</a></p>
            </div></div>
        </div><!-- /.container -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    </body>
</html>';
}
?>
