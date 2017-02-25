<?php
// TechBench dump
// Copyright (C) 2017  mkuba50

// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

// Additional terms to GPLv3 license apply, see LICENSE.txt file or
// <https://github.com/techbench-dump/website/blob/master/LICENSE.txt>.

// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.

// You should have received a copy of the GNU General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.

$fileName = isset($_GET['fileName']) ? $_GET['fileName'] : 'Win7_Pro_SP1_English_x64.iso';
require 'shared/get.php';

$downList = getDownloadByName($fileName);
if(isset($downList['error'])) {
    echo 'There was an error processing your request.';
    die();
}
echo '<h1>Moved to <a href="'. $downList['downloadLink'] .'">here</a>';

header('Location: '. $downList['downloadLink']);
die();
?>
