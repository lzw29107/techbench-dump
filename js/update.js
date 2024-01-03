
function getInfo(ID, callback) {
    url = `https://www.microsoft.com/en-us/api/controls/contentinclude/html?pageId=cd06bda8-ff9c-4a6e-912a-b92a21f42526&host=www.microsoft.com&segments=software-download%2Cwindows11&query=&action=getskuinformationbyProductedition&sessionId=${SessionID}&ProductEditionId=${ID}&sdVersion=2`;
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
        if (this.readyState == 4) {
            if (this.status == 200 && typeof callback === "function") {
                callback.apply(xhr, [ID]);
            } else if(this.status != 200) getInfo(ID, callback);
        }
    }
    xhr.open('POST',url);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded; charset=UTF-8");
    xhr.withCredentials = true;
    xhr.timeout=30000;
    xhr.send('controlAttributeMapping=');
}

function checkInfo(ID, SkuID, SkuName, callback) {
      url = `https://www.microsoft.com/en-us/api/controls/contentinclude/html?pageId=cfa9e580-a81e-4a4b-a846-7b21bf4e2e5b&host=www.microsoft.com&segments=software-download%2Cwindows11&query=&action=GetProductDownloadLinksBySku&sessionId=${SessionID}&skuId=${SkuID}&language=${SkuName}&sdVersion=2`;
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if (this.readyState == 4) {
            if (this.readyState == 4) {
                if (this.status == 200 && typeof callback === "function") {
                    callback.apply(xhr,[ID]);
                } else if(this.status != 200) checkInfo(ID, SkuID, SkuName, callback);
            }
        }
    }
    xhr.open('POST',url);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded; charset=UTF-8");
    xhr.withCredentials = true;
    xhr.timeout=30000;
    xhr.send('controlAttributeMapping=');
}

function _getInfo(ID) {
    response = this.responseText;
    let html = new DOMParser().parseFromString(response, 'text/html');
    if(html.getElementById('errorModalMessage')) return false;
    let Name = html.getElementsByTagName('i').item(0).textContent.substr(32);
    let Options = html.getElementsByTagName('option');
    let SkuID = JSON.parse(Options[Options.length - 1].getAttribute('value'))['id'];
    let SkuName = Options[Options.length - 1].textContent;
    checkInfo(ID, SkuID, SkuName, _checkInfo);
}

function _checkInfo(ID) {
    response = this.responseText;
    let downhtml = new DOMParser().parseFromString(response, 'text/html');
    let Info = [];
    if (downhtml.getElementById('errorModalMessage')) {
        Info['Validity'] = 'Invalid';
        let errorMsg = downhtml.getElementById('errorModalMessage').textContent;
        if(errorMsg == blocked) {
            Info['Validity'] = 'Valid';
        }
        Info['Arch'] = 'Unknown';
    } else {
        Info['Validity'] = 'Valid';
        let downBtns = downhtml.getElementsByClassName('product-download-type');
        let Arch = [];
        for(i = 0; i < downBtns.length; ++i) {
            let downBtn = downBtns[i];
            if(downBtn.textContent == 'IsoX86') Arch.push('x86');
            if(downBtn.textContent == 'IsoX64') Arch.push('x64');
            if(downBtn.textContent == 'Unknown') Arch.push('neutral');
        }
        if (Arch.length == 0) Arch.push('Unknown');
        Info['Arch'] = Arch.join('; ');
    }
    Infos[ID] = Info;
    current++;
    process(IDs, current);
}

function NewSession() {
    var xhr = new XMLHttpRequest();
    xhr.open('POST',window.location.href,false);
    xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
    xhr.send('NewSession=1');
    return xhr.responseText;
}

function process(IDs, current) {
    if(current > IDs.length) return true;
    let ID = IDs[current - 1];
    let Progress = ((current / IDs.length) * 100).toFixed(2);
    progressbar.classList.add("bg-info");
    if(Progress >= 10.00) progresstext.style.width = 'calc(50% + 1.25rem)';
    count.innerText = current + ' / ' + IDs.length;
    progress.setAttribute('aria-valuenow', Progress);
    progressbar.style.width = Progress + '%';
    progresstext.innerText = Progress + '%';
    if(current % 15 == 1) SessionID = NewSession();
    getInfo(ID, _getInfo);
}

checkBtn.onclick = function () {
    Type = 'WIP';
    blocked = 'We are unable to complete your request at this time. Some users, entities and locations are banned from using this service. For this reason, leveraging anonymous or location hiding technologies when connecting to this service is not generally allowed. If you believe that you encountered this problem in error, please try again. If the problem persists you may contact  Microsoft Support – Contact Us  page for assistance. Refer to message code 715-123130 and';
    Infos = [];
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = async function () {
        if (this.readyState == 4 && this.status == 200) {
            IDs = JSON.parse(this.responseText);
            dumpBtn.disabled = true;
            checkBtn.disabled = true;
            progress.style.display = '';
            count.style.display = '';
            current = 1;
            await process(IDs, current);
            result = [];
            for(i = 0; i < Infos.length; ++i) {
                if(typeof Infos[i] != 'undefined') result[i] = {
                    ID: i,
                    Validity: Infos[i]['Validity'],
                    Arch: Infos[i]['Arch']
                };
            }
            result[Infos.length] = {
                type: 'reCheck',
                status: 'result'
            };
            result = JSON.stringify(result).replaceAll('null,', '');
            var xhr = new XMLHttpRequest();
            xhr.open('POST', window.location.href);
            xhr.setRequestHeader('Content-Type', 'application/json');
            xhr.send(result);
        }
    }
    xhr.open('POST', window.location.href);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.send('reCheck=' + Type);
};
