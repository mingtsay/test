setInterval(function() {
    var a = $('.fa.fa-hourglass-start'),
        b = $('.fa.fa-hourglass-half'),
        c = $('.fa.fa-hourglass-end.rotate-0'),
        d = $('.fa.fa-hourglass-end.rotate-1');
    a.removeClass('fa-hourglass-start').addClass('fa-hourglass-half');
    b.removeClass('fa-hourglass-half').addClass('fa-hourglass-end').addClass('rotate-0');
    c.removeClass('rotate-0').addClass('rotate-1');
    d.removeClass('rotate-1').addClass('rotate-2').removeClass('fa-hourglass-end').addClass('fa-hourglass-start').removeClass('rotate-2');
}, 1000);