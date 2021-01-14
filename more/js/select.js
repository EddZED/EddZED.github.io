let select = function () {
  let selectHeader = document.querySelectorAll('.select__header');
  let selectItem = document.querySelectorAll('.select__item');

  selectHeader.forEach(item => {
    item.addEventListener('click', selectToggle)
  });

  selectItem.forEach(item => {
    item.addEventListener('click', selectChoose)
  });

  function selectToggle() {
    this.parentElement.classList.toggle('is-active');
  }

  function selectChoose() {
    let contentsel = this.innerHTML;
      select = this.closest('.select'),
      currentText = select.querySelector('.select__current');
    currentText.innerHTML = contentsel;
    select.classList.remove('is-active');

  }

};


select();