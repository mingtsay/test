$(function() {
    api({ action: 'notice_get', lang: lang }, function(data) {
        if (data.notice) {
            $('#notice').html(data.notice);
        }
    });

    window.page = 'notice';
});