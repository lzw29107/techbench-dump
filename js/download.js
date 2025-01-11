function getFullUrl(action, prodId, skuId, fileName, language, sessionId) {
    let baseUrl = `https://www.microsoft.com/software-download-connector/api/${action}?`;
    let parameters = new URLSearchParams();
    parameters.set('profile', '606624d44113');
    parameters.set('ProductEditionId', prodId);
    parameters.set('SKU', skuId);
    parameters.set('friendlyFileName', fileName);
    parameters.set('Locale', language);
    parameters.set('sessionID', sessionId);
    return baseUrl + parameters.toString();
}

let currentId = "";
let initialized = false;

function initializeSession(prodId, language, sessionId) {
    let langsUrl = getFullUrl(actions.info, prodId, undefined, undefined, language, sessionId);
    let xhr = new XMLHttpRequest();
    xhr.open('GET', langsUrl, true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            initialized = true;
            if (currentId !== "") {
                submit.disabled = false;
            }
        }
    };
    xhr.send();
    }

let actions = {
    'info': 'getskuinformationbyProductedition',
    'down': 'GetProductDownloadLinksBySku'
};

let prodLang = document.getElementById('product-languages');
let msContent2 = document.getElementById('msContent2');
let data;
let withCookies = false;
const prodId = new URLSearchParams(location.search).get('id');
let skuId;
const language = document.getElementById('langCodeMs').value;
let submit = document.getElementById('submit-sku');
let expireTime = document.getElementById('expireTime');
const expireText = expireTime.innerText;
let fileName;
let downDivs = {
    'neutral': document.getElementById('down'),
    'x86': document.getElementById('downx86'),
    'x64': document.getElementById('downx64'),
    'arm64': document.getElementById('downarm64')
};
let downBtns = {
    'neutral': downDivs['neutral'].getElementsByTagName('a')[0],
    'x86': downDivs['x86'].getElementsByTagName('a')[0],
    'x64': downDivs['x64'].getElementsByTagName('a')[0],
    'arm64': downDivs['arm64'].getElementsByTagName('a')[0]
};

let sessionId = newSession();
initializeSession(prodId, language, sessionId);

let xhr = new XMLHttpRequest();
xhr.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
        data = JSON.parse(this.responseText);

        let placeholderText = prodLang.options[prodLang.selectedIndex].innerText;

        withCookies = data['withCookies'];
        submit.setAttribute(            "onClick",             "getDownload(prodLang)"        );
        prodLang.setAttribute("onChange", "updateVars(prodLang)");

        $("select").select2({
            theme: "bootstrap-5",
            minimumResultsForSearch: Infinity,
            placeholder: placeholderText,
            data: data['select2']
        });
    }
};
xhr.open('POST', window.location.href);
xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
xhr.send('prodId=' + prodId);

function updateVars(prodLang) {
    let id = prodLang.value;
    currentId = id;
    if (id == "") {
        submit.disabled = true;
        return;
    }

    const found = data['select2'].find(item => item.id == id);
    id = { 'id': id };
    if (found) {
        id['language'] = found.enName ? found.enName : found.text;
    }

    if (initialized && id['id'] !== "") {
        submit.disabled = false;
    } else {
        submit.disabled = true;
    }

    return id;
}

function getDownload(prodLang) {
    let xhr = new XMLHttpRequest();
    skuId = updateVars(prodLang)['id'];
    xhr.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            data = JSON.parse(this.responseText);
            if (data.Errors !== undefined) {
                let errorModalMessage = document.getElementById('errorModalMessage');
                let errorMessage = data.Errors[0].Value;
                errorModalMessage.innerHTML = "<h4>" + errorMessage + "</h4><p>";
                return;
            }
            data.ProductDownloadOptions.forEach(option => {
                switch (option.DownloadType) {
                    case 0:
                        downDivs['x86'].removeAttribute('style');
                        downDivs['x86'].getElementsByTagName('p')[0].innerText += ' ' + option.Name;
                        downBtns['x86'].setAttribute('href', option.Uri);
                        break;
                    case 1:
                        downDivs['x64'].removeAttribute('style');
                        downDivs['x64'].getElementsByTagName('p')[0].innerText += ' ' + option.Name;
                        downBtns['x64'].setAttribute('href', option.Uri);
                        break;
                    case 2:
                        downDivs['arm64'].removeAttribute('style');
                        downDivs['arm64'].getElementsByTagName('p')[0].innerText += ' ' + option.Name;
                        downBtns['arm64'].setAttribute('href', option.Uri);
                        break;
                    default:
                        downDivs['neutral'].removeAttribute('style');
                        downDivs['neutral'].getElementsByTagName('p')[0].innerText += ' ' + option.Name;
                        downBtns['neutral'].setAttribute('href', option.Uri);
                }
            });
            expireTime.innerText = expireText + new Date(data.DownloadExpirationDatetime).toLocaleString();
        }
        msContent2.removeAttribute('style');
        submit.disabled = 0;
    };

    let downUrl = getFullUrl(actions.down, undefined, skuId, undefined, language, sessionId);
    xhr.open('GET', downUrl);
    xhr.withCredentials = withCookies;
    xhr.send();
    submit.disabled = 1;
}