var guest = true;
$(function() {
    var login_failure_timeout = false;
    $('#form_login').submit(function(event) {
        event.preventDefault();
        api({
            action: 'login',
            sid: this.sid.value,
            pw: this.pw.value,
            recaptcha: this['g-recaptcha-response'].value
        }, function(data) {
            if (data.login) {
                location = 'notice.html';
            } else {
                grecaptcha.reset();
                $('#login_failure').show();
                if (login_failure_timeout !== false) clearTimeout(login_failure_timeout);
                login_failure_timeout = setTimeout(function() {
                    $('#login_failure').slideUp('slow');
                    login_failure_timeout = false;
                }, 3000); // hide message after 3s
            }
        });
    });

    api({ action: 'is_login' }, function(data) {
        if (data.is_login) {
            location = 'notice.html';
        }
    });

    window.page = 'login';
});