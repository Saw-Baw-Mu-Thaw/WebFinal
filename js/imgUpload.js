function imgDropHandler(e) {
    e.preventDefault()
    var noteId = $(e.target).data('id')
    // console.log($(e.target))
    // console.log('Attach Image to Note', noteId)
    // console.log(e)
    if (e.originalEvent.dataTransfer) {
        console.log('original Event')
        console.log(e.originalEvent.dataTransfer.items)
        var item = [...e.originalEvent.dataTransfer.items][0]
        if (item.kind === 'file') {
            const file = item.getAsFile();
            console.log(`file name : ${file.name}}`)
            changeImg(file.name, file, noteId);
        }
    }
    $(e.target).removeClass('border border-danger')
}

function imgDragOverHandler(e) {
    var noteId = $(e.target).data('id')
    console.log($(e.target))
    console.log('File is above Note\'s ', noteId, 'drop zone')
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
    console.log(filename, file, noteId);
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
            console.log('success')
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