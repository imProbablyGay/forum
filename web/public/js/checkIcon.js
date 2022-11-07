export {checkIcons};

function checkIcons(icons) {
    icons.forEach(el => {
        var req = new XMLHttpRequest();
        req.open('get', el.src, false);
        req.send(null);

        if (req.status == 404) el.src = '../img/default-icon.png';
    })
}


