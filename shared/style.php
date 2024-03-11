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

$websiteVersion = '3.0.0-alpha';
function styleTop($pageType = 'home') {
    global $s, $langCore_menu, $select;

    switch ($pageType) {
    case 'home':
        $pageTitle = $s['tbDump'];
        $navbarLink1 = '<li class="nav-item ms-2"><a class="nav-link text-nowrap active" aria-current="page" href="#">'.$s['homePage'].'</a></li>
                        <li class="nav-item ms-2"><a class="nav-link text-nowrap" href="./products.php">'.$s['downloads'].'</a></li>';
        $navbarLink2 = '<li class="nav-item ms-2"><a class="nav-link text-nowrap" href="./about.php">'.$s['aboutPage'].'</a></li>';
        break;
    case 'downloads':
        $pageTitle = $s['tbDumpDownload'];
        $navbarLink1 = '<li class="nav-item"><a class="nav-link text-nowrap" href="./">'.$s['homePage'].'</a></li>
                        <li class="nav-item ms-2"><a class="nav-link text-nowrap active" aria-current="page" href="#">'.$s['downloads'].'</a></li>';
        $navbarLink2 = '<li class="nav-item ms-2"><a class="nav-link text-nowrap" href="./about.php">'.$s['aboutPage'].'</a></li>';
        break;
    case 'about':
        $pageTitle = $s['tbDump'];
        $navbarLink1 = '<li class="nav-item"><a class="nav-link text-nowrap" href="./">'.$s['homePage'].'</a></li>
                        <li class="nav-item ms-2"><a class="nav-link text-nowrap" href="./products.php">'.$s['downloads'].'</a></li>';
        $navbarLink2 = '<li class="nav-item ms-2"><a class="nav-link text-nowrap active" aria-current="page" href="#">'.$s['aboutPage'].'</a></li>';
        break;
    default:
        $pageTitle = $s['tbDump'];
        $navbarLink1 = '';
        $navbarLink2 = '';
        break;
    }
    
    $iso639lang = preg_replace("/-.*/i", "", $s['langCode']);

    if(isset($select)) {
        $Select = '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" integrity="sha512-aD9ophpFQ61nFZP6hXYu4Q/b/USW7rpLCQLX6Bi0WJHXNO7Js/fUENpBQf/+P4NtpzNX0jSgR5zVvPOJp+W2Kg==" crossorigin="anonymous">
                   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" integrity="sha512-z/90a5SWiu4MWVelb5+ny7sAayYUfMmdXKEAbpj27PfdkamNdyI3hcjxPxkOPbrXoKIm7r9V2mElt5f1OtVhqA==" crossorigin="anonymous">
                   <script defer="defer" src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.slim.min.js" integrity="sha512-sNylduh9fqpYUK5OYXWcBleGzbZInWj8yCJAU57r1dpSK9tP2ghf/SRYCMj+KsslFkCOt3TvJrX2AV/Gc3wOqA==" crossorigin="anonymous"></script>
                   <script defer="defer" src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js" integrity="sha512-4MvcHwcbqXKUHB6Lx3Zb5CEAVoE9u84qN+ZSMM6s7z8IeJriExrV3ND5zRze9mxNlABJ6k864P/Vl8m0Sd3DtQ==" crossorigin="anonymous"></script>';
    } else {
        $Select = '<link rel="prefetch" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" integrity="sha512-aD9ophpFQ61nFZP6hXYu4Q/b/USW7rpLCQLX6Bi0WJHXNO7Js/fUENpBQf/+P4NtpzNX0jSgR5zVvPOJp+W2Kg==" crossorigin="anonymous">
                   <link rel="prefetch" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" integrity="sha512-z/90a5SWiu4MWVelb5+ny7sAayYUfMmdXKEAbpj27PfdkamNdyI3hcjxPxkOPbrXoKIm7r9V2mElt5f1OtVhqA==" crossorigin="anonymous">
                   <link rel="prefetch" src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.slim.min.js" integrity="sha512-sNylduh9fqpYUK5OYXWcBleGzbZInWj8yCJAU57r1dpSK9tP2ghf/SRYCMj+KsslFkCOt3TvJrX2AV/Gc3wOqA==" crossorigin="anonymous">
                   <link rel="prefetch" src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js" integrity="sha512-4MvcHwcbqXKUHB6Lx3Zb5CEAVoE9u84qN+ZSMM6s7z8IeJriExrV3ND5zRze9mxNlABJ6k864P/Vl8m0Sd3DtQ==" crossorigin="anonymous">';
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
        <link rel="icon" href="favicon.ico" />
        <link rel="preconnect" href="https://cdn.jsdelivr.net/" crossorigin />
        <link rel="preload" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/fonts/bootstrap-icons.woff2?dd67030699838ea613ee6dbda90effa6" as="font" type="font/woff2" crossorigin />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha512-jnSuA4Ss2PkkikSOLtYs8BlYIeeIK1h99ty4YfvRPAlzr377vr3CXDb7sb7eEEBYjDtcYj+AjBH3FLv5uSJuXg==" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" integrity="sha512-dPXYcDub/aeb08c63jRq/k6GaKccl256JQy/AnOq7CAnEZ9FzSL9wSbcZkMp4R26vBsMLFYH4kQ67/bbV8XaCQ==" fetchpriority="high" crossorigin="anonymous">
        <link rel="stylesheet" href="css/style.css">
        $Select

        <script defer="defer" src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha512-TPh2Oxlg1zp+kz3nFA0C5vVC6leG/6mm1z9+mA81MI5eaUVqasPLO8Cuk4gMF4gUfP5etR73rgU/8PNMsSesoQ==" crossorigin="anonymous"></script>
        <script defer="defer" src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha512-ykZ1QQr0Jy/4ZkvKuqWn4iF3lqPZyij9iRv6sGqLRdTPkY69YX6+7wvVGmsdBbiIfN/8OdsI7HABjvEok6ZopQ==" crossorigin="anonymous"></script>

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
            <script defer="defer" src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
            <script defer="defer" src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>

    <body>

        <nav class="navbar navbar-expand-lg bg-body-tertiary fixed-top">
            <div class="container-lg">
                <a class="btn btn-outline-light navbar-brand" href="./">{$s['tbDump']}</a>
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
        '<a class="link-underline link-underline-opacity-0" href="https://forums.mydigitallife.info/threads/72165">'.$s['contributors'].'</a>'
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