<!DOCTYPE html>
<html lang="ru">
<meta charset="utf-8">
<title>Наши контакты</title>
<meta name="description" content="" />
<?php include "header.php"; ?>
<div class="container pad_mob">
  <div class="frame_bg">
  	<div class="col-sm-9 pad_mob">
  	  <div class="bread_crumb">
  	    <p><a href="/ed/zapravka/" class="link_crumb">Главная</a> → Наши контакты</p>
      </div>
      <h3 class="head_descrip">Контактная информация</h3><hr>
      <p class="txt_cont_inf"><strong>Адрес:</strong> г. Санкт-Петербург, пр-т. Просвещения 35</p>
      <p class="txt_cont_inf"><strong>Телефон: </strong><a href="tel:+78888888888" class="phone_cont_inf">+7(888) 888-88-88</a></p>
      <p class="txt_cont_inf"><strong>Email: </strong> <a href="mailto:info@info.ru" class="link_mail_foot">info@info.ru</a></p><br>
      <div class="map">
        <script type="text/javascript" charset="utf-8" async src="https://api-maps.yandex.ru/services/constructor/1.0/js/?um=constructor%3A9a2727db13ac4fb381cbd736e951978039aa14dc748c0d1a759c899885ff98c2&amp;width=100%25&amp;height=400&amp;lang=ru_RU&amp;scroll=false"></script>
      </div>
      <h3 class="head_form_cont">Оставить сообщение</h3>
      <form action="#" class="form_cont form__form callback-form" id="feedbackForm" method="post">
        <fieldset class="form__fields form__hide-success">
        <div class="col-sm-6 pad_mob" style="padding-left:0;">
          <label for="name" style="margin-top:10px;">Ваше имя</label>
          <input type="text" name="name" placeholder="" class="form_win_2"> 
        </div>
        <div class="col-sm-6 pad_mob" style="padding-right:0;">
          <label for="name" style="margin-top:10px;">Ваш E-mail</label>
          <input type="mailto:" name="email" placeholder="" class="form_win_2" required>
        </div>
        <input type="hidden" name="title" value="сообщение">
        <input type="hidden" name="go" value="consultback">
        <label for="name" style="margin-top:5px;">Ваше сообщение</label>
        <textarea name="text" class="form_win_place"></textarea><br><br>
        <input class="btn_main" type="submit" value="Отправить"> 
        </fieldset>
      </form>
    </div>
    <?php include "our_service.php";?>
    <div class="clearfix"></div>
  </div>	
</div>
<?php include "footer.php";?> 