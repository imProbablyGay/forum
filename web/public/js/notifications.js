document.querySelector('.search').addEventListener('click',(e) => {
    if (e.target.tagName != "A") return;
    e.preventDefault()
    let notifID = e.target.dataset.notifId;
    sendJSON('../ajax/update_notification.php', {"notif_id": notifID})
    .then(data => data.text())
    .then(data => {
        localStorage.setItem('notifData', data)
        location.href = e.target.href;
    })
});