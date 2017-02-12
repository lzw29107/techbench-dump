Info
----
This script obtains links from Microsoft's API and then writes them to file.<br>
Currently it is based on cUrl and Bash (on Windows it uses BusyBox).

tbdump.sh 			- Shell script version of TechBench dump. Obtains links from API, and then writes them to formatted file<br>
techbench.cmd		- Runs shell script on Windows and configures it to generate HTML file<br>
techbench_md.cmd	- Runs shell script on Windows and configures it to generate Markdown file (GitHub paste format)

Usage
-----
### Windows
Simply run desired cmd script, it will generate everything automatically.<br>

Command line usage:
```
<script.cmd> [first_id] [last_id]
```

Example command to create HTML file with products from range between 242 and 247:
```
techbench.cmd 242 247
```

### Everything else with bash support
Give execute permission to file and run it with desired parameters.<br>

Command line usage:
```
<./script.sh> generator [first_id] [last_id]
```

Example command to create HTML file with products from range between 242 and 247:
```
./tbdump.sh html 242 247
```

TechBench dump
--------------
Website: https://mdl-tb.ct8.pl/<br>
Markdown: https://mdl-tb.ct8.pl/dump.md

Credits
-------
WzorNET - finding out that TechBench contains more than Windows 10.<br>
Ron Yorston - BusyBox port for Windows. https://frippery.org/busybox/<br>
Stefan Kanthak - cUrl binaries for Windows. https://skanthak.homepage.t-online.de/curl.html
