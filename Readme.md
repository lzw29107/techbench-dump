Info
----
This script obtains links from Microsoft's API and then writes them to file.<br>
Currently it is based on cUrl and Bash.

tbdump.sh - Shell script version of TechBench dump. Obtains links from API, and then writes them to formatted file<br>

Usage
-----
### Everything with bash support
Give execute permission to file and run it with desired parameters.<br>

Command line usage:
```
<./script.sh> generator [first_id] [last_id]
```

Example command to create files used by Website with products from range between 242 and 247:
```
./tbdump.sh web 242 247
```

License
-------
Copyright 2017 mkuba50<br><br>

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
