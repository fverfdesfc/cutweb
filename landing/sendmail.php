<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

try {
    $pdo = new PDO("mysql:host=192.168.2.200;dbname=travelli_travel;charset=utf8", "travelli_travel", "1234");
} catch (PDOException $err) {
    die("資料庫無法連接");
}
$email = $_POST['email'];
// $sql =  "select * from member where uemail='$email'";
$stmt = $pdo->prepare("select * from member where uemail=?");
$stmt->execute(array($email));
$row = $stmt->fetch();
// $stmt = $pdo->prepare($sql);
// $row = $stmt->fetch();
if (isset($email)) {
    $u_id = $row['uid'];
    $token = md5($u_id . $row['uname'] . $row['upassword']);
    $url = "http://localhost/專題_台南遊/reset_pwd.php?email=" . $email . "&token=" . $token;
    $time = date('Y-m-d H:i');
    $update = time();
    $result = sendemail($time, $email, $url);
    if ($result) {
        $stmt = $pdo->prepare("update member set getpasstime='$update' where uid='$u_id'");
    }
}
function sendemail($time, $email, $url)
{
    require_once "PHPMailer/PHPmailer.php";
    require_once "PHPMailer/SMTP.php";
    require_once "PHPMailer/Exception.php";

    $mail = new PHPMailer();
    $mail->CharSet = "UTF-8";
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'ghj21189590@gmail.com';
    $mail->Password = 'john21189590';
    $mail->Port = 465;
    $mail->SMTPSecure = 'ssl';
    $mail->isHTML(true);
    $mail->setFrom('ghj21189590@gmaill.com', 'RaITa_ PoPo');
    $mail->addAddress($email);
    $mail->Subject = "我要找回密碼";
    $mail->Body = "親愛的" . $email . "：<br/>您在" . $time . "您提交了找回密碼請求。請點選下面的連結重置密碼(24小時內有效)。<br/><a href='" . $url . "' target='_blank'>" . $url . "</a>";
    if ($mail->send()) {
        $status = "sucess";
        $response = "Email is sent";
    } else {
        $status = "failed";
        $response = "Something is wrong: <br>" . $mail->ErrorInfo;
    }

    header('location:forget.php');
    exit();
}
