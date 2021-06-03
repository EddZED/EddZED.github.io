$(document).ready(function () {
	
    $('select').styler({
		selectSearch: true,
	});

    if ($(window).width() < 992) { 

        $('.show-filter').on('click', function (e) {
            $('.filter-mobile').slideToggle();
        });
    
        $('.close-filter').on('click', function (e) {
            $('.filter-mobile').slideToggle();
        });

        $('.mobile-filter-block-show').on('click', function (e) {
            $('.mobile-filter-block-content').slideToggle();
        });

        $('.left-menu__show').on('click', function (e) {
            $('.left-menu').slideToggle();
        });

        $('.lk-menu__show').on('click', function (e) {
            $('.lk-menu').slideToggle();
        });

        $('.lk-menu__close').on('click', function (e) {
            $('.lk-menu').slideToggle();
        });

        $('.catalog-fiter__show').on('click', function (e) {
            $('.catalog-fiter__content').slideToggle();
        });

        $('.catalog-fiter__close').on('click', function (e) {
            $('.catalog-fiter__content').slideToggle();
        });
    } 
    
    $('[data-role=toggle-block]').each(function() {
		var $block = $(this);

		$block.on('click.toggle', '[data-role=toggle-btn]', function() {
			var $btn = $(this);
			var $data = $block.find('[data-role=toggle-data]');

			// toggle $btn
			$btn.toggleClass('shown');
	
			// show/hide $data
			$data.slideToggle(200);
		});
	});

    $('.slider-for').slick({
        dots: false,
        slidesToShow: 1,
        slidesToScroll: 1,
		vertical: true,
		adaptiveHeight:true,
		infinite:true,
		draggable:true,
        asNavFor: '.slider-nav', 
		
    });
    
    $('.slider-nav').slick({
        slidesToShow: 3,
		vertical: true,
		adaptiveHeight:true,
		infinite:false,
        slidesToScroll: 1,
        asNavFor: '.slider-for', 
		focusOnSelect: true,
        responsive: [
            {
              breakpoint: 1200,
              settings: {
                slidesToShow: 2,
                slidesToScroll: 1,
              }
            },
            {
              breakpoint: 768,
              settings: {
                slidesToShow: 1,
                slidesToScroll: 1
              }
            }
            ]
    });

    $(".product-page__images").slick({
        dots: false,
        infinite: true,
        slidesToShow: 5,
        slidesToScroll: 1,
    	responsive: [
		{
		  breakpoint: 1200,
		  settings: {
			slidesToShow: 4,
			slidesToScroll: 1,
		  }
		},
		{
            breakpoint: 576,
            settings: {
              slidesToShow: 2,
              slidesToScroll: 1
            }
          }
		]
	});

    $(".page-product__sider").slick({
        dots: false,
        infinite: true,
        slidesToShow: 5,
        slidesToScroll: 1,
    	responsive: [
		{
		  breakpoint: 1200,
		  settings: {
			slidesToShow: 4,
			slidesToScroll: 1,
		  }
		},
        {
            breakpoint: 992,
            settings: {
              slidesToShow: 3,
              slidesToScroll: 1,
            }
          },
		{
		  breakpoint: 576,
		  settings: {
			slidesToShow: 2,
			slidesToScroll: 1
		  }
		}
		]
	});
	
	
	const block = document.querySelector('.catalig-titile');

	if (block) {
		block.addEventListener('click' , (e)=> {
			if (e.target.closest('.filter-link')) {
			e.preventDefault();
				
			const collection = document.querySelectorAll('.filter-link'),
	    		  menu = e.target.parentNode.querySelector('.filter-link');
				
			collection.forEach((element) => {
				element.classList.remove('active')
			});
				
			menu.classList.add('active')
		}
		});
	}

	$("#phone").inputmask("+7 (999) - 999 - 99 - 99");
	$("#phone1").inputmask("+7 (999) - 999 - 99 - 99");
	
	$('.add-link').on('click', function (e) {
		e.preventDefault();
        $('.add-profile').fadeIn();
    });

});
