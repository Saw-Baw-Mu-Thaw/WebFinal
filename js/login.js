$(document).ready(function () {
    hideError();

    $('#username').on('click', function () {
        hideError();
    });
    $('#password').on('click', function () {
        hideError();
    });

    $('#loginForm').on("submit", function (e) {
        e.preventDefault();

        // console.log("I'm gonna submit the form")

        // send ajax in here

        Username = $('#username').val()
        Password = $('#password').val()

        $.ajax({
            url: "api/login_service.php",
            type: "POST",
            dataType: "json",
            contentType: 'application/json',
            data: JSON.stringify({ username: Username, password: Password })

        }).done(function (json) {
            if (json['code'] == 0) {
                window.location.replace("index.php");
            } else {
                $('#username').val(Username);
                $('#password').val(Password);
                showError(json['message']);
            }
        });
    })
})

function showError(message) {
    $('#errorDiv').css('visibility', 'visible');
    $('#errorDiv').text(message);
}

function hideError(message) {
    $('#errorDiv').css('visibility', 'hidden');
}