import { formatDatetime } from "./utils.js";
import { removeAtchImg } from "./removeAttachedImg.js";

function generateGrid(obj) {

    // create a row of cards
    var card = $("<div class='card col-lg-4 col-12 m-1 mode-target'></div>");
    var cardBody = $("<div class='card-body'></div>");
    var cardTitle = $("<h5 class='card-title'></h5>").text(obj['Title']);
    var cardText = $("<p class='card-text'></p>").html("Last Modified : " + formatDatetime(obj['LastModified']));
    var openBtn = $(`<button class='btn btn-primary mx-1' onclick='openNote(${obj['NoteID']}, ${obj['Locked']})'><i class="far fa-edit"></i></button>`);
    var delBtn = $(`<button class='btn btn-danger mx-1'  onclick='deleteNote(${obj['NoteID']}, "${obj['Title']}")'><i class="fas fa-trash"></i></button>`);
    var lockBtn;

    if (obj['Locked'] == false) {
        // setting lock button and icon
        lockBtn = $(`<button class='btn btn-info mx-1' onclick='lockNote(${obj['NoteID']})'>Lock Note</button>`)
        $(cardTitle).append('<span class="mx-1"><i class="fas fa-lock-open"></i></span>')
    } else {
        // setting remove lock button and icon
        lockBtn = $(`<button class='btn btn-info mx-1' onclick='removeLock(${obj['NoteID']})'>Remove Lock</button>`)
        $(cardTitle).append('<span class="mx-1"><i class="fas fa-lock"></i></span>')
    }

    // attached Image button
    if (obj['AttachedImg'] != null) {
        var attachedImg = $(`<img class='card-img-top' src='${obj['AttachedImg']}' />`)

        if (obj['SharedNote'] == false) {
            $(attachedImg).on('drop', imgDropHandler)
            $(attachedImg).on('dragover', imgDragOverHandler)
            $(attachedImg).on('dragenter', imgDragEnter)
            $(attachedImg).on('dragleave', imgDragLeave)
            $(attachedImg).attr('data-id', obj['NoteID'])
        }
        $(card).append(attachedImg);

    } else {
        // you can replace the default_image, probably something smaller
        var attachedImg = $("<img class='card-img-top' src='images/default_image.png' />")
        if (obj['SharedNote'] == false) {
            $(attachedImg).on('drop', imgDropHandler)
            $(attachedImg).on('dragover', imgDragOverHandler)
            $(attachedImg).on('dragenter', imgDragEnter)
            $(attachedImg).on('dragleave', imgDragLeave)
            $(attachedImg).attr('data-id', obj['NoteID'])
        }
        $(card).append(attachedImg);
    }

    // pin button
    var pinBtn;
    if (obj['Pinned'] == true) {
        pinBtn = $(`<button class='btn mx-1' onclick='removePin(${obj['NoteID']})'>
                <span style='color:red;'><i class="fas fa-thumbtack"></i></span>
                </button>`)
    } else {
        pinBtn = $(`<button class='btn mx-1' onclick='addPin(${obj['NoteID']})'>
            <span style='color:blue;'><i class="fas fa-thumbtack"></i></span>
            </button>`)
    }

    // code for labels
    $(cardText).append("<hr>")
    var labelString = ""
    var labelList = obj['Labels']
    // console.log(labelList)
    for (var j = 0; j < labelList.length; j++) {
        labelString += "#" + labelList[j]['Label'] + " "
    }
    var labelhead = $('<h4>Labels</h4>')
    var labelp = $(`<p class='m-1'>${labelString}</p>`)
    $(cardText).append(labelhead)
    $(cardText).append(labelp)
    $(cardText).append("<hr>")


    // appending elements
    $(cardTitle).prepend(pinBtn);
    if (obj['SharedNote'] == true) { // shared notes can't be deleted by non-owner
        $(cardBody).append(cardTitle, cardText, openBtn);
    } else {
        $(cardBody).append(cardTitle, cardText, openBtn, delBtn, lockBtn);

        // add button to revert attached image back to default
        if (obj['AttachedImg'] != null) {
            var removeAttachedImgBtn = $("<button class='btn btn-danger mx-1'><i class='fas fa-image'></i></button>")
            $(removeAttachedImgBtn).attr('data-id', obj['NoteID'])
            $(removeAttachedImgBtn).attr('data-loc', obj['AttachedImg'])
            $(removeAttachedImgBtn).on('click', removeAtchImg)
            $(cardBody).append(removeAttachedImgBtn)
        }
    }
    $(card).append(cardBody);
    $("#mainContent").append(card);

    formatDatetime(obj['LastModified']);
}

export { generateGrid }