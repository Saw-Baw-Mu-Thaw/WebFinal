import { changeMode } from "./mode.js";
import collaboration from "./collaboration.js";

var noteChanged = false;
var timeout = null;
var failInterval = null;
var intervalStarted = false;
var noteId;
var localTitle = "";
var localContents = "";
var oldTitle = "";
var localMode = "";
var currentUserId = "";
var currentUsername = "";
var isCollaborationEnabled = false;

$(document).ready(function () {
    // console.log('running')
    setPreferences();

    $('#title').on('input', saveContent);
    $('#textareaElem').on('input', saveContent);
    $('#homeBtn').on('click', goHome);
    $('#AddLabelBtn').on('click', addLabel);
    $('input:radio[name=mode]').on('click', changeMode)

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
            // console.log(response['role'])
            if (response['action'] === "Create") {
                document.title = "Create Page"
            } else {
                document.title = "Edit Page"
            }

            $('#title').val(response['title'])
            $('#textareaElem').val(response['contents'])
            oldTitle = $('#title').val();

            if (response['role'] == 'VIEWER') {
                $('#title').prop('disabled', true);
                $('#textareaElem').prop('disabled', true);
            }

            noteId = response['NoteID'];

            // Get current user information
            $.ajax({
                url: "api/get_user_info.php",
                type: "GET",
                dataType: "json"
            }).done(function (userInfo) {
                if (userInfo.code === 0) {
                    currentUserId = userInfo.userId;
                    currentUsername = userInfo.username;

                    // If this is a shared note with edit permissions, initialize collaboration
                    if (response['role'] === 'EDITOR' && noteId) {
                        initializeCollaboration();
                    }
                }
            });

            // console.log(response['labels'])
            // showing the labels
            var labels = response['labels']
            for (var i = 0; i < labels.length; i++) {
                var label = labels[i];
                var labelElem = $(`<div class='d-inline border border-info rounded-pill p-2 m-1'><span>#${label['Label']} </span></div>`)
                var labelUpdateBtn = $(`<button class='btn btn-primary btn-sm rounded-circle' data-id=${label['LabelID']}></button>`)
                $(labelUpdateBtn).html('<i class="far fa-edit"></i>')
                $(labelUpdateBtn).on('click', updateLabel);

                var labelDeleteBtn = $(`<button class='btn btn-danger btn-sm rounded-circle' data-id=${label['LabelID']}>&times;</button>`)
                $(labelDeleteBtn).on('click', deleteLabel);
                $(labelElem).append(labelUpdateBtn, labelDeleteBtn)
                $('#labelDiv').append(labelElem)
            }
            showSaved()

            //setPreferences();
        }
    }).fail(function () {
        alert("Could not connect to server")
    }).always(() => {
        //console.log('executing always')
    })
}

/**
 * Initialize real-time collaboration
 */
function initializeCollaboration() {
    if (!noteId || !currentUserId || !currentUsername) {
        console.error('Missing required data for collaboration');
        return;
    }

    // Load collaboration CSS
    $('head').append('<link rel="stylesheet" href="css/collaboration.css">');

    // Initialize collaboration module
    collaboration.init(noteId, currentUserId, currentUsername);
    isCollaborationEnabled = true;
}

function saveContent() {
    // console.log("Saving")
    // console.log("timeout : ", timeout)
    if (timeout != null) {
        // console.log('cleared timeout')
        window.clearTimeout(timeout);
    }

    noteChanged = true;
    // console.log('noteChanged :', noteChanged);
    var newTitle = $('#title').val();
    var contents = $('#textareaElem').val();
    localTitle = $('#title').val();
    localContents = $('#textareaElem').val();

    timeout = window.setTimeout(function () { sendContent(oldTitle, newTitle, contents) }, 2000);
}

function sendContent(OldTitle, NewTitle, Contents) {
    showSaving();

    // console.log("Sending")

    $.ajax({
        url: "api/update_note.php",
        type: "POST",
        datatype: "json",
        contenttype: "application/json",
        data: JSON.stringify({ oldTitle: OldTitle, newTitle: NewTitle, contents: Contents })
    }).done(function (response) {
        if (response['code'] == 0) {
            showSaved();
            noteChanged = false;
            oldTitle = $('#title').val()
            // console.log(noteChanged);

            // stops the fail retry
            intervalStarted = false
            window.clearInterval(failInterval)
            failInterval = null;
        } else {
            showNotSaved();
        }
    }).fail(function () {
        showNotSaved();

        console.log('starting fail interval')
        // try saving every one minute
        if (intervalStarted == false) {
            failInterval = window.setInterval(function () {
                // console.log('trying again')
                sendContent(oldTitle, localTitle, localContents)
            }, 60000)
            intervalStarted = true
        }

    })
}

function showSaved() {
    $('#statusDiv').removeClass("alert alert-danger alert-success alert-warning")
    $('#statusDiv').text("Saved");
    $('#statusDiv').addClass("alert alert-success")
}

function showNotSaved() {
    $('#statusDiv').removeClass("alert alert-danger alert-success alert-warning")
    $('#statusDiv').text("Not Saved");
    $('#statusDiv').addClass("alert alert-danger")
}

function showSaving() {
    $('#statusDiv').removeClass("alert alert-danger alert-success alert-warning")
    $('#statusDiv').text("Saving...");
    $('#statusDiv').addClass("alert alert-warning")
}

function goHome() {
    // Disconnect from collaboration if enabled
    if (isCollaborationEnabled) {
        collaboration.disconnect();
    }

    location.replace("index.php");
}

// set light mode or dark mode
function setPreferences() {
    $.ajax({
        url: 'api/get_preferences.php',
        type: "GET",
        datatype: "json"
    }).done(function (response) {
        console.log(response)
        if (response['code'] == 0) {
            if (response['Mode'] == 'DARK') {
                $('.mode-target').addClass('bg-dark')
            } else {
                $('.mode-target').addClass('bg-light')
            }
        }
    }).fail(() => {

    })
}