<?
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>site.ru</title>
    <!-- css &js -->
    <link rel="stylesheet" href="../scss/style.css">
    <script src='../js/_getSession.js' defer></script>
    <script src='../js/functions.js' defer></script>
    <script src='../js/navbarLogic.js' defer></script>
    <script src='../js/expandImg.js' defer></script>
    <script type='module' src='../js/checkIcon.js'></script>
    <script src="../js/handleSearch.js" defer></script>
    <!-- !@@@! -->
    <?
    if ($url[0] == 'login' || $url[0] == 'register') {
      echo '<link href="../scss/login.css" rel="stylesheet">';
    }
    else if ($url[0] == 'profile') {
      if ($url[1] == 'change_icon') {
        echo '<link  href="../dist/cropper.css" rel="stylesheet">
        <script  type="module" src="../dist/cropper.js" defer></script>
        <script src="../js/handleNewIcon.js" defer></script>';
      }
      else if ($url[1] == 'notifications') {
        echo '<script src="../js/notifications.js" defer></script>';
      }
    }
    else if ($url[0] == 'question') {
      if ($url[1] == 'new') {
        echo '<script src="../js/createQuestion.js" defer></script>
      <script src="../js/beforeUnload.js" defer></script><script src="https://cdn.tiny.cloud/1/d6cgt3veoxqgph1gof0h81iuu47ufm0ot3ufaownd0l4he82/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>';
      }
      else if ($url[1] != '') {
        echo '<script src="https://cdn.tiny.cloud/1/d6cgt3veoxqgph1gof0h81iuu47ufm0ot3ufaownd0l4he82/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
        <script src="../js/createAnswer.js" defer></script>';
      }
    }
    ?>

    <!-- bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</head>
<body>
<header class="header">
    <!-- navbar -->
      <nav class="fixed-top navbar navbar-expand-lg">
      <div class="container">
      <a class="navbar-brand" href="/">Our<br><span>Forum</span></a>
        <div class="navbar__search"><input type="text" placeholder="поиск"><div class="navbar__search-output"></div></div>
        <div class="navbar__notifications">
          <a href="/profile/notifications">
            <?=notif_count()?>
            <img src="../images/img/notif.svg" >
          </a>
        </div>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <div class="bar bar-1"></div>
          <div class="bar bar-2"></div>
          <div class="bar bar-3"></div>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav navbar-nav-scroll" style="--bs-scroll-height:100vh;">
            <li class="nav-item">
              <?=drawLogin()?>
              <script type="module">
                import {checkIcons} from "../js/checkIcon.js";
                let icons = document.querySelectorAll("#icon-img");
                checkIcons(icons);
              </script>
            </li>
          </ul>
        </div>
      </div>
    </nav>
</header>
