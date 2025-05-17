function removeAtchImg(e) {
    var noteId = $(e.target).data('id')
    var location = $(e.target).data('loc')
    // console.log('Detach image from noteId ', noteId, '\n Delete Image ', filename)
    // request to make attachedImg null in database
    $.ajax({
        url: 'api/unattach_img.php',
        type: 'POST',
        datatype: 'json',
        data: JSON.stringify({ NoteId: noteId, Location: '.' + location })
    }).done((response) => {
        if (response['code'] == 0) {
            // console.log('image', $('img[data-id=' + noteId + ']'))
            // console.log('button', $('button[data-id=' + noteId + ']'))
            $('img[data-id=' + noteId + ']').attr('src', './images/default_image.png')
            $('button[data-id=' + noteId + ']').remove()
        }
    })
}

export { removeAtchImg }