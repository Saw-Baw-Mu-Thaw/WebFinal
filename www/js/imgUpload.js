function imgDropHandler(e) {
    e.preventDefault()
    var noteId = $(e.target).data('id')
    if (e.originalEvent.dataTransfer) {
        var item = [...e.originalEvent.dataTransfer.items][0]
        if (item.kind === 'file') {
            const file = item.getAsFile();
            changeImg(file.name, file, noteId);
        }
    }
    $(e.target).removeClass('border border-danger')
}

function imgDragOverHandler(e) {
    var noteId = $(e.target).data('id')
    e.preventDefault()
}

function imgDragEnter(e) {
    var elem = $(e.target)
    $(elem).addClass('border border-danger')
}

function imgDragLeave(e) {
    var elem = $(e.target)
    $(elem).removeClass('border border-danger')
}

function changeImg(filename, file, noteId) {
    var fd = new FormData();
    fd.append("file", file)
    fd.append("filename", filename)
    fd.append("noteId", noteId)

    $.ajax({
        url: 'api/attach_img.php',
        type: "POST",
        datatype: 'json',
        data: fd,
        processData: false,
        contentType: false
    }).done((response) => {
        if (response['code'] == 0) {
            location.reload();
        } else {
            showError(response['message'])
        }
    }).fail((response) => {
        showError(response['message'])
    })
}

function showError(msg) {
    $("#errorDiv").show();
    $("#errorDiv").text(msg);

    window.setTimeout(function () { $("#errorDiv").hide() }, 2500)
}