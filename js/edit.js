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
    $('#textareaElem').on('input', function() {
        saveContent();
    });
    $('#homeBtn').on('click', goHome);
    $('#AddLabelBtn').on('click', addLabel);
    $('input:radio[name=mode]').on('click', changeMode);
    
    getNoteContents();
    
    // Add tooltip initialization
    $('[data-toggle="tooltip"]').tooltip();
})

function getNoteContents() {
    // Check for offline mode - get note from IndexedDB if needed
    const offlineNoteId = localStorage.getItem('currentNoteId');
    
    if (offlineNoteId && window.OfflineNotesManager && !window.OfflineNotesManager.isOnline()) {
        // We're offline and have a note ID stored - try to get it from IndexedDB
        window.OfflineNotesManager.getNotesFromIndexedDB()
            .then(notes => {
                const offlineNote = notes.find(note => note.id.toString() === offlineNoteId.toString());
                
                if (offlineNote) {
                    console.log('Loaded offline note:', offlineNote);
                    document.title = "Edit Page (Offline)";
                    $('#title').val(offlineNote.Title);
                    $('#textareaElem').val(offlineNote.Content);
                    oldTitle = $('#title').val();
                    noteId = offlineNote.id;
                    
                    // Set up event handlers
                    $('#title, #textareaElem').off('input').on('input', saveOfflineContent);
                    
                    // Show offline status
                    showOfflineStatus();
                    showSaved();
                } else {
                    console.error('Failed to find offline note with ID:', offlineNoteId);
                    $('#statusDiv').text("Error: Could not load note").addClass("alert alert-danger");
                }
            })
            .catch(error => {
                console.error('Error loading offline note:', error);
                $('#statusDiv').text("Error: " + error.message).addClass("alert alert-danger");
            });
        return;
    }

    // Normal online mode
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
            document.title = "Edit Page";
            $('#title').val(response['title']);
            $('#textareaElem').val(response['contents']);
            oldTitle = $('#title').val();
            
            // Regular save content event handler for editing
            $('#title, #textareaElem').off('input').on('input', saveContent);

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

    // Check if we're offline
    if (window.OfflineNotesManager && !window.OfflineNotesManager.isOnline()) {
        console.log('Offline detected, saving to IndexedDB instead');
        
        // Save to IndexedDB
        saveToIndexedDB(NewTitle, Contents);
        return;
    }

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
            
            // If we're back online and we have an offline notes manager, save this to IndexedDB as well
            // This ensures IndexedDB is updated with the latest server data
            if (window.OfflineNotesManager) {
                const note = {
                    id: noteId,
                    Title: NewTitle,
                    Content: Contents,
                    syncStatus: 'synced'
                };
                window.OfflineNotesManager.saveNoteToIndexedDB(note, 'synced');
            }
        } else {
            showNotSaved();
        }
    }).fail(function () {
        // We might have gone offline
        if (window.OfflineNotesManager) {
            console.log('API call failed, attempting to save offline');
            
            // Save to IndexedDB instead
            saveToIndexedDB(NewTitle, Contents);
            
            // Show offline status if needed
            showOfflineStatus();
        } else {
            showNotSaved();

            console.log('starting fail interval')
            // try saving every one minute
            if (intervalStarted == false) {
                failInterval = window.setInterval(function () {
                    // console.log('trying again')
                    sendContent(oldTitle, localTitle, localContents)
                }, 10000)
                intervalStarted = true
            }
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

// set light mode or dark mode and other preferences
function setPreferences() {
    $.ajax({
        url: 'api/get_preferences.php',
        type: "GET",
        datatype: "json"
    }).done(function (response) {
        console.log(response)
        if (response['code'] == 0) {
            // Apply theme
            const isDarkMode = response['Mode'] == 'DARK';
            if (isDarkMode) {
                $('.mode-target').addClass('bg-dark')
            } else {
                $('.mode-target').addClass('bg-light')
            }
            
            // Apply font size and note color
            const fontSize = response['FontSize'] ? response['FontSize'] : 16;
            const fontSizePx = fontSize + 'px';
            const noteColor = response['NoteColor'] || '#ffffff';
            
            // Save to localStorage as raw values
            localStorage.setItem('fontSize', fontSize.toString());
            localStorage.setItem('noteColor', noteColor);
            
            // Apply CSS variables
            document.documentElement.style.setProperty('--note-font-size', fontSizePx);
            document.documentElement.style.setProperty('--note-color', noteColor);
            
            // Apply directly to textarea and ensure it's visible
            $('#textareaElem').css('font-size', fontSizePx);
            
            // Only apply background color in light mode
            if (!isDarkMode) {
                $('#textareaElem').css('background-color', noteColor);
            }
            
            // Apply to all text elements for better visibility
            $('p, h4, h5, .card-text, .card-body').css('font-size', fontSizePx);
        }
    }).fail(() => {
        // Fallback to localStorage values if API fails
        const fontSize = localStorage.getItem('fontSize');
        const noteColor = localStorage.getItem('noteColor');
        
        if (fontSize) {
            const fontSizePx = fontSize + 'px';
            document.documentElement.style.setProperty('--note-font-size', fontSizePx);
            $('#textareaElem').css('font-size', fontSizePx);
            $('p, h4, h5, .card-text, .card-body').css('font-size', fontSizePx);
        }
        
        // Only apply background color if not in dark mode
        const isDarkMode = $('body').hasClass('bg-dark');
        if (noteColor && !isDarkMode) {
            document.documentElement.style.setProperty('--note-color', noteColor);
            $('#textareaElem').css('background-color', noteColor);
        }
    })
}

/**
 * Save content to IndexedDB when in offline mode
 */
function saveOfflineContent() {
    if (timeout != null) {
        window.clearTimeout(timeout);
    }

    noteChanged = true;
    const newTitle = $('#title').val();
    const contents = $('#textareaElem').val();
    localTitle = newTitle;
    localContents = contents;

    timeout = window.setTimeout(function() {
        saveToIndexedDB(newTitle, contents);
    }, 2000);
}

/**
 * Save the note to IndexedDB for offline storage
 */
function saveToIndexedDB(title, content) {
    showSaving();
    
    if (!window.OfflineNotesManager) {
        showNotSaved();
        return;
    }
    
    const note = {
        id: noteId,
        Title: title,
        Content: content,
        ModifiedAt: new Date().toISOString(),
    };
    
    window.OfflineNotesManager.saveNoteToIndexedDB(note)
        .then(success => {
            if (success) {
                showSaved();
                noteChanged = false;
                oldTitle = title;
            } else {
                showNotSaved();
            }
        })
        .catch(error => {
            console.error('Error saving to IndexedDB:', error);
            showNotSaved();
        });
}

/**
 * Show offline status indicator
 */
function showOfflineStatus() {
    // Add offline indicator to status
    const offlineIndicator = $('<div id="offline-indicator" class="alert alert-warning mt-2 mb-2">'+
        '<i class="fas fa-wifi mr-2"></i> You are offline. Changes will be saved locally and synced when you reconnect.'+
        '</div>');
    
    if (!$('#offline-indicator').length) {
        $('#statusDiv').after(offlineIndicator);
    }
}

