$(document).ready(function() {

    var title = document.title.split('-')[0].trim();
    $('.nav a:contains("' + title + '")').addClass('active');

    $("#loginForm").on('submit', function(e) {
        var valid = true;
        var email = $('login-email').val();
        var password = $('login-password').val();

        if (email.length === 0 || email.length > 150)
            valid = false;

        if (password === 0 || password.length > 20)
            valid = false;

        if (!valid) {
            e.preventDefault();
            alert('Invalid input fields.');
        }
    });

});

function validateEmail(email) {
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}