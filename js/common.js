let rootElement = document.documentElement;
let themeBtn = document.getElementById('themeBtn');
let themeIcon = document.getElementById('themeIcon');
let restoreBtn = document.getElementById('restoreBtn');
let mediaQueryList = window.matchMedia('(prefers-color-scheme: dark)');

const themeMode = {
    dark: 'dark',
    light: 'light'
};

function doesCookieExist(cookieName) {
    return document.cookie.split(';').some((item) => item.trim().startsWith(cookieName));
}

function setCookie(name, value, days = 30) {
    let date = new Date();
    date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
    const expires = "; expires=" + date.toUTCString();

    const cookie = [
        `${name}=${value}; `,
        `expires=${date}; `,
        `Max-Age=${expires}; `,
        'path=/; ',
        `domain=${location.hostname}; `,
        location.protocol == 'https:' ? 'Secure; ' : '',
        'SameSite=Strict'
    ].join('');

    document.cookie = cookie;
    if (days < 0) location.reload();
}

function getCookie(cookieName) {
    let name = cookieName + "=";
    let cookieArray = document.cookie.split(';');
    for (let i = 0; i < cookieArray.length; i++) {
        let currentCookie = cookieArray[i].trim();
        if (currentCookie.startsWith(name)) {
            return currentCookie.substring(name.length, currentCookie.length);
        }
    }
    return "";
}

function removeCookie(cookieName) {
    setCookie(cookieName, '', -1);
}

function setTheme(theme) {
    switch (theme) {
        case themeMode.dark:
            rootElement.setAttribute('data-bs-theme', 'dark');
            if (themeIcon.classList.contains('bi-sun'))
            {
                themeIcon.classList.remove('bi-sun');
                themeIcon.classList.add('bi-moon');
            }
            break;
        case themeMode.light:
            rootElement.setAttribute('data-bs-theme', 'light');
            if (themeIcon.classList.contains('bi-moon'))
            {
                themeIcon.classList.remove('bi-moon');
                themeIcon.classList.add('bi-sun');
            }
            break;
        default:
            console.error('Invalid theme');
            if (doesCookieExist('theme')) {
                removeCookie('theme');
            }
            return;
    }

    if (doesCookieExist('theme')) {
        restoreBtn.disabled = false;
    }
}

function detectColorScheme() {
    if (mediaQueryList.matches) {
        setTheme(themeMode.dark);
    } else {
        setTheme(themeMode.light);
    }
}

function resetTheme() {
    removeCookie('theme');
}

function newSession() {
    let xhr = new XMLHttpRequest();
    xhr.open('POST', getScriptPath() + '../update.php', false);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.send('NewSession=1');
    return xhr.responseText;
}

function getScriptPath() {
    let scriptSrc;
    if (document.currentScript) {
        scriptSrc = document.currentScript.src;
    } else {
        let scripts = document.getElementsByTagName('script');
        scriptSrc = scripts[scripts.length - 1].src;
    }
    return scriptSrc.substring(0, scriptSrc.lastIndexOf('/') + 1);
}

if (doesCookieExist('theme')) {
    restoreBtn.disabled = false;
    setTheme(getCookie('theme'));
} else {
    detectColorScheme();
    mediaQueryList.addEventListener('change', detectColorScheme);
}

themeBtn.onclick = function () {
    let newTheme = rootElement.getAttribute('data-bs-theme') == 'dark' ? 'light' : 'dark';
    setTheme(newTheme);
    setCookie('theme', newTheme);
};

restoreBtn.onclick = function () {
    resetTheme();
};