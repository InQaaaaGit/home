import $ from 'jquery';
import 'block_slider/libs/slick';

/**
 * @module block_slider/functions/slider
 */

const initSlider = () => {
    $(document).ready(function () {
        $(".technical-library").slick({
            slidesToShow: 4,
            slidesToScroll: 4,
            dots: true,
            prevArrow:
                '<button type="button" class="slick-arrow slick-arrow__prev"><i class="fas fa-chevron-left"></i></button>',
            nextArrow:
                '<button type="button" class="slick-arrow slick-arrow__next"><i class="fas fas fa-chevron-right"></i></button>',
            responsive: [
                {
                    breakpoint: 1240,
                    settings: {
                        slidesToShow: 4,
                        slidesToScroll: 4,
                    }
                },
                {
                    breakpoint: 768,
                    settings: {
                        slidesToShow: 3,
                        slidesToScroll: 3,
                    }
                },
                {
                    breakpoint: 525,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1,
                    }
                },
            ]
        });
    });
};

export default {initSlider};