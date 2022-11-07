<?php
    session_start();
    if ($_SESSION['user']) {
        header('Location: /');
    }
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>site.ru</title>
    <link rel="stylesheet" href="./scss/login.css">
</head>
<body>

<body>

    <!-- Форма регистрации -->

    <form action="../ajax/signup.php" method="post" enctype="multipart/form-data">
        <label>Логин</label>
        <input type="text" name="login" placeholder="Введите свой логин"required>
        <label>Почта</label>
        <input type="email" name="email" placeholder="Введите адрес своей почты"required>
        <label>Изображение профиля</label>
        <input type="file" name="photo" accept="image/*" required>
        <label>Пароль</label>
        <input type="password" name="password" placeholder="Введите пароль"required>
        <label>Подтверждение пароля</label>
        <input type="password" name="password_confirm" placeholder="Подтвердите пароль"required>
        <button type="submit" class="register-btn">Зарегистрироваться</button>
        <p>
            У вас уже есть аккаунт? - <a href="/login">авторизируйтесь</a>!
        </p>
        <?php
            if ($_SESSION['message']) {
                echo '<p class="msg"> ' . $_SESSION['message'] . ' </p>';
            }
            unset($_SESSION['message']);
        ?>
    </form>
</body>
</html>