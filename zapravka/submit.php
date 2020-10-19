<?php
$formGo = $_POST["go"];

if($formGo) {

// ===== Reference ============================
$recaptchaOn = false; // true- включить рекаптчу; false- отключить рекаптчу
if ($recaptchaOn) {
$recaptcha = $_POST['g-recaptcha-response'];
}
if( isset($_POST["page"]) ) {
$page = $_POST["page"];
}
if( isset($_POST["title"]) ) {
$title = $_POST["title"];
}
if( isset($_POST["name"]) ) {
$name = $_POST["name"];
}
if( isset($_POST["email"]) ) {
$email = $_POST["email"];
}
if( isset($_POST["phone"]) ) {
$phone = $_POST["phone"];
}
if( isset($_POST["text"]) ) {
$text = $_POST["text"];
}
if( isset($_POST["tirazh"]) ) {
$tirazh = $_POST["tirazh"];
}
if( isset($_POST["size"]) ) {
$size = $_POST["size"];
}
if( isset($_POST["type"]) ) {
$type = $_POST["type"];
}
if( isset($_POST["color"]) ) {
$color = $_POST["color"];
}
if( isset($_POST["lam"]) ) {
$lam = $_POST["lam"];
}
if( isset($_POST["crugl"]) ) {
$crugl = $_POST["crugl"];
}
if( isset($_POST["result"]) ) {
$result = $_POST["result"];
}


// ===== Variables =====
$to = "eddie_nt@mail.ru"; // E-mail на который присылать письмо
$fromEmail = "info@web-ekb.ru"; // E-mail от имени которого приходит письмо. Почта на домене сайта.
$subject = "Обращение из формы обратной связи";

if ( $formGo == 'order_service' ) {
$subject = "Заказ онлайн";
}
if ( $formGo == 'consultback' ) {
$subject = "Оставлено сообщение";
}
if ( $formGo == 'pechat' ) {
$subject = "Заказана печать";
}

function adopt($text) {
return '=?UTF-8?B?'.base64_encode($text).'?=';
}

$message = '<html><body>';
$message .= "<table>";
if (!empty($page)) {
$message .= "<tr>";
$message .= "<td>";
$message .= "<strong> Заказ: </strong>";
$message .= "</td>";
$message .= "<td style='padding-left:12px;'>";
$message .= "$page";
$message .= "</td>";
$message .= "</tr>";
}
if (!empty($refil)) {
$message .= "<tr>";
$message .= "<td>";
$message .= "<strong> Заказ: </strong>";
$message .= "</td>";
$message .= "<td style='padding-left:12px;'>";
$message .= "<strong> $refil </strong>";
$message .= "</td>";
$message .= "</tr>";
}
if (!empty($name)) {
$message .= "<tr>";
$message .= "<td>";
$message .= "<strong> Имя: </strong>";
$message .= "</td>";
$message .= "<td style='padding-left:12px;'>";
$message .= "$name";
$message .= "</td>";
$message .= "</tr>";
}
if (!empty($email)) {
$message .= "<tr>";
$message .= "<td>";
$message .= "<strong> E-mail: </strong>";
$message .= "</td>";
$message .= "<td style='padding-left:12px;'>";
$message .= "$email";
$message .= "</td>";
$message .= "</tr>";
}
if (!empty($phone)) {
$message .= "<tr>";
$message .= "<td>";
$message .= "<strong> Телефон: </strong>";
$message .= "</td>";
$message .= "<td style='padding-left:12px;'>";
$message .= "$phone";
$message .= "</td>";
$message .= "</tr>";
}
if (!empty($text)) {
$message .= "<tr>";
$message .= "<td>";
$message .= "<strong> Сообщение: </strong>";
$message .= "</td>";
$message .= "<td style='padding-left:12px;'>";
$message .= "$text";
$message .= "</td>";
$message .= "</tr>";
}
if (!empty($tirazh)) {
$message .= "<tr>";
$message .= "<td>";
$message .= "<strong> Тираж: </strong>";
$message .= "</td>";
$message .= "<td style='padding-left:12px;'>";
$message .= "$tirazh шт.";
$message .= "</td>";
$message .= "</tr>";
}
if (!empty($size)) {
$message .= "<tr>";
$message .= "<td>";
$message .= "<strong> Размер: </strong>";
$message .= "</td>";
$message .= "<td style='padding-left:12px;'>";
$message .= "$size см";
$message .= "</td>";
$message .= "</tr>";
}
if (!empty($type)) {
$message .= "<tr>";
$message .= "<td>";
$message .= "<strong> Тип бумаги: </strong>";
$message .= "</td>";
$message .= "<td style='padding-left:12px;'>";
$message .= "$type";
$message .= "</td>";
$message .= "</tr>";
}
if (!empty($color)) {
$message .= "<tr>";
$message .= "<td>";
$message .= "<strong> Цветность: </strong>";
$message .= "</td>";
$message .= "<td
 
style='padding-left:12px;'>";
$message .= "$color";
$message .= "</td>";
$message .= "</tr>";
}
if (!empty($lam)) {
$message .= "<tr>";
$message .= "<td>";
$message .= "<strong> Ламинация: </strong>";
$message .= "</td>";
$message .= "<td style='padding-left:12px;'>";
$message .= "$lam";
$message .= "</td>";
$message .= "</tr>";
}
if (!empty($crugl)) {
$message .= "<tr>";
$message .= "<td>";
$message .= "<strong> Скругление углов: </strong>";
$message .= "</td>";
$message .= "<td style='padding-left:12px;'>";
$message .= "$crugl";
$message .= "</td>";
$message .= "</tr>";
}


$message .= "</table><br><br>";
$message .= '</body></html>';
$headers = "MIME-Version: 1.0" . PHP_EOL .
"Content-Type: text/html; charset=utf-8" . PHP_EOL .
'From: '.adopt($name).' <'.$fromEmail.'>' . PHP_EOL .
'Reply-To: '.adopt($name).' <'.$email.'> ' . PHP_EOL;

if ( $recaptchaOn ) {
if (!empty($recaptcha)) {
$secret = '6LfMJSgTAAAAABw4lECZsLP5krXztMRZC0_Fgt3O';
$url = "//www.google.com/recaptcha/api/siteverify?secret=".$secret ."&response=".$recaptcha."&remoteip=".$_SERVER['REMOTE_ADDR'];

$response = file_get_contents("//www.google.com/recaptcha/api/siteverify?secret=".$secret ."&response=".$recaptcha."&remoteip=".$_SERVER['REMOTE_ADDR']);

if ( $response.success === false ) {
$answer = '2';
} else {
if (mail($to, adopt($subject), $message, $headers)) {
$answer = '1';
} else {
$answer = '0';
}
}

} else {
$answer = '3';
}
} else {
if (mail($to, adopt($subject), $message, $headers)) {
$answer = '1';
} else {
$answer = '0';
}
}

die($answer);

}
?>