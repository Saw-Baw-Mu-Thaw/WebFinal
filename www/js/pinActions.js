function addPin(noteId) {
    $.ajax({
        url: 'api/add_pin.php?id=' + noteId,
        type: "GET",
        datatype: "json"
    }).done(function (response) {
        if (response['code'] == 0) {
            location.reload();
        } else {
            showError(response['message']);
        }
    })
}

function removePin(noteId) {
    $.ajax({
        url: "api/remove_pin.php?id=" + noteId,
        type: "GET",
        datatype: "json"
    }).done(function (response) {
        if (response['code'] == 0) {
            location.reload();
        } else {
            showError(response['message']);
        }
    })
}

function showError(msg) {
    $("#errorDiv").show();
    $("#errorDiv").text(msg);

    window.setTimeout(function () { $("#errorDiv").hide() }, 2500)
}