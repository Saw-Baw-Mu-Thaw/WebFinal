import { formatDatetime } from "./utils.js";

function generateGrid(obj) {

    // create a row of cards
    var card = $("<div class='card col-lg-4 col-12 m-1'></div>");
    var cardBody = $("<div class='card-body'></div>");
    var cardTitle = $("<h5 class='card-title'></h5>").text(obj['Title']);
    var cardText = $("<p class='card-text'></p>").html("Last Modified : " + formatDatetime(obj['LastModified']));
    var openBtn = $(`<button class='btn btn-primary mx-1' onclick='openNote(${obj['NoteID']}, ${obj['Locked']})'>Open</button>`);
    var delBtn = $(`<button class='btn btn-danger mx-1'  onclick='deleteNote(${obj['NoteID']}, "${obj['Title']}")'>Delete</button>`);
    var lockBtn;
    if (obj['Locked'] == false) {
        // console.log('set lock btn')
        lockBtn = $(`<button class='btn btn-info mx-1' onclick='lockNote(${obj['NoteID']})'>Lock Note</button>`)
    } else {
        // console.log('set unlock btn')
        lockBtn = $(`<button class='btn btn-info mx-1' onclick='removeLock(${obj['NoteID']})'>Remove Lock</button>`)
    }

    if (obj['AttachedImg'] != null) {
        var attachedImg = $(`<img class='card-img-top' src='${obj['AttachedImg']}' />`)
        $(card).append(attachedImg);
    } else {
        // you can replace the default_image, probably something smaller
        var attachedImg = $("<img class='card-img-top' src='images/default_image.png' />")
        $(card).append(attachedImg);
    }

    var pinBtn;
    if (obj['Pinned'] == true) {
        pinBtn = $(`<button class='btn btn-primary mx-1' onclick='removePin(${obj['NoteID']})'>`
            `<span style='color:red;'><i class="fas fa-thumbtack"></i></span>` +
            `</button>`)
    } else {
        pinBtn = $(`<button class='btn btn-primary mx-1' onclick='addPin(${obj['NoteID']})'>
            <span style='color:blue;'><i class="fas fa-thumbtack"></i></span>
            </button>`)
    }

    $(cardTitle).prepend(pinBtn);
    $(cardBody).append(cardTitle, cardText, openBtn, delBtn, lockBtn);
    $(card).append(cardBody);
    $("#mainContent").append(card);

    formatDatetime(obj['LastModified']);
}

export { generateGrid }