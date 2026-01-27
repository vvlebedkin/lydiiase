$(window).on('load', function () {
    setTimeout(() => {
        $('.loader_body').addClass('done')
    }, 300);
});


$(document).ready(function () {

    $('.top_lines-close').click(function () {
        $('.top_lines-wrapper').addClass('hide')
    })

    $(window).scroll(function () {
        if ($(window).scrollTop() > 100) {
            $('.header').addClass('fixed')
        }
        else {
            $('.header').removeClass('fixed')
        }
    })

    const delay = 4000;

    new Swiper('.main_slider', {
        loop: true,
        navigation: {
            nextEl: '.main_slider-arrow.next',
            prevEl: '.main_slider-arrow.prev',
        },

        autoplay: { delay },
        on: {
            init() {
                const bar = this.el.querySelector('.main_progress span');
                const reset = () => {
                    bar.style.transition = 'none';
                    bar.style.width = '0';
                    bar.offsetWidth;
                    bar.style.transition = `width ${delay}ms linear`;
                    bar.style.width = '100%';
                };
                reset();
                this.on('slideChange', reset);
            }
        }
    });










    function initCatalogSlider(containerClass) {
        const slider = new Swiper(containerClass, {
            pagination: {
                el: containerClass + ' .catalog_item-pagination',
                clickable: true,
            },
            effect: 'fade',
            fadeEffect: {
                crossFade: true
            },
        });

        const paginationContainer = document.querySelector(containerClass + ' .catalog_item-pagination');

        if (paginationContainer) {
            paginationContainer.addEventListener('mouseover', (e) => {
                const bullet = e.target.closest('.swiper-pagination-bullet');
                if (!bullet) return;

                const allBullets = paginationContainer.querySelectorAll('.swiper-pagination-bullet');
                const index = Array.from(allBullets).indexOf(bullet);

                if (index !== -1) {
                    slider.slideTo(index);
                }
            });
        }

        return slider;
    }

    const sliders = document.querySelectorAll('.catalog_item-slider');
    sliders.forEach((slider, index) => {
        slider.classList.add(`catalog-slider-${index}`);
        initCatalogSlider(`.catalog-slider-${index}`);
    });




    new Swiper('.news_slider', {
        loop: false,
        pagination: {
            el: '.news_pagination',
            clickable: true,
        },
        navigation: {
            nextEl: '.news_arrow.next',
            prevEl: '.news_arrow.prev',
        },
    });




    new Swiper('.gallery_slider', {
        loop: false,
        slidesPerView: 'auto',
        navigation: {
            nextEl: '.gallery_left .gallery_arrow.next',
            prevEl: '.gallery_left .gallery_arrow.prev',
        },
        breakpoints: {
            320: {
                navigation: {
                    nextEl: '.gallery_slider .gallery_arrow.next',
                    prevEl: '.gallery_slider .gallery_arrow.prev',
                },
            },
            992: {
                navigation: {
                    nextEl: '.gallery_left .gallery_arrow.next',
                    prevEl: '.gallery_left .gallery_arrow.prev',
                },
            }
        }
    });



    $('.form_btn').click(function (e) {
        e.preventDefault()
        $('.subscribe_left-prev').addClass('hide')
        $('.subscribe_left-back').addClass('show')
    })

    $('.header_search').click(function () {
        if (!$('.header_search').hasClass('active')) {
            $(this).addClass('active')
        }
    })

    $('.header_search-close').click(function () {
        setTimeout(() => {
            $('.header_search').removeClass('active')
        }, 50);
    })





    $('.burger_menu').click(function () {
        $('.burger_menu, .header_inner').toggleClass('active')
    })



    $('.menu li').click(function () {
        $(this).find('.menu_dropdown').slideToggle(400)
    })

    $(".catalog_filter-price").each(function () {
        var $slider = $(this);
        var $container = $slider.closest('.catalog_filter-slider');
        var $minSpan = $container.find('.filter_slider-value.min span');
        var $maxSpan = $container.find('.filter_slider-value.max span');
        var $minInput = $container.find('.min-inp');
        var $maxInput = $container.find('.max-inp');

        var minPrice = 0;
        var maxPrice = 30000;


        var updatingFromSlider = false;


        function formatNumber(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
        }


        function parsePrice(value) {
            if (value === '' || value === ' ') return null;
            var parsed = parseInt(value.toString().replace(/\s+/g, '').replace(/[^\d]/g, ''));
            return isNaN(parsed) ? null : parsed;
        }

        $slider.slider({
            range: true,
            min: minPrice,
            max: maxPrice,
            values: [minPrice, maxPrice],
            slide: function (event, ui) {
                updatingFromSlider = true;


                $minSpan.text(formatNumber(ui.values[0]));
                $maxSpan.text(formatNumber(ui.values[1]));


                $minInput.val(formatNumber(ui.values[0]) + ' ₽');
                $maxInput.val(formatNumber(ui.values[1]) + ' ₽');

                updatingFromSlider = false;
            },
            change: function (event, ui) {
                updatingFromSlider = true;

                $minSpan.text(formatNumber(ui.values[0]));
                $maxSpan.text(formatNumber(ui.values[1]));
                $minInput.val(formatNumber(ui.values[0]) + ' ₽');
                $maxInput.val(formatNumber(ui.values[1]) + ' ₽');

                updatingFromSlider = false;
            }
        });


        function updateSliderFromInputs() {
            if (updatingFromSlider) return;

            var minVal = parsePrice($minInput.val());
            var maxVal = parsePrice($maxInput.val());


            if (minVal === null && maxVal === null) return;


            var currentValues = $slider.slider("values");
            if (minVal === null) minVal = currentValues[0];
            if (maxVal === null) maxVal = currentValues[1];


            minVal = Math.min(Math.max(minVal, minPrice), maxPrice);
            maxVal = Math.min(Math.max(maxVal, minPrice), maxPrice);


            if (minVal > maxVal) {
                var temp = minVal;
                minVal = maxVal;
                maxVal = temp;
            }


            $slider.slider("values", [minVal, maxVal]);


            var formattedMin = formatNumber(minVal);
            var formattedMax = formatNumber(maxVal);

            $minSpan.text(formattedMin);
            $maxSpan.text(formattedMax);


            if (!$minInput.is(':focus')) {
                $minInput.val(formattedMin + ' ₽');
            }
            if (!$maxInput.is(':focus')) {
                $maxInput.val(formattedMax + ' ₽');
            }
        }


        $minInput.on('input', function () {
            if (updatingFromSlider) return;

            var value = $(this).val();


            var numValue = parsePrice(value);

            if (numValue === null) {

                $(this).val('');
            } else {
                $(this).val(formatNumber(numValue));
            }
        });

        $maxInput.on('input', function () {
            if (updatingFromSlider) return;

            var value = $(this).val();
            var numValue = parsePrice(value);

            if (numValue === null) {
                $(this).val('');
            } else {
                $(this).val(formatNumber(numValue));
            }
        });


        var updateTimeout;
        function delayedUpdate() {
            clearTimeout(updateTimeout);
            updateTimeout = setTimeout(updateSliderFromInputs, 300);
        }

        $minInput.on('keyup', delayedUpdate);
        $maxInput.on('keyup', delayedUpdate);


        $minInput.on('blur', function () {
            var val = $(this).val();
            if (val === '' || val === ' ') {
                var currentValues = $slider.slider("values");
                $(this).val(formatNumber(currentValues[0]) + ' ₽');
            } else if (!val.includes('₽')) {
                var numValue = parsePrice(val);
                if (numValue !== null) {
                    $(this).val(formatNumber(numValue) + ' ₽');
                }
            }
            updateSliderFromInputs();
        });

        $maxInput.on('blur', function () {
            var val = $(this).val();
            if (val === '' || val === ' ') {
                var currentValues = $slider.slider("values");
                $(this).val(formatNumber(currentValues[1]) + ' ₽');
            } else if (!val.includes('₽')) {
                var numValue = parsePrice(val);
                if (numValue !== null) {
                    $(this).val(formatNumber(numValue) + ' ₽');
                }
            }
            updateSliderFromInputs();
        });


        var initialValues = $slider.slider("values");
        $minSpan.text(formatNumber(initialValues[0]));
        $maxSpan.text(formatNumber(initialValues[1]));
        $minInput.val(formatNumber(initialValues[0]) + ' ₽');
        $maxInput.val(formatNumber(initialValues[1]) + ' ₽');
    });






    $('.catalog_aside-tab').click(function () {
        $(this).next().slideToggle(400)
        $(this).toggleClass('active')
    })



    $('.catalog_filter-subtitle').click(function () {
        if ($(window).width() < 992) {
            $('.catalog_aside').slideToggle(400)
        }
    })


    var cardDots = new Swiper(".card_dots", {
        spaceBetween: 12,
        slidesPerView: 4,
        watchSlidesProgress: true,
        direction: 'vertical',
        navigation: {
            nextEl: ".card_dots-arrow.next",
            prevEl: ".card_dots-arrow.prev",
        },
        breakpoints: {
            320: {
                slidesPerView: 3,
                direction: 'horizontal',
            },
            767: {
                slidesPerView: 4,
            },
            992: {
                slidesPerView: 3,
            }
        }
    });
    var cardImgs = new Swiper(".card_imgs", {
        thumbs: {
            swiper: cardDots,
        },
    });


    $('.card_faq-title').click(function () {
        $(this).next().slideToggle(400)
        $(this).toggleClass('active')
    })


    $('.counter > span').on('click', function () {
        var input = $(this).closest('.counter').find('input');
        var value = parseInt(input.val()) || 0;
        if ($(this).hasClass('count-arrow-minus')) {
            if (value > 1) {
                value = value - 1;
            }
        }
        if ($(this).hasClass('count-arrow-plus')) {
            value = value + 1;
        };
        input.val(value).change();
    });





    $('.cart_promo').keyup(function () {
        if ($(this).val().length > 0) {
            $(this).addClass('active')
        }
        else {
            $(this).removeClass('active')

        }
    })



})

