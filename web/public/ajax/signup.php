<?php
    session_start();
    require_once '../core/common.php';

    $login = $_POST['login'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    // check if this user already exists
    $user = select("SELECT * FROM `users` WHERE `login` = '$login' || `email` = '$email'");
    if (count($user) > 0) {
        $_SESSION['message'] = 'Такой пользователь уже есть';
        header('Location: /register');
        exit;
    }

    if ($password === $password_confirm) {
        $password = md5($password);
        execQuery("INSERT INTO `users` (`id`, `login`, `email`, `password`) VALUES (NULL, '$login', '$email', '$password')");
        $u_id = select("SELECT id FROM users WHERE login = '$login'")[0]['id'];

        $path = 'images/user_icons/' . $u_id.'.jpeg';
        if (!move_uploaded_file($_FILES['photo']['tmp_name'], '../' . $path)) {
            $_SESSION['message'] = 'Ошибка при загрузке сообщения';
            header('Location: /register');
        }


        $_SESSION['message'] = 'Регистрация прошла успешно!';
        header('Location: /login');

        mkdir("../images/$u_id", 0777, true);
    } else {
        $_SESSION['message'] = 'Пароли не совпадают';
        header('Location: /register');
    }

?>
