function addLabel() {
    var Label = $('#txtLabel').val()
    $.ajax({
        url: 'api/add_label.php',
        type: 'POST',
        datatype: 'json',
        contenttype: 'application/json',
        data: JSON.stringify({ label: Label })
    }).done(function (response) {
        if (response['code'] == 0) {
            location.reload();
        }
    }).fail(function () {
        alert('Could not add label');
    })
}

function deleteLabel(e) {
    var labelId = $(e.target).data('id');

    // send delete ajax
    $.ajax({
        url: 'api/delete_label.php?id=' + labelId,
        type: "DELETE",
        datatype: 'json'
    }).done(function (response) {
        if (response['code'] == 0) {
            location.reload();
        }
    })
}

function updateLabel(e) {
    var LabelId = $(e.target).data('id')
    var LabelName = $('#txtLabel').val()

    if (LabelName.length == 0) {
        $('#statusDiv').removeClass("alert alert-danger alert-success alert-warning")
        $('#statusDiv').text("Label name is empty");
        $('#statusDiv').addClass("alert alert-danger")
        return;
    }

    $.ajax({
        url: 'api/update_label.php',
        type: 'POST',
        datatype: "json",
        data: JSON.stringify({ labelId: LabelId, labelName: LabelName })
    }).done((response) => {
        if (response['code'] == 0) {
            location.reload();
        } else {
            $('#statusDiv').removeClass("alert alert-danger alert-success alert-warning")
            $('#statusDiv').text(response['message']);
            $('#statusDiv').addClass("alert alert-danger")
        }
    }).fail(() => {
        $('#statusDiv').removeClass("alert alert-danger alert-success alert-warning")
        $('#statusDiv').text("Can't connect to server");
        $('#statusDiv').addClass("alert alert-danger")
    })
}