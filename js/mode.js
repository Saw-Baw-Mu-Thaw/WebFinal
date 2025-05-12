function changeMode(e) {
    var mode = $(e.target).val()

    $.ajax({
        url: 'api/update_mode.php?mode=' + mode,
        type: 'GET',
        datatype: 'json'
    }).done(function (response) {
        console.log(response)
        if (response['code'] == 0) {
            location.reload();
        }
    })
}

export { changeMode }