window.addEventListener('DOMContentLoaded', () => { // Ждём загрузки DOM дерева 
  'use strict';

  const body = document.body;

  const vacancies = () => {
    const itemAll = document.querySelectorAll('.vacancies-item__link');
    const messageAll = document.querySelectorAll('.vacancies-item__message');


    body.addEventListener('click', (e) => {
      let target = e.target;

      const removeClass = (block) => {
        block.forEach((item) => {
          item.classList.remove('active');
        })
      }


      if (target.closest('.vacancies-item')) {
        target = target.closest('.vacancies-item');

        let link = target.querySelector('.vacancies-item__link'),
          message = target.querySelector('.vacancies-item__message');

        removeClass(itemAll);
        removeClass(messageAll);

        link.classList.add('active');
        message.classList.add('active');

      } else {
        removeClass(itemAll);
        removeClass(messageAll);

      }

    })
  }

  vacancies();

  const slider = (idSelector, slideRer = 2, slideRow = 1) => {

    const swiper = new Swiper(idSelector, {
      slidesPerView: slideRer,
        grid: {
          rows: slideRow,
        },

      // If we need pagination
      pagination: {
        el: '.swiper-pagination',
        type: 'fraction',
        formatFractionCurrent: addZero,
        formatFractionTotal: addZero
      },

      // Navigation arrows
      navigation: {
        nextEl: '.swiper-next',
        prevEl: '.swiper-prev',
      },

      // And if we need scrollbar
      scrollbar: {
        el: '.swiper-scrollbar',
      },
    });

    function addZero(num) {
      return (num > 9) ? num : '0' + num;
    }


  }
  slider('#partners-swiper', 5 , 2);
  slider('#life-swiper', 1);
  slider('#article-swiper', 1);

  const slide2 = (idSelector, next, prev, slideRer = 1) => {
    const swiper = new Swiper(idSelector, {  

      slidesPerView: slideRer,
      spaceBetween: 20,

      // Navigation arrows
      navigation: {
        nextEl: next,
        prevEl: prev,
      },
      
    });
  }

  slide2('#article2', '.article-swiper-next', '.article-swiper-prev')
  slide2('#read-swiper', '.read-next', '.read-prev', 3)




});