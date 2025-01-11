
function getInfo(id, callback) {
    const url = `https://www.microsoft.com/en-us/api/controls/contentinclude/html?pageid=cd06bda8-ff9c-4a6e-912a-b92a21f42526&host=www.microsoft.com&segments=software-download%2Cwindows11&query=&action=getskuinformationbyProductedition&sessionId=${sessionId}&ProductEditionId=${id}&sdVersion=2`;
    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
        if (this.readyState == 4) {
            if (this.status == 200 && typeof callback === "function") {
                callback.apply(xhr, [id]);
            } else if (this.status != 200) getInfo(id, callback);
        }
    }
    xhr.open('POST',url);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded; charset=UTF-8");
    xhr.withCredentials = true;
    xhr.timeout=30000;
    xhr.send('controlAttributeMapping=');
}

function checkInfo(id, skuId, skuName, callback) {
    const url = `https://www.microsoft.com/en-us/api/controls/contentinclude/html?pageid=cfa9e580-a81e-4a4b-a846-7b21bf4e2e5b&host=www.microsoft.com&segments=software-download%2Cwindows11&query=&action=GetProductDownloadLinksBySku&sessionId=${sessionId}&skuId=${skuId}&language=${skuName}&sdVersion=2`;
    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if (this.readyState == 4) {
            if (this.status == 200 && typeof callback === "function") {
                callback.apply(xhr,[id]);
            } else if (this.status != 200) checkInfo(id, skuId, skuName, callback);
        }
    }
    xhr.open('POST',url);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded; charset=UTF-8");
    xhr.withCredentials = true;
    xhr.timeout=30000;
    xhr.send('controlAttributeMapping=');
}

function _getInfo(id) {
    let response = this.responseText;
    let html = new DOMParser().parseFromString(response, 'text/html');
    if (html.getElementById('errorModalMessage')) return false;
    let name = html.getElementsByTagName('i').item(0).textContent.substr(32);
    let options = html.getElementsByTagName('option');
    let skuId = JSON.parse(options[options.length - 1].getAttribute('value'))['id'];
    let skuName = options[options.length - 1].textContent;
    checkInfo(id, skuId, skuName, _checkInfo);
}

function _checkInfo(id) {
    let response = this.responseText;
    let downhtml = new DOMParser().parseFromString(response, 'text/html');
    let info = [];
    if (downhtml.getElementById('errorModalMessage')) {
        info['Status'] = 'Invalid';
        let errorMsg = downhtml.getElementById('errorModalMessage').textContent;
        if(errorMsg == blocked) {
            info['Status'] = 'Valid';
        }
        info['Arch'] = 'Unknown';
    } else {
        info['Status'] = 'Valid';
        let downBtns = downhtml.getElementsByClassName('product-download-type');
        let arch = [];
        for(i = 0; i < downBtns.length; ++i) {
            let downBtn = downBtns[i];
            if(downBtn.textContent == 'IsoX86') arch.push('x86');
            if(downBtn.textContent == 'IsoX64') arch.push('x64');
            if(downBtn.textContent == 'Unknown') arch.push('neutral');
        }
        if (arch.length == 0) arch.push('Unknown');
        info['Arch'] = arch;
    }
    infos[id] = info;
    current++;
    process(ids, current);
}

function process(ids, current) {
    return new Promise(function () {
        if(current > ids.length) return true;
        let id = ids[current - 1];
        let progressValue = ((current / ids.length) * 100).toFixed(2);
        progressbar.classList.add("bg-info");
        if(progressValue >= 10.00) progresstext.style.width = 'calc(50% + 1.25rem)';
        count.innerText = current + ' / ' + ids.length;
        progress.setAttribute('aria-valuenow', progressValue);
        progressbar.style.width = progressValue + '%';
        progresstext.innerText = progressValue + '%';
        if(current % 15 == 1) newSession();
        getInfo(id, _getInfo);
    });
}

let sessionId;

const blocked = 'We are unable to complete your request at this time. Some users, entities and locations are banned from using this service. For this reason, leveraging anonymous or location hiding technologies when connecting to this service is not generally allowed. If you believe that you encountered this problem in error, please try again. If the problem persists you may contact  Microsoft Support – Contact Us  page for assistance. Refer to message code 715-123130 and';

checkBtn.onclick = function () {
    let type = 'WIP';
    infos = [];
    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = async function () {
        if (this.readyState == 4 && this.status == 200) {
            ids = JSON.parse(this.responseText);
            dumpBtn.disabled = true;
            checkBtn.disabled = true;
            progress.style.display = '';
            count.style.display = '';
            current = 1;
            await process(ids, current);
            let result = [];
            for(i = 0; i < infos.length; ++i) {
                if(typeof infos[i] != 'undefined') result[i] = {
                    ID: i,
                    Status: infos[i]['Status'],
                    Arch: infos[i]['Arch']
                };
            }
            result[infos.length] = {
                type: 'recheck',
                status: 'result'
            };
            result = JSON.stringify(result).replaceAll('null,', '');
            let xhr = new XMLHttpRequest();
            xhr.open('POST', window.location.href);
            xhr.setRequestHeader('Content-Type', 'application/json');
            xhr.send(result);
        }
    }
    xhr.open('POST', window.location.href);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.send('recheck=' + type);
};
