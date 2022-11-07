getSession.then(d => executePage(d))

function executePage(asyncData) {
    // answers variables
    let createA = document.querySelector('.answers__create');
    let createAnswerField = document.querySelector('.answers__new');
    let answersField = document.querySelector('.answers__field');
    let sendA = document.querySelector('.answers__new-send');
    let answersToExclude = [];
    let questionID = getQuestionID();
    let php_session = asyncData['user'] ?? null;


    // add events
    createA.addEventListener('click', createNewAnswer);
    sendA.addEventListener('click', sendNewAnswer);
    answersField.addEventListener('click', answerInteractions)

    let moreAnswersBtn = document.querySelector('.answers__more span');
    if (moreAnswersBtn) moreAnswersBtn.addEventListener('click', displayAllAnswers);

    // check redirect
    checkRedirect();

    function createNewAnswer() {
        // check login
        if (!php_session) {
            displayAlertModal('myModal', '<h3>Вы должны быть зарегестрированы!</h3><br><a class="login-btn" href="/login"> Логин</a>');
            return false;
        }

        if (!this.classList.contains('decline')) {//if create new answer
            // upload image function
            const image_upload_handler_callback = (blobInfo, progress) => new Promise((resolve, reject) => {
                const xhr = new XMLHttpRequest();
                xhr.withCredentials = false;
                xhr.open('POST', '../ajax/tinyMCE_upload_image.php');
                
                xhr.upload.onprogress = (e) => {
                    progress(e.loaded / e.total * 100);
                };
                
                xhr.onload = () => {
                    if (xhr.status === 403) {
                        reject({ message: 'HTTP Error: ' + xhr.status, remove: true });
                        return;
                    }
                  
                    if (xhr.status < 200 || xhr.status >= 300) {
                        reject('HTTP Error: ' + xhr.status);
                        return;
                    }
                  
                    const json = JSON.parse(xhr.responseText);
                  
                    if (!json || typeof json.location != 'string') {
                        reject('Invalid JSON: ' + xhr.responseText);
                        return;
                    }
                  
                    resolve(json.location);
                };
                
                xhr.onerror = () => {
                  reject('Image upload failed due to a XHR Transport error. Code: ' + xhr.status);
                };
                
                const formData = new FormData();
                formData.append('file', blobInfo.blob(), blobInfo.filename());
                
                xhr.send(formData);
            });


            createA.innerHTML = 'Отменить';
            tinymce.init({
                selector: '.answers__new>textarea',
                plugins: 'codesample image',
                toolbar: "bold italic underline | codesample | image",
                image_dimensions: false,
                images_upload_url: '../ajax/tinyMCE_upload_image.php',
                content_style: 'img {max-width: 100%;max-height:500px;}',
                language:'ru',
                menubar:false,
                statusbar: false,
                images_upload_handler: image_upload_handler_callback
            });
        }
        else { //if decline creating new answer
            declineBtn();
        }
        
        createA.classList.toggle('decline');
        createAnswerField.classList.toggle('d-none');
    }

    function sendNewAnswer() {
        let content = tinymce.activeEditor.getContent();

        // if answer is empty
        if (content == '') {
            displayAlertModal('myModal', '<h3>Все поля должны быть заполнены!</h3>');
            return false;
        }

        sendA.removeEventListener('click', sendNewAnswer);
        createA.classList.toggle('decline');
        createAnswerField.classList.toggle('d-none');
        
        // get and transfer data
        let data = [
            content, // tinyMCE content
            questionID,
            php_session['id'],
        ];

        sendJSON('../ajax/upload_answer.php', data)
        .then(data => data.text())
        .then((data) => {
            // display answer
            displayUploadedAnswer(data);
            displayAlertModal('myModal', '<h3>Ответ добавлен!</a>');

            // add uploaded answer id to exclude
            answersToExclude.push(+data.id);
            sendA.addEventListener('click', sendNewAnswer);
        })

        declineBtn();
    }

    function declineBtn() {
        createA.innerHTML = 'Написать ответ';
        tinymce.activeEditor.setContent("");
        tinymce.activeEditor.remove()
    }

    function displayUploadedAnswer(data) {
        let answer = createAnswerEl(data);

        if (answersField.querySelector('h4')) {
            answersField.innerHTML = '';
        }
        answersField.prepend(answer);
    }
    
    function createAnswerEl(data) {
        let answerShell = document.createElement('div');
            answerShell.classList.add('answer__shell');
            answerShell.innerHTML = data;

        return answerShell;
    }

    function answerInteractions(e) {
        let el = e.target;
        let login_id;
        let answer_id = getDataset(el, 'data-answer-id');
        try {login_id = php_session.id}catch(e){login_id = null};

        // check login
        if (el.classList.contains('login-needed') && !login_id) {
            displayAlertModal('myModal', '<h3>Вы должны быть зарегестрированы!</h3><br><a class="login-btn" href="/login"> Логин</a>');
            return false;
        }

        //display comments
        let elems = {
            "answerComment": el.classList.contains('answer__comments-display'),
            "commentAnswer":el.classList.contains('comment__answers-display') && !el.classList.contains('comment-reply'),
            "commentReply":el.classList.contains('comment-reply')
        }
        if (elems.answerComment || elems.commentAnswer || elems.commentReply) {
            // check type
            let type = '';
            let commentID;
            let commentArea = el.parentElement.nextElementSibling;
            toggle()

            if (elems.commentAnswer) { //if display comment answers
                type = 'comment_answer';
                commentID = getDataset(el, 'data-comment-id');
            } else if (elems.commentReply) { // if display comment answer replies
                sendRequest({"type": "comment_reply"});
                return;
            }

            if (commentArea.children.length == 0) {
                // display comments if not displayed yet
                let viewProps = {
                    "a_id":answer_id,
                    "q_id":questionID,
                    "c_id":commentID,
                    "user_id": login_id,
                    "table": "comments",
                    "type": type
                }
                sendRequest(viewProps);
            }

            return;

            function sendRequest(viewProps) {
                sendJSON('../ajax/display_comments.php', viewProps)
                    .then(data => data.text())
                    .then(data => {
                        commentArea.innerHTML = data;
                    })
            }

            function toggle() {
                commentArea.classList.toggle('hidden');
                el.classList.toggle('active');
            }
        }
        
        // check login
        if (php_session === null) return;

        if(el.classList.contains('comment__send')) { //send comment
            // set delay
            answersField.removeEventListener('click', answerInteractions)

            // validate length
            let textArea = el.previousElementSibling.previousElementSibling;
            if (textArea.value.length == 0) {
                displayAlertModal('myModal', '<h3>Комментарий не может быть пустым!</h3>');
                answersField.addEventListener('click', answerInteractions)
                return false;
            }

            let parentID;
            let type = '';
            if (el.classList.contains('send-comment-answer')) {
                parentID = getDataset(el, 'data-comment-id');
                type = 'commentReply';
            }
            let answerAuthor = getDataset(el, "data-author");
            let data = {
                "question_id": questionID,
                "answer_id": answer_id,
                "user" : login_id,
                "author": answerAuthor,
                "message": textArea.value,
                "comment_id": parentID,
                "type": type
            };

            // send comment request
            sendJSON('../ajax/handle_comments.php', data)
            .then(data => data.text())
            .then(data => {
                // display uploaded comment
                let commentField = getCommentsField(el);
                let comm = data;
                let commShell = document.createElement('div');
                    commShell.classList.add('comment-shell');
                    commShell.innerHTML = comm;
                    textArea.value = '';
                
                // if 0 comments
                if (type == '') { //if send comment
                    if (commentField.querySelector('h5')) {
                        commentField.innerHTML = '';
                        commShell.classList.remove('comment-shell');
                    }
                }
                else if (el.classList.contains('reply')) {
                    el.parentElement.parentElement.classList.add('hidden');
                    el.parentElement.parentElement.previousElementSibling.children[1].classList.remove('active');
                }

                commentField.prepend(commShell);
                answersField.addEventListener('click', answerInteractions)
            })
        }
        else if (el.classList.contains('answer__likes') || el.classList.contains('comment__likes')) { // likes
            let likes = +el.innerHTML.replace(/\D/g, "");
            let reply = el.classList.contains('reply__likes')
            let author = getDataset(el, 'data-author');
            let reply_id = getDataset(el, 'data-comment-id-like');
            let comment_id = getDataset(el, 'data-comment-id');
            let data = {
                "answer_id": answer_id,
                "question_id": questionID,
                "reply_id": reply_id,
                "comment_id": comment_id,
                "user" : login_id,
                "reply": reply,
                "author": author
            };
            // add like
            if (!el.classList.contains('active')) {
                el.innerHTML = 'нравится '+ (likes+1);
                like(data);
            }
            else if (el.classList.contains('active')){ //remove like
                el.innerHTML = 'нравится '+ (likes-1);
                like(data);
            }

            el.classList.toggle('active');
        }

        // local fn's
        function like(data) {
            sendJSON('../ajax/like.php', data)
        }

        function getCommentsField(el) {
            // get comments block
            let out = el.parentElement.nextElementSibling;
            if (out != null) return out;

            // get comments replies block
            while (true) {
                el = el.parentElement;
                if (el.classList.contains('comments__area')) {
                    return el;
                };
            }
        }
    }
    
    function displayAllAnswers() {
        sendJSON('../ajax/display_all_answers.php',{"id": questionID, "exclude": answersToExclude})
        .then(data => data.text())
        .then(data => {
            moreAnswersBtn.parentElement.remove();
            answersField.innerHTML += data;
        })
    }

    
    function checkRedirect() {
        let data = JSON.parse(localStorage.getItem('notifData'));
        let answersSection = document.querySelector('.answers__field');
        if (!data) return;

        let type = data.type;
        let answer = document.querySelector(`[data-answer-id="${data['answer_id']}"]`);
        // always check answer
        displayAnswer(data['question_id'], data['answer_id'], data['parent_id'], data['comment_reply_id']);

        // remove notifData from localStorage
        localStorage.removeItem('notifData');

        function displayAnswer(qID, aID, cID, rID) {
            if (answer) answer.remove();
            displayCertainA(qID, aID, cID, rID);
        }

        function displayCertainA(qID, aID, cID, rID) {
            sendJSON('../ajax/display_certain_answer.php', {"q_id": qID, "a_id": aID})
            .then(data => data.text())
            .then(data => {
                // exlude displayed answer
                answersToExclude.push(aID);

                let answerShell = document.createElement('div');
                answerShell.classList.add('answer__shell');
                answerShell.innerHTML = data;
                answersSection.prepend(answerShell);

                // decrease answers count
                let countElem = document.querySelector('.answers-count');
                if (countElem) {
                    let count = +countElem.textContent - 1;
                    countElem.innerHTML = count;
                }

                handleComments(cID, rID);
            })
        }

        function displayComments(cID, rID, replies) {
            answer.click()

            let commentTimer = setInterval(() => {
                let displayedArea = answersSection.querySelector(`.answer__shell [data-comment-id="${cID}"]`);
                if (displayedArea) {
                    clearInterval(commentTimer);

                    // if display replies
                    if (replies) {
                        let comment = displayedArea.querySelector('.comment__answers-display');
                        comment.click();

                        let replyTimer = setInterval(() => {
                            let reply = displayedArea.querySelector(`[data-comment-id-like="${rID}"]`);
                            if (reply) {
                                clearInterval(replyTimer)
                                displayedArea.querySelector(`[data-comment-id-like="${rID}"]`).classList.add('highlight')
                            };
                        }, 200);
                    }
                    else displayedArea.classList.add('highlight');
                }
            }, 200);
        }

        function handleComments(cID, rID) {
            // handle comments
            answer = answersSection.querySelector(`.answer__comments-display`);
            if (type == 'comment') displayComments(cID, rID, false);
            else if (type == 'comment_answer') displayComments(cID, rID, true);
            else {
                // add highlight
                answersSection.children[0].children[0].classList.add('highlight');
            }
        }
    }
}