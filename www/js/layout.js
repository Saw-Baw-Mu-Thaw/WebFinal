import { generateGrid } from "./generateGrid.js";
import { generateList } from "./generateList.js";
import { showError } from "./utils.js";

function changeLayout(e) {
    var layout = $(e.target).val();
    localStorage.setItem('Layout', layout);
    // console.log(layout);
    $.ajax({
        url: 'api/update_layout.php?layout=' + layout,
        type: 'GET',
        datatype: 'json'
    }).done((response) => {
        if (response['code'] == 0) { // update successful
            location.reload()
        }

    }).fail(() => {
        showError("Couldn't connect to server")
        var notes = JSON.parse(localStorage.getItem('notes'))


        // console.log(notes)
        $('#mainContent').empty()
        if (layout === "GRID") {
            for (var i = 0; i < notes.length; i++) {
                generateGrid(notes[i])
            }
        } else {
            generateList(notes)
        }

        var mode = localStorage.getItem('Mode');
        if (mode === "DARK") {
            $('mode-target').removeClass('bg-dark bg-light')
            $('mode-target').addClass('bg-dark')
        } else {
            $('mode-target').removeClass('bg-dark bg-light')
            $('mode-target').addClass('bg-light')
        }

        // set light mode and dark mode
        // store mode in local storage

    })
}

export { changeLayout }