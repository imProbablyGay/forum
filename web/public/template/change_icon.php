<?php
session_start();
require '_header.php';
?>

<body >
    <div class="container">
        <div class="row">
            <div class="col-12 change-icon d-flex justify-content-center align-items-center" style='height:100vh;'>
                <input type="file" multiple accept="image/*" id="f" style='display:none;'>
                <label for="f" class='change-icon__btn'>Выберите картинку</label>
            </div>
        </div>
    </div>
</body>
