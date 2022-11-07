<?php
session_start();
require_once '_header.php';
?>

<body>
    <div class="container">
        <div class="row question">
            <div class="col-12 question__title">
                <h2>Задайте любой вопрос</h2>
                <br>
            </div>

            <form class="question__body" method='POST'>
                <div class="col-12 question__name">
                    <h4>Суть вопроса</h4>
                    <p>Сформулируйте вопрос так, чтобы сразу было понятно, о чём речь.</p>
                    <input type="text" class='question_name'>
                </div><br>
                <div class="col-12 question__description">
                    <h4>Детали вопроса</h4>
                    <p>Опишите в подробностях свой вопрос, чтобы получить более точный ответ.</p>
                    <textarea></textarea>
                </div><br>
                <div class="col-12 question__images">
                    <h4>Картинки</h4>
                    <p>Добавьте, если нужно, картинки, которые помогут понять вопрос.</p>
                    <div class="question__media">
                        <input type="file" multiple accept="image/*" id="f" name='question_images'>
                        <label class='images' for="f">Выберите или перетащите файлы</label>
                    </div>
                    <div class="question__images-display hidden"></div>
                </div>
                <div class="col-12 question__send">
                    <input type="submit" value='Спросить'>
                </div>
            </form>
        </div>
    </div>
    </div>

    <!-- drag indicator -->
    <div class="drag-indicator hidden">
        <span>Перетащите файлы сюда</span>
    </div>

</body>