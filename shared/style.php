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

$websiteVersion = '3.0.0-preview.0';
function styleTop($pageType = 'home') {
    global $s, $langCore_menu, $select;


    $theme = 'auto';
    if(isset($_COOKIE['theme'])) {
        switch($_COOKIE['theme']) {
            case 'light':
                $theme = 'light';
                break;

            case 'dark':
                $theme = 'dark';
                break;

            default:
                $theme = 'auto';
                break;
        }
    }

    if($theme == 'auto') {
        $themeMode = '<style>@import url(\'css/dark.css\') (prefers-color-scheme: dark);</style>';
        $themeBs = '';
    } elseif($theme == 'light') {
        $themeMode = '';
        $themeBs = 'data-bs-theme="light"';
    } elseif($theme == 'dark') {
        $themeMode = '<link rel="stylesheet" href="css/dark.css">';
        $themeBs = 'data-bs-theme="dark"';
    }

    if($theme == 'dark') {
        $themeIcon = 'moon';
    } else $themeIcon = 'sun';

    switch ($pageType) {
    case 'home':
        $pageTitle = $s['tbDump'];
        $navbarLink1 = '<li class="nav-item ms-2"><a class="nav-link text-nowrap active" aria-current="page" href="#">'.$s['homePage'].'</a></li>
                        <li class="nav-item ms-2"><a class="nav-link text-nowrap" href="./products.php">'.$s['downloads'].'</a></li>';
        $navbarLink2 = '<li class="nav-item ms-2"><a class="nav-link text-nowrap" href="./about.php">'.$s['aboutPage'].'</a></li>';
        break;
    case 'downloads':
        $pageTitle = $s['tbDumpDownload'];
        $navbarLink1 = '<li class="nav-item ms-2"><a class="nav-link text-nowrap" href="./">'.$s['homePage'].'</a></li>
                        <li class="nav-item ms-2"><a class="nav-link text-nowrap active" aria-current="page" href="#">'.$s['downloads'].'</a></li>';
        $navbarLink2 = '<li class="nav-item ms-2"><a class="nav-link text-nowrap" href="./about.php">'.$s['aboutPage'].'</a></li>';
        break;
    case 'about':
        $pageTitle = $s['tbDump'];
        $navbarLink1 = '<li class="nav-item ms-2"><a class="nav-link text-nowrap" href="./">'.$s['homePage'].'</a></li>
                        <li class="nav-item ms-2"><a class="nav-link text-nowrap" href="./products.php">'.$s['downloads'].'</a></li>';
        $navbarLink2 = '<li class="nav-item ms-2"><a class="nav-link text-nowrap active" aria-current="page" href="#">'.$s['aboutPage'].'</a></li>';
        break;
    case 'update':
        $pageTitle = $s['tbDump'];
        $navbarLink1 = '<li class="nav-item ms-2"><a class="nav-link text-nowrap" href="./">'.$s['homePage'].'</a></li>
                        <li class="nav-item ms-2"><a class="nav-link text-nowrap" href="./products.php">'.$s['downloads'].'</a></li>';
        $navbarLink2 = '<li class="nav-item ms-2"><a class="nav-link text-nowrap" href="./about.php">'.$s['aboutPage'].'</a></li>';
        break;
    default:
        $pageTitle = $s['tbDump'];
        $navbarLink1 = '';
        $navbarLink2 = '';
        break;
    }
    
    $iso639lang = preg_replace("/-.*/i", "", $s['langCode']);

    if(isset($select)) {
        $selectContent = '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" integrity="sha512-aD9ophpFQ61nFZP6hXYu4Q/b/USW7rpLCQLX6Bi0WJHXNO7Js/fUENpBQf/+P4NtpzNX0jSgR5zVvPOJp+W2Kg==" crossorigin="anonymous" onerror="this.onerror=null;this.href=\'css/select2.min.css\';">
                   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" integrity="sha512-z/90a5SWiu4MWVelb5+ny7sAayYUfMmdXKEAbpj27PfdkamNdyI3hcjxPxkOPbrXoKIm7r9V2mElt5f1OtVhqA==" crossorigin="anonymous" onerror="this.onerror=null;this.href=\'css/select2-bootstrap-5-theme.min.css\';">
                   <script defer="defer" src="https://cdn.jsdelivr.net/npm/jquery@4.0.0-beta/dist/jquery.slim.min.js" integrity="sha512-lv3BlyhGttLlp7v8JMNDvgiaeT+N8hSxUjF45KNgigDDT26l1JeVby6SEj+Oz1oxcEQW7CxP15LW+ihoAt4+tA==" crossorigin="anonymous" onerror="this.onerror=null;this.href=\'js/jquery.slim.min.js\';"></script>
                   <script defer="defer" src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js" integrity="sha512-4MvcHwcbqXKUHB6Lx3Zb5CEAVoE9u84qN+ZSMM6s7z8IeJriExrV3ND5zRze9mxNlABJ6k864P/Vl8m0Sd3DtQ==" crossorigin="anonymous" onerror="this.onerror=null;this.href=\'js/select2.min.js\';"></script>';
    } else {
        $selectContent = '<link rel="prefetch" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" integrity="sha512-aD9ophpFQ61nFZP6hXYu4Q/b/USW7rpLCQLX6Bi0WJHXNO7Js/fUENpBQf/+P4NtpzNX0jSgR5zVvPOJp+W2Kg==" crossorigin="anonymous"onerror="this.onerror=null;this.href=\'css/select2.min.css\';">
                   <link rel="prefetch" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" integrity="sha512-z/90a5SWiu4MWVelb5+ny7sAayYUfMmdXKEAbpj27PfdkamNdyI3hcjxPxkOPbrXoKIm7r9V2mElt5f1OtVhqA==" crossorigin="anonymous" onerror="this.onerror=null;this.href=\'css/select2-bootstrap-5-theme.min.css\';">
                   <link rel="prefetch" src="https://cdn.jsdelivr.net/npm/jquery@4.0.0-beta/dist/jquery.slim.min.js" integrity="sha512-lv3BlyhGttLlp7v8JMNDvgiaeT+N8hSxUjF45KNgigDDT26l1JeVby6SEj+Oz1oxcEQW7CxP15LW+ihoAt4+tA==" crossorigin="anonymous" onerror="this.onerror=null;this.href=\'js/jquery.slim.min.js\';">
                   <link rel="prefetch" src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js" integrity="sha512-4MvcHwcbqXKUHB6Lx3Zb5CEAVoE9u84qN+ZSMM6s7z8IeJriExrV3ND5zRze9mxNlABJ6k864P/Vl8m0Sd3DtQ==" crossorigin="anonymous" onerror="this.onerror=null;this.href=\'js/select2.min.js\';">';
    }
  
    $iso639lang = preg_replace("/-.*/i", "", $s['langCode']);

    echo <<<HTML
<!DOCTYPE html>
<html $themeBs lang="$iso639lang">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="referrer" content="strict-origin-when-cross-origin" />

        <meta property="og:title" content="$pageTitle">
        <meta property="og:type" content="website">
        <meta property="og:description" content="{$s['aboutPageContent']}">
        <meta property="og:image" content="https://i.imgur.com/ES6JymB.png">

        <title>$pageTitle</title>
        <link rel="icon" href="favicon.ico" />
        <link rel="preconnect" href="https://cdn.jsdelivr.net/" crossorigin="anonymous" />
        <link rel="preload" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/fonts/bootstrap-icons.woff2?dd67030699838ea613ee6dbda90effa6" as="font" type="font/woff2" crossorigin="anonymous" onerror="this.onerror=null;this.href='css/fonts/bootstrap-icons.woff2?dd67030699838ea613ee6dbda90effa6';" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha512-jnSuA4Ss2PkkikSOLtYs8BlYIeeIK1h99ty4YfvRPAlzr377vr3CXDb7sb7eEEBYjDtcYj+AjBH3FLv5uSJuXg==" crossorigin="anonymous" onerror="this.onerror=null;this.href='css/bootstrap.min.css';">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" integrity="sha512-dPXYcDub/aeb08c63jRq/k6GaKccl256JQy/AnOq7CAnEZ9FzSL9wSbcZkMp4R26vBsMLFYH4kQ67/bbV8XaCQ==" fetchpriority="high" crossorigin="anonymous" onerror="this.onerror=null;this.href='css/bootstrap-icons.min.css';">
        <link rel="stylesheet" href="css/style.css">
        $themeMode
        $selectContent

        <script defer="defer" src="js/common.js"></script>
        <script defer="defer" src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha512-TPh2Oxlg1zp+kz3nFA0C5vVC6leG/6mm1z9+mA81MI5eaUVqasPLO8Cuk4gMF4gUfP5etR73rgU/8PNMsSesoQ==" crossorigin="anonymous" onerror="this.onerror=null;this.href='js/popper.min.js';"></script>
        <script defer="defer" src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha512-ykZ1QQr0Jy/4ZkvKuqWn4iF3lqPZyij9iRv6sGqLRdTPkY69YX6+7wvVGmsdBbiIfN/8OdsI7HABjvEok6ZopQ==" crossorigin="anonymous" onerror="this.onerror=null;this.href='js/bootstrap.min.js';"></script>

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
            <script defer="defer" src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
            <script defer="defer" src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>

    <body>
        <nav class="navbar navbar-expand-lg bg-body-tertiary fixed-top">
            <div class="container-lg">
                <a class="btn btn-outline navbar-brand" href="./">{$s['tbDump']}</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar" aria-controls="navbar" aria-expanded="false">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbar">
                    <ul class="navbar-nav">
                        $navbarLink1
                        $navbarLink2
                    </ul>
                    <ul class="navbar-nav ms-auto">
                        <form action="./products.php" class="me-3" role="search">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="search" class="form-control" name="search" placeholder="{$s['searchBar']}" aria-label="Search">
                            </div>
                        </form>
                        <button class="btn btn-theme p-1" id="themeBtn"><i class="bi bi-{$themeIcon}" id="themeIcon"></i></button>
                        <button class="btn btn-sm btn-theme p-0" id="restoreBtn" disabled><i class="bi bi-arrow-counterclockwise"></i></button>
                        $langCore_menu
                    </ul>
                </div><!--/.nav-collapse -->
            </div>
        </nav>

        <div class="container">

            <div class="content px-3 py-5">
HTML;
}

function styleBottom() {
    global $s, $websiteVersion;

    $copyright = sprintf(
        $s['footerNotice'],
        date('Y'),
        '<a class="link-underline link-underline-opacity-0" href="https://forums.mydigitallife.net/threads/72165">'.$s['contributors'].'</a>'
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