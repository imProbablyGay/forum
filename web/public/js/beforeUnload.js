window.onbeforeunload = deleteQuestionMedia;

function deleteQuestionMedia () {
    let data = images;
    fetch('../ajax/delete_question_image.php', {
        method: "POST",
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({"path": data}),
    })
}