var api = function (data, callback) {
    return $.ajax({
        url: '/api.php',
        method: 'POST',
        data: data,
        dataType: 'json',
        async: (typeof callback === 'function'),
        success: callback || null,
    }).responseJSON;
};

var logout = function() {
    api({ action: 'logout' }, function() {
        location = 'login.html';
    });
};

$(function() {
    var $user_info = $('#user_information span'),
        check_timeout = function() {
            api({ action: 'get_timeout' }, function(data) {
                if (data.timeout > 0) setTimeout(check_timeout, data.timeout * 1000);
                else location = 'login.html';
            });
        };
    api({ action: 'is_login' }, function(data) {
        window.is_login = data.is_login;
        if (window.is_login) {
            $('#not_login, .nav-login').hide();
            api({ action: 'user_get_information' }, function(data) {
                $user_info.text(data.user_information);
            });
        } else {
            if (window.guest) {
                $('#user_information, .sidebar-menu li').hide();
                $('.sidebar-menu li.header, .sidebar-menu li.nav-login, .sidebar-menu li.nav-status').show();
            } else location = 'login.html';
        }
        if ($('.sidebar-menu li.nav-' + window.page).length) $('.sidebar-menu li.nav-' + window.page).addClass('active');
        if (window.is_login) check_timeout();
        if (window.page) {
            $('.nav-language a').attr('href', '/' + (lang === 'zh-tw' ? 'en-us' : 'zh-tw') + '/' + window.page + '.html');
            $('a.logo, a.site-title').attr('href', '/' + lang + '/' + (window.is_login ? 'notice' : 'login') + '.html');
        }
    });
});