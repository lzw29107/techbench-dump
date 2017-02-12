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

TechBench dump
--------------
Website: https://mdl-tb.ct8.pl/<br>
Markdown: https://mdl-tb.ct8.pl/dump.md

Credits
-------
WzorNET - finding out that TechBench contains more than Windows 10.<br>
Bootstrap - http://getbootstrap.com/<br>
GoSquared - Flags. https://github.com/gosquared/flags
