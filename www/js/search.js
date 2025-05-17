var timeout = null;

function searchNote(e) {

    if (timeout != null) {
        window.clearTimeout(timeout);
    }

    var searchTxt = $(e.target).val();
    if (searchTxt.length < 2) {
        $('#searchList').empty()
        timeout = null
        return;
    }
    timeout = window.setTimeout(sendSearch, 500, searchTxt)
}

function sendSearch(searchTxt) {

    // clear list group
    $('#searchList').empty()
    // send ajax
    $.ajax({
        url: 'api/search_notes.php',
        type: "POST",
        data: "json",
        data: JSON.stringify({ text: searchTxt })
    }).done((response) => {
        if (response['code'] == 0) {
            var searches = response['searches']
            for (var i = 0; i < searches.length; i++) {
                var search = searches[i]
                var listButton = $("<button onclick='openNote(" + search['noteId'] + "," + search['locked'] + ")'></button>")
                $(listButton).addClass('list-group-item list-group-item-action w-100')

                var TitleHead = $("<h5></h5>")
                $(TitleHead).html(search['headingText'])

                var contentText = $("<p></p>")
                $(contentText).html(search['pText'])

                $(listButton).append(TitleHead, contentText)
                $('#searchList').append(listButton)
            }
        }
    })
    // populate list group
}

export { searchNote }