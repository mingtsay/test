$(function() {
    api({ action: 'is_login' }, function(data) {
        window.is_login = data.is_login;
        if (!data.is_login) {
            if (window.guest) $('#user_information, .sidebar-menu li').hide();
            else location = 'login.html';
        }
    });
});