$(function() {
    var timeInformation = null, getTimeInformation = function() {
        $.ajax({
            url: '/admin/time.php',
            method: 'GET',
            dataType: 'json',
            async: true,
            success: function(data) {
                timeInformation = data;

                var min = Math.floor(data.remain / 60),
                    sec = data.remain % 60,
                    t = data.remain < 0 ? '00:00' : (min < 10 ? '0' : '') + min + ':' + (sec < 10 ? '0' : '') + sec;

                $('#time-remaining code').text(t);

                if (data.remain < 0) location = 'lockscreen.php';
                else setTimeout(getTimeInformation, 1000);
            }
        });
    };
    getTimeInformation();
    $('#time-remaining').click(function() {
        $.ajax({
            url: '/admin/time.php?reset',
            method: 'GET',
            dataType: 'json',
            async: true
        });
    });
});