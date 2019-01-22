$(document).ready(function() {
    //Custom select options in .custom-form class
    $('.select-holder').each(function() {
        /*var li = '';
        $(this).find("select > option").each(function() {
            li += '<li>' + this.text + '</li>';
        });
        $(this).find('select').after("<ul class='fake-ul list-none'>" + li + "</ul>");
        $(this).find('.fake-ul li').click(function() {
            var current_txt = $(this).text();
            console.log(current_txt);
            $(this).closest('.select-holder ul').find('li:first-child').html(current_txt);
        });*/

    });
    //For toggling custom fake ul select
    $('.custom-row ul').click(function() {
        $(this).toggleClass('toggle');
    });

    //For toggling sidebar
    $('.btn-sidebar').click(function() {
        $('#sidebar, #content').toggleClass('active');
    });

    //For lightboxes
    $('.btn-popup').click(function(e) {
        e.preventDefault();
        var currentPopup = $(this).attr('href');
        $('.lightbox-container').removeClass('active');
        $(currentPopup).addClass('active');
    });
    $('.btn-confirm').click(function(e) {
        e.preventDefault();
        var currentDialogue = $(this).attr('href');
        $('.dialogue').removeClass('active');
        $(currentDialogue).addClass('active');
    });

    //For Closing lightboxes
    $('.cancel, .btn-close').click(function(e) {
        e.preventDefault();
        $('.dialogue, .lightbox').removeClass('active');
    });

    //For same-height blocks
    var class_name = ".same-height";
    var max_height = 0;
    $(class_name).each(function(index, data) {
        if ($(data).height() > max_height) {
            max_height = $(data).height();
            console.log(max_height);
        }
    });
    $(class_name).height(max_height);


    //For logout button toggling
    $('.logout-opener').click(function() {
        $('.logout-area').slideToggle(100);
    });
    //For Approve button
    $('a.approve').click(function(e) {
        e.preventDefault();
        $(this).toggleClass('active');
    });

    //Circular progress bars
    function animateElements() {
        $('.progressbar').each(function() {
            var elementPos = $(this).offset().top;
            var topOfWindow = $(window).scrollTop();
            var percent = $(this).find('.circle').attr('data-percent');
            var percentage = parseInt(percent, 10) / parseInt(100, 10);
            var animate = $(this).data('animate');
            if (elementPos < topOfWindow + $(window).height() - 30 && !animate) {
                $(this).data('animate', true);
                $(this).find('.circle').circleProgress({
                    startAngle: -Math.PI / 2,
                    value: percent / 100,
                    thickness: 10,
                    fill: {
                        color: '#56606e'
                    }
                }).on('circle-animation-progress', function(event, progress, stepValue) {
                    $(this).find('.percent-val').text((stepValue * 100).toFixed(1) + "%");
                }).stop();
            }
        });
    }
    // Show animated elements
    animateElements();
    $(window).scroll(animateElements);

});

$(document).on('click', function(e) {
    if ($(e.target).is('.select-holder ul li') === false) {
        $('ul').removeClass('toggle');
    }
});