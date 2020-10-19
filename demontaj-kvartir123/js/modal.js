
		var modal = document.querySelector("#modal"),
		    modalOverlay = document.querySelector("#modal-overlay"),
		    closeButton = document.querySelector("#close-button"),
		    openButton = document.querySelector("#open-button");

	closeButton.addEventListener("click", function() {
	  modal.classList.toggle("closed");
	  modalOverlay.classList.toggle("closed");
	});

	openButton.addEventListener("click", function() {
	  modal.classList.toggle("closed");
	  modalOverlay.classList.toggle("closed");
	});
jQuery(function($){
	$(document).mouseup(function (e){ // событие клика по веб-документу
		var div = $(".modal"); // тут указываем ID элемента
		if (!div.is(e.target) // если клик был не по нашему блоку
		    && div.has(e.target).length === 0) { // и не по его дочерним элементам
			div.hide(); // скрываем его
		}
	});
});
