<?php
session_start();
require_once '_header.php';
require_once './core/QuestionPage.php';

$Q_ID = filter_var($url[1], FILTER_SANITIZE_NUMBER_INT);
$question = new Question($Q_ID);
if ($question->data == '') $question->not_found();
$displayQ = $question->display();

$answers = new Answers($Q_ID);
$displayA = $answers->get_answers();

?>

<body>
    <div class="container">
        <div class="row question">
            <div class="question__body">
                <div class="col-12 question__author">
                    <?=$displayQ['author']?>
                </div>
                <div class="col-12 question__name">
                    <h4><?=$displayQ['title']?></h4>
                </div><br>
                <div class="col-12 question__description">
                    <p><?=$displayQ['description']?></p>
                </div><br>
                <?=$displayQ['images']?>
            </div>
        </div>
        <div class="row question__answers answers">
            <div class="col-12">
                <div class="answers__title">
                    <span>Ответы на вопрос:</span>
                    <span class="answers__create">Написать ответ</span>
                </div>
            </div>
            <div class="col-12">
                <div class="answers__display">
                    <div class="answers__new d-none">
                        <textarea></textarea>
                        <div class="answers__new-send"><span>Отправить</span></div>
                    </div>
                    <div class="answers__field"><?=$displayA?></div>
                </div>
            </div>
        </div>
    </div>

</body>

