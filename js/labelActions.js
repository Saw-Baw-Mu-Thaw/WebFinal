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
}