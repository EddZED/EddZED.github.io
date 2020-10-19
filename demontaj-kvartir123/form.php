<div class="container">
  <div class="row d-flex align-items-center justify-content-center pb-3">
  	<h3 class="head_order px-3 pb-5">закажите демонтаж со скидкой 8%</h3>
    <div class="col-lg-7 col-md-6">
  	  <form class="form__form callback-form" id="callbackForm" action="">
  	    <fieldset class="form__fields form__hide-success">	
  		    <p class="head_form">Есть вопросы?</p>
  		    <p class="help_txt">- Поможем рассчитать стоимость работ</p>
  		    <label for="name" style="color:#ccc">Ваше имя</label><br>
  		    <input type="text" name="name" class="form-input" required><br>
  		    <label for="phone" style="color:#ccc">Ваш телефон</label><br>
  		    <input type="tel" name="phone" class="form-input" required>
  		    <input type="hidden" name="title" value="услуги">
    		<input type="hidden" name="go" value="skidkaback">
    		<input type="hidden" name="page" value="<?php echo $pageService;?>">
  		    <button class="btn btn_order mx-0 my-4" type="submit">отправить</button>
  		</fieldset>
  	  </form>
    </div>
    <div class="col-lg-5 col-md-6 text-center">
      <img src="img/img_form.png" class="img-form py-3" alt="скидка на демонтажные работы">
    </div>
  </div>    
</div>