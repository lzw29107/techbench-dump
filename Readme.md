TechBench dump
--------------
Website: https://tb.win-story.cn/<br>
Script:<br>
tbdump.sh 			- Shell script of TechBench dump. Obtains information from API, and then writes them to json file<br>
tbdump.cmd		        - Running PowerShell script without modifying the default script execution policy<br>
tbdump_for(v5/v7).ps1		- PowerShell (v5/v7+) script of TechBench dump. Obtains information from API, and then writes them to json file

Usage
-----
### Windows
Simply run desired cmd script, it will generate everything automatically.<br>It will automatically use PowerShell to run script.<br>Of course, you can also run PowerShell script directly, its usage can be obtained through the Get-Help command.<br>

Command line usage:
```
<script.cmd> [first_id] [last_id] [/f {Path for /f}]
<script.cmd> [/c] [last_id] [/f {Path for /f}]

[/c] Starting dump from existing dump file.
[/f] Path to dump file.
```

Example command to create files used by Website with products from range between 242 and 247:
```
tbdump.cmd 242 247
```

### Linux
Give execute permission to file and run it with desired parameters.<br>PowerShell v7 script are also applicable to Linux.<br>

Command line usage:
```
<./script.sh> [-f {Path for -f}] [first_id] [last_id]
<./script.sh> [-f {Path for -f}] [-c] [last_id]

[-c] Starting dump from existing dump file.
[-f] Path to dump file.
```

Example command to create files used by Website with products from range between 242 and 247:
```
./tbdump.sh 242 247
```

License
-------
Copyright 2023 Techbench dump website authors and contributors<br><br>

Licensed under the Apache License, Version 2.0 (the "License");<br>
you may not use this file except in compliance with the License.<br>
You may obtain a copy of the License at<br><br>

http://www.apache.org/licenses/LICENSE-2.0<br><br>

Unless required by applicable law or agreed to in writing, software<br>
distributed under the License is distributed on an "AS IS" BASIS,<br>
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.<br>
See the License for the specific language governing permissions and<br>
limitations under the License.<br>

Credits
-------
WzorNET - finding out that TechBench contains more than Windows 10.<br>
Bootstrap - http://getbootstrap.com/<br>
GoSquared - Flags https://github.com/gosquared/flags
