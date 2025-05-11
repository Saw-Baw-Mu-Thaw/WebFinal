function Logout() {
    $.ajax({
        url: 'api/destroy_session.php',
        type: "GET",
        datatype: 'json'
    }).done(function (response) {
        if (response['code'] == 0) {
            window.location.replace('logout.php');
        }
    });
}