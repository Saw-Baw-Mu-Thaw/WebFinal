$(document).ready(function () {
    console.log('running')

    getNoteContents();
})

function getNoteContents() {
    $.ajax({
        url: "api/get_note_contents.php",
        type: "GET",
        datatype: "json"
    }).done(function (response) {
        if (response['code'] == 0) {
            // console.log(response['title'])
            // console.log(response['contents'])
            // console.log(response['action']);
            if (response['action'] == "CREATE") {
                document.title = "Create Page"
            } else {
                document.title = "Edit Page"
            }

            $('#title').val(response['title'])
            $('#textareaElem').val(response['contents'])
        }
    }).fail(function () {
        alert("Could not connect to server")
    })
}