<?php
$formGo = $_POST["go"];

if($formGo) {

    // ===== Reference ============================
    $recaptchaOn = false; // true- включить рекаптчу; false- отключить рекаптчу
	if ($recaptchaOn) {
        $recaptcha = $_POST['g-recaptcha-response'];
    }
	if( isset($_POST["phone"]) ) {
        $phone = $_POST["phone"];
    }
    if( isset($_POST["name"]) ) {
        $name = $_POST["name"];
    }
    if( isset($_POST["mail"]) ) {
        $email = $_POST["mail"];
    }
    if( isset($_POST["text"]) ) {
        $emess = $_POST["text"];
    }
	if( isset($_POST["url1"]) ) {
        $url = $_POST["url1"];
    }
	if( isset($_POST["url2"]) ) {
        $urle = $_POST["url2"];
    }
	

    // ===== Variables =====
    $to = "ekbbriket@yandex.ru"; // E-mail на который присылать письмо
    $fromEmail = "info@web-ekb.ru"; // E-mail от имени которого приходит письмо. Почта на домене сайта.
    $subject = "Обращение из формы обратной связи";

    if ( $formGo == 'callback' ) {
        $subject = "Сообщение (trulypartners)";
    }
	if ( $formGo == 'callback2' ) {
        $subject = "Заказ в один клик (trulypartners)";
    }
	if ( $formGo == 'usluga' ) {
        $subject = "Заказ с калькулятора(trulypartners)";
    }
	

    function adopt($text) {
        return '=?UTF-8?B?'.base64_encode($text).'?=';
    }

    $message  = '<html><body>';
    $message .= "<table>";
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
	if (!empty($emess)) {
        $message .= "<tr>";
        $message .= "<td>";
        $message .= "<strong> Комментарий: </strong>";
        $message .= "</td>";
        $message .= "<td style='padding-left:12px;'>";
        $message .= "$emess";
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