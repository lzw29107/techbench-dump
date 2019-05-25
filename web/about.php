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

require 'lang/core.php';
require 'shared/style.php';

styleTop('about');
echo '<h1>'.$translation['tbDump'].' <span class="badge">v'.$websiteVersion.'</span></h1>';
?>

<h3><span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span> <?php echo $translation['aboutPageTitle'];?></h3>
<p><?php echo $translation['aboutPageContent'];?></p>

<h3><span class="glyphicon glyphicon-thumbs-up" aria-hidden="true"></span> <?php echo $translation['aboutThanksTitle'];?></h3>
<p><?php echo $translation['aboutThanksContent'];?></p>

<h3><span class="glyphicon glyphicon-globe" aria-hidden="true"></span> <?php echo $translation['aboutTranslationsTitle'];?></h3>
<table class="table table-striped">
    <thead>
        <tr>
            <th><?php echo $translation['language'];?></th>
            <th><?php echo $translation['authors'];?></th>
        </tr>
    </thead>
    <tr>
        <td><img src="lang/flags/en-US.png"> English (US)</td>
        <td><a href="https://forums.mydigitallife.info/members/317641">whatever127</a> (<a href="https://github.com/whatever127">GitHub</a>)</td>
    </tr>
    <tr>
        <td><img src="lang/flags/es-ES.png"> Español (España)</td>
        <td><a href="https://forums.mydigitallife.info/members/417886">antonio8909</a></td>
    </tr>
    <tr>
        <td><img src="lang/flags/fr-FR.png"> Français</td>
        <td><a href="https://forums.mydigitallife.info/members/476049">NeXtStatioN (@AniMachin3)</a></td>
    </tr>
    <tr>
        <td><img src="lang/flags/it-IT.png"> Italiano</td>
        <td><a href="https://forums.mydigitallife.info/members/6748">garf02</a></td>
    </tr>
    <tr>
        <td><img src="lang/flags/nl-NL.png"> Nederlands</td>
        <td><a href="https://forums.mydigitallife.info/members/104688">Enthousiast</a></td>
    </tr>
    <tr>
        <td><img src="lang/flags/pl-PL.png"> Polski</td>
        <td><a href="https://forums.mydigitallife.info/members/317641">whatever127</a> (<a href="https://github.com/whatever127">GitHub</a>)</td>
    </tr>
    <tr>
        <td><img src="lang/flags/ru-RU.png"> Русский</td>
        <td><a href="https://forums.mydigitallife.info/members/381582">adguard</a></td>
    </tr>
    <tr>
        <td><img src="lang/flags/ar-EG.png"> العربية</td>
        <td><a href="https://forums.mydigitallife.info/members/319699">ShoSh</a></td>
    </tr>
    <tr>
        <td><img src="lang/flags/th-TH.png"> ภาษาไทย</td>
        <td><a href="https://forums.mydigitallife.info/members/418421">Phairat</a></td>
    </tr>
    <tr>
        <td><img src="lang/flags/ja-JP.png"> 日本語</td>
        <td><a href="https://forums.mydigitallife.info/members/476049">NeXtStatioN (@AniMachin3)</a></td>
    </tr>
    <tr>
        <td><img src="lang/flags/zh-CN.png"> 简体中文</td>
        <td><a href="https://forums.mydigitallife.info/members/623435">tneplus</a></td>
    </tr>
    <tr>
        <td><img src="lang/flags/zh-TW.png"> 繁體中文</td>
        <td><a href="https://forums.mydigitallife.info/members/269134">rubyclose (@iliGPU)</a></td>
    </tr>
    <tr>
        <td><img src="lang/flags/qps-ploc.png"> [ !!! Ƥşḗŭḓǿ !!! ]</td>
        <td><a href="https://forums.mydigitallife.info/members/317641">whatever127</a> (<a href="https://github.com/whatever127">GitHub</a>)</td>
    </tr>
</table>

<h3><span class="glyphicon glyphicon-certificate" aria-hidden="true"></span> <?php echo $translation['aboutLicenseTitle'];?></h3>
<p>
Copyright <?php echo date('Y'); ?> whatever127<br><br>

Licensed under the Apache License, Version 2.0 (the "License");<br>
you may not use this file except in compliance with the License.<br>
You may obtain a copy of the License at<br><br>

&nbsp;&nbsp;&nbsp;&nbsp;<a href="http://www.apache.org/licenses/LICENSE-2.0">http://www.apache.org/licenses/LICENSE-2.0</a><br><br>

Unless required by applicable law or agreed to in writing, software<br>
distributed under the License is distributed on an "AS IS" BASIS,<br>
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.<br>
See the License for the specific language governing permissions and<br>
limitations under the License.<br>
</p>


<?php styleBottom(); ?>
