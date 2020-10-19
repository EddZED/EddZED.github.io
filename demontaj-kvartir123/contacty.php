<!DOCTYPE html>
<html lang="ru">
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta http-equiv="x-ua-compatible" content="ie=edge">
<title>Контактные данные</title>
<meta name="description" content=""/>
<?php include "header.php"; ?>
<?php $pageService="Страница контактов"?>
<div class="bg_header_2"></div>
<div class="bg_content_3">
<div class="container pb-4">
  <div class="row">
    <div class="w-100 pt-5"></div>
    <div class="col-md-6 pad_mobile">
      <h2 class="head_content_3 ml-3 p-3"><span class="line_2">Контакты</span></h2>
      <p class="txt_contact"><strong class="strong">Компания:</strong> ООО «Демонтаж»</p>
      <p class="txt_contact"><strong class="strong">Адрес:</strong> г. Москва, ул. Ленина, 25, оф. 100</p>
      <p class="txt_contact"><strong class="strong">Телефон:</strong><a href="tel:+78008008080"> 8-800-800-80-80</a></p>
      <p class="txt_contact"><strong class="strong">Электронная почта:</strong><a href="mailto:demontazh@demontazh.ru"> demontazh@demontazh.ru</a></p>
      <p class="head_content_2 pb-4"><span class="line">Оставить заявку</span></p>
      <form class="form_contact"  id="callback" action="#" method="post">
        <fieldset class="form__fields form__hide-success">
          <input type="text" id="" class="form-input mb-4" placeholder="Ваше имя">
          <input type="tel" id="" class="form-input mb-4" placeholder="Ваш телефон">
          <textarea class="form-input" placeholder="Ваше сообщение"></textarea>
          <div class="row">
            <div class="col-md-8 d-flex align-items-center">  
              <input type="checkbox" class="" id="defaultChecked2" checked>
              <label class="float-left pl-3 m-0" for="defaultChecked2">Я даю свое согласие на обработку персональных данных и соглашаюсь с <a href="">политикой конфиденциальности</a></label>
            </div>
            <div class="col-md-4 m-0 d-flex align-items-center">
              <button class="btn-outline btn_form_contact waves-effect" type="submit">Отправить</button>
              <input type="hidden" name="title" value="услуги">
              <input type="hidden" name="go" value="order_service">
              <input type="hidden" name="page" value="<?php echo $pageService;?>">
            </div>
          </div>  
        </fieldset>
      </form>
    </div>	
    <div class="col-md-6">
      <div class="map pb-5">
        <script type="text/javascript" charset="utf-8" async src="https://api-maps.yandex.ru/services/constructor/1.0/js/?um=constructor%3Af56cde54034f9a0d4df83f246d74208caa344b19f75fa3bb92462591f7267054&amp;width=100%25&amp;height=520&amp;lang=ru_RU&amp;scroll=false"></script>
      </div>  
    </div>
  </div>
</div>
<?php include "form.php";?>
</div>  





<?php include "footer.php"; ?>