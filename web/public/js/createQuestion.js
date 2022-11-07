getSession.then(d => executePage(d))

function executePage(asyncData) {
    let php_session = asyncData['user'];
    let fileInp = document.querySelector('#f');
    let images = [];
    let imagesDisplay = document.querySelector('.question__images-display');
    let form = document.forms[0];
    let dragIndicator = document.querySelector('.drag-indicator');

    // check login
    if (php_session == undefined) location.href = '/';

    // tinymce
    tinymce.init({
        selector: '.question__description>textarea',
        plugins: 'codesample',
        toolbar: "bold italic underline | codesample",
        language:'ru',
        menubar:false,
        statusbar: false,
    });


    form.onsubmit = function(e) {
        e.preventDefault();

        // validate
        let title = form.querySelector('.question_name').value.trim();
        let desc = tinymce.activeEditor.getContent();
        if (title == '' || desc == '') {
            displayAlertModal('myModal', '<h3>Все поля должны быть заполнены!</h3>');
            return false;
        }
        // send request
        data = {
            "title": title,
            "description": desc,
            "images": images
        };

        sendJSON('../ajax/upload_question.php', data)
        .then(data => data.text())
        .then(data => {
            window.onbeforeunload =null;
            location.href = '/question/'+data;
        })
    }

    // display by click
    fileInp.onchange = (e) => {
        imagesDisplay.classList.remove('hidden');
        let files = e.target.files;
        handleFiles(files)
    }

    // display by drag-n-drop
    ;['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        document.addEventListener(eventName, preventDefaults, false)
    })
    document.addEventListener('dragenter', () => {
        dragIndicator.classList.remove('hidden');
    })
    document.addEventListener('dragend', () => {
        dragIndicator.classList.add('hidden');
    })
    document.addEventListener('drop', (e) => {
        dragIndicator.classList.add('hidden');
        imagesDisplay.classList.remove('hidden');
        let files = e.dataTransfer.files;
        handleFiles(files);
    }, false)

    function handleFiles(files) {
        ([...files]).forEach(uploadFile)
    }

    function uploadFile(file) {
        // validate file
        if (file['type'].split('/')[0] != 'image') return false;

        let formData = new FormData()
        formData.append('file', file)
        formData.append('user', php_session['id']);
        fetch('../ajax/upload_question_image.php', {
            method: "POST",
            body: formData,
        })
        .then(data => data.text())
        .then(data => {
            images.push(data)
            imagesDisplay.innerHTML += `<div class='question__images-image'><div class='remove' data-path='${data}'><img src='../images/img/delete-icon.svg'></div><img class='expandable' src='${data}'></div>`;
        
            // remove image onclick
            imagesDisplay.onclick = function(e) {
                if (e.target.tagName == 'IMG') {
                    let removeEl = e.target.parentElement;
                    let path = [removeEl.dataset.path];
                    deleteImg(path);
                    removeEl.parentElement.remove();

                    // remove path from ${images}
                    let i = images.indexOf(path);
                    images.splice(i, 1);
                }
            }

        })
    }

    function deleteImg(data) {
        sendJSON('../ajax/delete_question_image.php', {"path": data})
    }

    function preventDefaults (e) {
        e.preventDefault()
        e.stopPropagation()
    }

}