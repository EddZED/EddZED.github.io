<h3 class="head_form">Оставить онлайн заявку</h3>
<form action="#" class="form__form callback-form " id="zakazbackForm" method="post" style="text-align:center;">
  <fieldset class="form__fields form__hide-success">
    <input type="text" name="name" placeholder="Имя" class="form_win" required><br> 
    <input type="tel" name="phone" placeholder="Номер телефона" class="form_win" required><br><br>
    <input type="hidden" name="title" value="услуги">
    <input type="hidden" name="go" value="order_service">
    <input type="hidden" name="page" value="<?php echo $pageService;?>">
    <input class="btn_main" type="submit" value="Отправить"> 
  </fieldset>
</form><br><br>