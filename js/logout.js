function Logout() {
    $.ajax({
        url: 'api/destroy_session.php',
        type: "GET",
        datatype: 'json'
    }).done(function (response) {
        if (response['code'] == 0) {
            localStorage.clear(); // delete stored mode, layout and notes
            window.location.replace('logout.php');
        }
    });
}