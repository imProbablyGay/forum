<?php
$user = $_POST['user'];
$uploadDir = "../images/$user/";
$typeFile = explode('/', $_FILES['file']['type']);
$uploadFile = $uploadDir . basename(md5($_FILES['file']['tmp_name'].time()).'.'.$typeFile[1]);
if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadFile)) {
    $response = ['result'=>'OK', 'data'=>'../'.$uploadFile];
} else {
    $response = ['result'=>'ERROR', 'data'=>''];
}
echo $uploadFile;