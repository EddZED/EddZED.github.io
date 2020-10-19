    <footer>
      <div class="footer d-flex align-items-center justify-content-between">
	    <div class="container">
	  	  <p class="txt_foot m-0 px-2">2012 - <?php $current_year = date ( 'Y' ); echo  $current_year;?>. Демонтаж-всем. Все права защищены.</p>  	  
	    </div>
	  </div>
    </footer>
    <button onclick="topFunction()" id="myBtn" title="Вверх"></button>
	
	<div class="overlay open">
    <div class="modal open-phone-block open" id="modal1">
	  <div class="pad-modal">
		<button type="button" class="btn-close-new" data-dismiss="modal">X</button>
		<form class="form__form callback-form" id="callbackForm" action="#">
		<fieldset class="form__fields form__hide-success content-form" style="color:#000;">
          <p class="form__title text-center mb-3">ЗАКАЗАТЬ КОНСУЛЬТАЦИЮ</p>
          <label>Имя: </label>
		  <input class="input-modal" name="name" type="text" required="">
          <label>Телефон: </label>
          <input class="input-modal" name="phone" type="phone" required="">
          <input type="hidden" name="go" value="callback">
          <div class="form-group" style="text-align:center;">
            <button class="btn-submit btn btn-dark" type="submit" style="width:auto;color:#000;">Отправить</button>
          </div>
        </fieldset>
        </form>
	  </div>
    </div>
  </div>
  
  <div class="overlay open">
    <div class="modal open-phone-block open" id="modal2">
	  <div class="pad-modal">
		<button type="button" class="btn-close-new" data-dismiss="modal">X</button>
		<form class="form__form callback-form" id="calcbackForm" action="#">
		<fieldset class="form__fields form__hide-success content-form" style="color:#000;">
          <p class="form__title text-center mb-3">РАССЧИАТЬ СТОИМОСТЬ</p>
          <label>Необходимая работа: </label>
		  <select class="input-modal" name="tip" required="">
		    <option>Демонтаж кирпичного дома</option>
			<option>Демотнаж дома из бруса</option>
			<option>Демонтаж садовой постройки или сарая</option>
			<option>Демонтаж бани</option>
			<option>Демонтаж дома после пожара</option>
			<option>Демонтаж старого дома</option>
			<option>Демонтаж каркасного дома</option>
			<option>Снос части дома</option>
			<option>Демонтаж панельного дома</option>
			<option>Снос щитового дома</option>
			<option>Разборка деревянного дома</option>
			<option>Демонтаж фундамента</option>
			<option>Демонтаж забора</option>
			<option>Демонтаж брусчатки</option>
			<option>Демонтаж асфальта</option>
		  </select>
          <!--<label>Материал демонтажа: </label>
          <select class="input-modal" name="steni" required="">
		    <option>Дерево</option>
			<option>Кирпич или шлакоблок</option>
			<option>Асфальт</option>
			<option>Кирпичные</option>
		  </select>-->
		  <div class="row">
		    <div class="col-6">
		      <label>Ширина (метры): </label>
              <input class="input-modal" name="shirina" type="text">
		    </div>
		    <div class="col-6">
		      <label>Длина (метры): </label>
              <input class="input-modal" name="dlina" type="text">
			</div>
		  </div>
		  <label>Телефон: </label>
          <input class="input-modal" name="phone" type="phone" required="">
          <input type="hidden" name="go" value="calc">
          <div class="form-group" style="text-align:center;">
            <button class="btn-submit btn btn-dark" type="submit" style="width:auto;color:#000;">Отправить</button>
          </div>
        </fieldset>
        </form>
	  </div>
    </div>
  </div>
<script type="text/javascript">
  window.onscroll = function() {scrollFunction()};

function scrollFunction() {
    if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
        document.getElementById("myBtn").style.display = "block";
    } else {
        document.getElementById("myBtn").style.display = "none";
    }
}

// When the user clicks on the button, scroll to the top of the document
function topFunction() {
    document.body.scrollTop = 0; // For Safari
    document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
}
</script>
    <script type="text/javascript" src="js/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="js/popper.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/mdb.min.js"></script>
    <script type="text/javascript" src="js/menu.js"></script>
    <script type="text/javascript" src="js/call.js"></script>
	<script type="text/javascript" src="js/modal.js"></script>
  </body>
</html>