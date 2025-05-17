import { generateGrid } from "./generateGrid.js";
import { generateList } from "./generateList.js";

function findLabels(e) {
    var label = $(e.target).data('label')
    var notes = JSON.parse(localStorage.getItem('notes'));
    var layout = localStorage.getItem('Layout');
    var mode = localStorage.getItem('Mode');

    if (label == 0) { // Show All Chosen
        if (layout === "GRID") {
            $('#mainContent').empty()
            for (var i = 0; i < notes.length; i++) {
                // console.log(response[i]);
                generateGrid(notes[i]);
            }
        } else {
            $('#mainContent').empty()
            generateList(notes);
        }
    } else { // search by specific label
        var filterlist = []
        for (var i = 0; i < notes.length; i++) {
            var note = notes[i];
            var currLabels = note['Labels']
            for (var j = 0; j < currLabels.length; j++) {
                var obj = currLabels[j];
                if (obj['Label'] == label) {
                    filterlist.push(note);
                    break;
                }
            }
        }

        if (layout === "GRID") {
            $('#mainContent').empty()
            for (var i = 0; i < filterlist.length; i++) {
                generateGrid(filterlist[i]);
            }
        } else {
            $('#mainContent').empty()
            generateList(filterlist);
        }
    }

    if (mode === "DARK") {
        $('.mode-target').removeClass('bg-dark bg-light')
        $('.mode-target').addClass('bg-dark')
    } else {
        $('.mode-target').removeClass('bg-dark bg-light')
        $('.mode-target').addClass('bg-light')
    }
}

export { findLabels }