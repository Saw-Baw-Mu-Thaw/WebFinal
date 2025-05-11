import { generateGrid } from "./generateGrid.js";
import { generateList } from "./generateList.js";


function getNotesGrid() {
    $.ajax({
        url: "api/get_notes.php",
        type: "GET",
        datatype: "json"
    }).done(function (response) {
        // console.log(response);

        for (var i = 0; i < response.length; i++) {
            // console.log(response[i]);
            generateGrid(response[i]);
        }
    });
}

function getNotesList() {
    $.ajax({
        url: "api/get_notes.php",
        type: "GET",
        datatype: "json"
    }).done(function (response) {
        console.log(response);
        generateList(response);
    });
}

function setPreferences(elemList) {
    $.ajax({
        url: 'api/get_preferences.php',
        type: "GET",
        datatype: "json"
    }).done(function (response) {
        // console.log(response)

        if (response['code'] == 0) {
            // console.log("setting preferences");

            // choose list or grid here
            if (response['Layout'] == "GRID") {
                getNotesGrid()
            } else {
                getNotesList()
            }

            // set font size here
            for (var i = 0; i < elemList.length; i++) {
                // console.log("setting font size")
                $(elemList[i]).css('font-size', response['FontSize']);
            }

            // set light or dark mode here
            // for (var i = 0; i < elemList.length; i++) {
            //     // console.log(elemList[i]) 

            //     // replace bg-light and bg-dark with custom class
            //     $(elemList[i]).removeClass("bg-light bg-dark");
            //     if (response['Mode'] == "LIGHT") {
            //         $(elemList[i]).addClass("bg-light");
            //     } else {
            //         $(elemList[i]).addClass("bg-dark");
            //     }
            //     $('div.modal-open').removeClass('bg-light bg-dark')
            // }
        }
    })
}

function setUsernameHeading() {

    // set username heading
    $.ajax({
        url: "api/get_user_info.php",
        type: "GET",
        datatype: "json"
    }).done(function (response) {
        $('#userHeading').text(response['username'] + "\'s notes");
    })
}





$(document).ready(function () {
    $("#errorDiv").hide();
    // use display block to show it
    setUsernameHeading();
    // getNotes();
    setPreferences(['body', 'div', 'h4', 'h5']);
});