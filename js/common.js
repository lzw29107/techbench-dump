let rootElement = document.documentElement;
let themeBtn = document.getElementById('themeBtn');
let themeIcon = document.getElementById('themeIcon');
let restoreBtn = document.getElementById('restoreBtn');
let mediaQueryList = window.matchMedia('(prefers-color-scheme: dark)');

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
  for(let i = 0; i < cookieArray.length; i++) {
    let currentCookie = cookieArray[i].trim();
    if (currentCookie.startsWith(name)) {
      return currentCookie.substring(name.length, currentCookie.length);
    }
   }
   return "";
}

function setTheme(theme) {
    if (theme == 'dark') {
        rootElement.setAttribute('data-bs-theme', 'dark');
        if (themeIcon.classList.contains('bi-sun'))
        {
            themeIcon.classList.remove('bi-sun');
            themeIcon.classList.add('bi-moon');
        }
    } else {
        rootElement.setAttribute('data-bs-theme', 'light');
        if (themeIcon.classList.contains('bi-moon'))
        {
            themeIcon.classList.remove('bi-moon');
            themeIcon.classList.add('bi-sun');
        }
    }
    if (doesCookieExist('theme')) {
        restoreBtn.disabled = false;
    }
}

function detectColorScheme() {
    if (mediaQueryList.matches) {
        setTheme('dark');
    } else {
        setTheme('light');
    }
}

if (doesCookieExist('theme')) {
    restoreBtn.disabled = false;
    setTheme(getCookie('theme'));
} else {
    detectColorScheme();
    mediaQueryList.addListener(detectColorScheme);
}

themeBtn.onclick = function () {
    let newTheme = rootElement.getAttribute('data-bs-theme') == 'dark' ? 'light' : 'dark';
    setTheme(newTheme);
    setCookie('theme', newTheme);
};

restoreBtn.onclick = function () {
    setCookie('theme', '', -1);
};