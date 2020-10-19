<!DOCTYPE html>
<html lang="ru">
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta http-equiv="x-ua-compatible" content="ie=edge">
<title>Демонтаж квартир</title>
<meta name="description" content="" />
<?php include "header.php"; ?>
<div class="bg_header_2"></div>
<div class="madal_window">
  <div style="width:100%;height:100%;"><	
  <div class="window">
  	<form class="form__form callback-form order p-4" id="callback" action="#" method="post">
  	    <fieldset class="form__fields form__hide-success">	
  		    <p class="head_form">Заказать звонок</p>
  		    <label for="name" style="color:#ccc">Ваше имя</label><br>
  		    <input type="text" name="name" class="form-input"><br>
  		    <label for="phone" style="color:#ccc">Ваш телефон</label><br>
  		    <input type="tel" name="phone" class="form-input">
  		    <input type="hidden" name="title" value="услуги">
    		<input type="hidden" name="go" value="order_service">
    		<input type="hidden" name="page" value="<?php echo $pageService;?>">
  		    <button class="btn btn_order mx-0 my-4" type="submit">заказать</button>
  		</fieldset>
  	</form>
  </div>	
</div>
<?php include "footer.php"; ?>