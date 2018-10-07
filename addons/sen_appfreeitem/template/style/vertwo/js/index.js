$(function(){

    //头部轮播
    var slideBanner = new Swiper('.slideBanner',{
        autoplayDisableOnInteraction:false,
        autoplay:3000,
        visibilityFullFit:true,
        loop:true,
        pagination:'.pagination',
    });
    //slider效果
    var slider = new Swiper('.tab-bd',{
        speed:500,
        onSlideChangeStart: function(){
            $('.tab-hd .active').removeClass('active');
            $('.tab-hd li').eq(slider.activeIndex).addClass('active'); 
        },
        //高度自适应
        onInit:function(swiper){
            swiper.container[0].style.height=swiper.slides[swiper.activeIndex].offsetHeight+'px';
        },
        onSlideChangeEnd:function(swiper){
            swiper.container[0].style.height=swiper.slides[swiper.activeIndex].offsetHeight+'px';
        }
    });
    //点击导航切换
    $('.tab-hd li').on('touchstart mousedown',function(e){
        e.preventDefault();
        $('.tab-hd .active').removeClass('active');
        $(this).addClass("active");
        slider.slideTo( $(this).index() );
    });
    $(".tab-hd li").click(function(e){
        e.preventDefault();
    });

    // tab切换固定
    var tabHei = $('.tab-hd').height();
    $(window).scroll(function() {
        if( $(window).scrollTop() > tabHei ) {
            $('.tab-hd').css({
                position: 'fixed',
                left: 0,
                top: 0
            });
        } else {
            $('.tab-hd').css({
                position: 'relative'
            });
        }
    });

    

    // 返回顶部
    var fullHei = $(window).height();
    $(window).scroll(function() {
        if( $(window).scrollTop() > fullHei ) {
            $('#goTop').fadeIn(300);
        } else {
            $('#goTop').fadeOut(300);
        }
    });
    $('#goTop').click(function() {
        $('body,html').animate({scrollTop: 0}, 1000);
    });
});