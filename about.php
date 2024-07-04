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

require_once 'shared/lang.php';
require_once 'shared/style.php';

styleTop('about');

$copyright = sprintf(
    $s['copyright'],
    date('Y'),
    '<a class="link-underline link-underline-opacity-0" href="https://forums.mydigitallife.info/threads/72165">'.$s['contributors'].'</a>'
);

echo <<<HTML
<div class="my-5 text-center">
    <h1 class="fs-1 fw-bold">{$s['tbDump']} 
        <span class="badge rounded-pill bg-primary position-absolute">v$websiteVersion</span>
    </h1>
</div>

<h3 class="fs-4"><i class="bi bi-info-circle-fill fs-5"></i> {$s['aboutPageTitle']}</h3>
<p>{$s['aboutPageContent']}</p>

<h3 class="fs-4"><i class="bi bi-hand-thumbs-up fs-5"></i> {$s['aboutThanksTitle']}</h3>
<p>{$s['aboutThanksContent']}</p>

<h3 class="fs-4"><i class="bi bi-globe fs-5"></i> {$s['aboutTranslationsTitle']}</h3>
<table class="table table-striped">
    <thead>
        <tr>
            <th>{$s['language']}</th>
            <th>{$s['authors']}</th>
        </tr>
    </thead>
<tbody><tr>
        <td><img src="contrib/flags/en-US.png"> English (US)</td>
        <td><a class="link-underline link-underline-opacity-0" href="https://forums.mydigitallife.info/members/317641">whatever127</a> (<a class="link-underline link-underline-opacity-0" href="https://github.com/whatever127">GitHub</a>)</td>
    </tr>
    <tr>
        <td><img src="contrib/flags/es-ES.png"> Español (España)</td>
        <td><a class="link-underline link-underline-opacity-0" href="https://forums.mydigitallife.info/members/417886">antonio8909</a></td>
    </tr>
    <tr>
        <td><img src="contrib/flags/fr-FR.png"> Français</td>
        <td><a class="link-underline link-underline-opacity-0" href="https://forums.mydigitallife.info/members/476049">NeXtStatioN (@AniMachin3)</a></td>
    </tr>
    <tr>
        <td><img src="contrib/flags/it-IT.png"> Italiano</td>
        <td><a class="link-underline link-underline-opacity-0" href="https://forums.mydigitallife.info/members/6748">garf02</a></td>
    </tr>
    <tr>
        <td><img src="contrib/flags/nl-NL.png"> Nederlands</td>
        <td><a class="link-underline link-underline-opacity-0" href="https://forums.mydigitallife.info/members/104688">Enthousiast</a></td>
    </tr>
    <tr>
        <td><img src="contrib/flags/pl-PL.png"> Polski</td>
        <td><a class="link-underline link-underline-opacity-0" href="https://forums.mydigitallife.info/members/317641">whatever127</a> (<a class="link-underline link-underline-opacity-0" href="https://github.com/whatever127">GitHub</a>)</td>
    </tr>
    <tr>
        <td><img src="contrib/flags/pt-BR.png"> Português (Brasil)</td>
        <td><a class="link-underline link-underline-opacity-0" href="https://gitlab.com/ygor.almeida">Ygor Almeida</a></td>
    </tr>
    <tr>
        <td><img src="contrib/flags/ru-RU.png"> Русский</td>
        <td><a class="link-underline link-underline-opacity-0" href="https://forums.mydigitallife.info/members/381582">adguard</a></td>
    </tr>
    <tr>
        <td><img src="contrib/flags/ar-EG.png"> العربية</td>
        <td><a class="link-underline link-underline-opacity-0" href="https://forums.mydigitallife.info/members/319699">ShoSh</a></td>
    </tr>
    <tr>
        <td><img src="contrib/flags/th-TH.png"> ภาษาไทย</td>
        <td><a class="link-underline link-underline-opacity-0" href="https://forums.mydigitallife.info/members/418421">Phairat</a></td>
    </tr>
    <tr>
        <td><img src="contrib/flags/ja-JP.png"> 日本語</td>
        <td><a class="link-underline link-underline-opacity-0" href="https://forums.mydigitallife.info/members/476049">NeXtStatioN (@AniMachin3)</a></td>
    </tr>
    <tr>
        <td><img src="contrib/flags/zh-CN.png"> 简体中文</td>
        <td><a class="link-underline link-underline-opacity-0" href="https://forums.mydigitallife.info/members/690532">正义羊 (JRJSheep)</a></td>
    </tr>
    <tr>
        <td><img src="contrib/flags/zh-TW.png"> 繁體中文</td>
        <td><a class="link-underline link-underline-opacity-0" href="https://forums.mydigitallife.info/members/269134">rubyclose (@iliGPU)</a></td>
    </tr>
    <tr>
        <td><img src="contrib/flags/qps-ploc.png"> [ !!! Ƥşḗŭḓǿ !!! ]</td>
        <td><a class="link-underline link-underline-opacity-0" href="https://forums.mydigitallife.info/members/317641">whatever127</a> (<a class="link-underline link-underline-opacity-0" href="https://github.com/whatever127">GitHub</a>)</td>
    </tr>
</tbody>
</table>

<h3 class="fs-4"><i class="bi bi-patch-check-fill fs-5"></i> {$s['aboutLicenseTitle']}</h3>
<p>
$copyright<br><br>

Licensed under the Apache License, Version 2.0 (the "License");<br>
you may not use this file except in compliance with the License.<br>
You may obtain a copy of the License at<br><br>

<a class="link-underline link-underline-opacity-0" href="https://www.apache.org/licenses/LICENSE-2.0">https://www.apache.org/licenses/LICENSE-2.0</a><br><br>

Unless required by applicable law or agreed to in writing, software<br>
distributed under the License is distributed on an "AS IS" BASIS,<br>
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.<br>
See the License for the specific language governing permissions and<br>
limitations under the License.<br>
</p>
HTML;
?>
<?php styleBottom(); ?>
