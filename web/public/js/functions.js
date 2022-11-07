(function addModal() {
    document.body.innerHTML += `<!-- modal -->
    <div class="modal" tabindex="-1" id='myModal'>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            <div class="modal-body" style='text-align:center;'>
                <!-- add text by js -->
            </div>
        </div>
    </div>`;
})();

function displayAlertModal(modalId, modalBody) {
    new bootstrap.Modal(document.getElementById(modalId), {
        keyboard: true
    }).show()
    
    document.querySelector(`#${modalId} .modal-body`).innerHTML = modalBody;
}

function sendJSON(url, data) {
    return fetch(url, {
        method: "POST",
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data),
    })
}

// get closest attr of certain elem
function getDataset(el, ...attrs) {
    let out = {};

    attrs.forEach(attr => {
        let _el = el;
        let count = 0;
        while (true) {
            count++;
            if (_el == null || _el.tagName == "HEAD") return null;
            _el = _el.previousElementSibling ?? _el.parentElement;
            
            if (_el.getAttribute(attr) != undefined) {
                out[count] = _el.getAttribute(attr);
                return;
            };
        }
    })
    return out[Object.keys(out)[0]];
}

// get question id
function getQuestionID() {
    return parseInt(location.href.split('/').reverse()[0])
}

//remove deleted element from DOM
function removeDeletedAnswer(el) {
    while (true) {
        el = el.previousElementSibling ?? el.parentElement;
        let elClasses = Array.from(el.classList);
        if (elClasses.includes('comment') || elClasses.includes('answer') || elClasses.includes('search__question')) {
            el.remove();
            return;
        };
    }
}

function handleDelete(e) {
    getSession.then(d => {
        let php_session = d.user;
        let el = e.target;

        if (el.classList.contains('delete')) { //delete
            let answer_id = getDataset(el, 'data-answer-id');
            let question_id = getDataset(el, 'data-question-id');
            let login_id;
            try {login_id = php_session.id}catch(e){login_id = null};

            let comment_id = getDataset(el, 'data-comment-id');
            let reply_id = getDataset(el, 'data-comment-id-like');
            let data = {
                "answer_id": answer_id,
                "question_id": question_id ?? getQuestionID(),
                "comment_id": comment_id,
                "reply_id": reply_id,
                "user" : login_id,
                "reply": el.classList.contains('reply')
            };

            // remove answer from page
            removeDeletedAnswer(el);
        
            sendJSON('../ajax/delete_answer.php',data)
                .then(data => data.text())
                .then(data => console.log(data))
        }
    })
}