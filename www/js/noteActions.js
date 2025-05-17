function openNote(noteId, locked) {

    if (locked) {
        // invoke password modal here

        $(".modal-title").text("Enter password")
        $("#pwdSubmitBtn").data('id', noteId);
        $("#pwdSubmitBtn").on('click', sendPwd)
        $("#passwordModal").modal('show')
    } else {
        // send request
        $.ajax({
            url: 'api/open_note.php?id=' + noteId,
            type: "GET",
            datatype: "json"
        }).done(function (response) {
            if (response['code'] == 0) {
                window.location.replace("edit.php");
            } else {
                showError(response['message'])
            }
        })
    }


}

/**
 * Open a note by its ID (used by notifications)
 * @param {number} noteId The note ID to open
 */
function openNoteById(noteId) {
    $.ajax({
        url: 'api/open_note.php',
        type: "POST",
        datatype: "json",
        contentType: 'application/json',
        data: JSON.stringify({ noteId: noteId })
    }).done(function (response) {
        if (response['code'] == 0) {
            window.location.replace("edit.php");
        } else {
            showError(response['message'])
        }
    });
}

// Make openNoteById available globally for the notifications system
// window.openNoteById = openNoteById;

function sendPwd(e) {
    var noteId = $(e.target).data('id')
    var Password = $('#notePwd').val();

    // send ajax
    $.ajax({
        url: 'api/open_locked_note.php',
        type: "POST",
        datatype: "json",
        contenttype: 'application/json',
        data: JSON.stringify({ id: noteId, password: Password })
    }).done(function (response) {
        if (response['code'] == 0) {
            window.location.replace("edit.php");
        } else {
            showError(response['message'])
        }
    })
}

function deleteNote(noteId, title) {
    $("#errorDiv").hide();

    $(".modal-title").text("Delete " + title + "?");
    $("#delConfirmBtn").data("noteId", noteId)
    $('#delConfirmBtn').data("title", title)
    $("#delConfirmBtn").on('click', sendDelete);
    $("#deleteModal").modal('show');
}

function sendDelete(e) {
    // send ajax to delete note

    var noteId = $(e.target).data("noteId")
    var title = $(e.target).data("title")

    $.ajax({
        url: 'api/delete_note.php?noteId=' + noteId + '&title=' + title,
        type: "DELETE",
        datatype: "json",
    }).done(function (response) {
        if (response['code'] == 0) {
            location.reload();
        } else {
            showError(response['message'])
        }
    })
}

function lockNote(noteId) {
    $(".modal-title").text("Set your password");
    $('#pwdSetBtn').data("id", noteId);
    $("#pwdSetBtn").on('click', setPassword);
    $("#pwdError").hide();
    $("#setPwdModal").modal('show');
}

function setPassword(e) {

    var NoteId = $(e.target).data('id')
    var password1 = $("#pwd1").val();
    var password2 = $("#pwd2").val();


    if (password1.length == 0 || password2.length == 0) {
        $("#pwdError").show()
        $("#pwdError").text('Passwords cannot be empty')
        window.setTimeout(function () { $("#pwdError").hide() }, 3000)
    }
    else if (password1 != password2) {
        $("#pwdError").show()
        $("#pwdError").text('Passwords are different')
        window.setTimeout(function () { $("#pwdError").hide() }, 3000)

    } else {
        // send ajax
        $.ajax({
            url: 'api/add_locked_note.php',
            type: "POST",
            datatype: "json",
            contenttype: "application/json",
            data: JSON.stringify({ noteId: NoteId, password: password1 })
        }).done(function (response) {
            if (response['code'] == 0) {
                location.reload();
            } else {
                showError(response['message'])
            }
        })
    }
}

function removeLock(noteId) {

    $(".modal-title").text("Enter password")
    $("#pwdSubmitBtn").data('id', noteId);
    $("#pwdSubmitBtn").on('click', sendPwdforRemove)
    $("#passwordModal").modal('show')

}

function sendPwdforRemove(e) {
    var noteId = $(e.target).data('id')
    var Password = $('#notePwd').val();

    $.ajax({
        url: 'api/open_locked_note.php',
        type: "POST",
        datatype: "json",
        contenttype: 'application/json',
        data: JSON.stringify({ id: noteId, password: Password })
    }).done(function (response) {
        if (response['code'] == 0) {

            // send ajax
            $.ajax({
                url: 'api/remove_lock.php?id=' + noteId,
                type: 'DELETE',
                datatype: "json"
            }).done(function (response) {
                if (response['code'] == 0) {
                    location.reload();
                }
                else {
                    showError(response['message'])
                }
            })
        } else {
            showError(response['message'])
        }
    })
}

function changeNotePassword(noteId) {

    // show the modal
    $(".modal-title").text("Change Password");
    $('#pwdChangeBtn').data("id", noteId);
    $("#pwdChangeBtn").on('click', sendNewPassword);
    $("#pwdChangeError").hide();
    $("#changePwdModal").modal('show');
}

function sendNewPassword(e) {
    var NoteId = $(e.target).data('id');
    var OldPwd = $('#oldPwd').val();
    var NewPwd1 = $('#pwd3').val();
    var NewPwd2 = $('#pwd4').val();

    if (NewPwd1 !== NewPwd2) {
        $('#pwdChangeError').text("New passwords are not the same")
        $("#pwdChangeError").show();
        window.setTimeout(() => {
            $('#pwdChangeError').hide()
        }, 1000)

        window.setTimeout(function () { $("#pwdChangeError").hide() }, 2500)
    }

    if (OldPwd.length == 0 || NewPwd1.length == 0 || NewPwd2.length == 0) {
        $('#pwdChangeError').text("Passwords can't be empty")
        $("#pwdChangeError").show();
        window.setTimeout(() => {
            $('#pwdChangeError').hide()
        }, 1000)

        window.setTimeout(function () { $("#pwdChangeError").hide() }, 2500)
    }

    $.ajax({
        url: 'api/update_note_pwd.php',
        type: "POST",
        datatype: "json",
        data: JSON.stringify({ oldPwd: OldPwd, newPwd1: NewPwd1, newPwd2: NewPwd2, noteId: NoteId })
    }).done((response) => {
        if (response['code'] == 0) {
            // location.reload();
            $('#pwdChangeError').removeClass('alert-danger')
            $('#pwdChangeError').addClass('alert-success')
            $('#pwdChangeError').text("Passwords changed successfully")
            $("#pwdChangeError").show();

            window.setTimeout(() => {
                $('#pwdChangeError').hide()
                $('#pwdChangeError').removeClass('alert-success')
                $('#pwdChangeError').addClass('alert-danger')
                $('#changePwdModal').modal('toggle')
            }, 500)

            window.setTimeout(function () { $("#pwdChangeError").hide() }, 2500)
        } else {
            $('#pwdChangeError').text(response['message'])
            $("#pwdChangeError").show();
            window.setTimeout(() => {
                $('#pwdChangeError').hide()
            }, 1000)

        }
    })
}

/**
 * Creates a new empty note in the database and redirects to edit page
 */
function createEmptyNote() {
    // Create a default title - user can change it in the edit page
    const defaultTitle = "New Note " + new Date().toLocaleString().replace(/[\/:\\]/g, '-');
    
    // Check if offline - create in IndexedDB if so
    if (window.OfflineNotesManager && !window.OfflineNotesManager.isOnline()) {
        console.log('Creating offline note');
        window.OfflineNotesManager.createNoteOffline(defaultTitle, '')
            .then(note => {
                // Store the note ID in localStorage
                localStorage.setItem('currentNoteId', note.id);
                // Redirect to the edit page
                window.location.replace('edit.php');
            })
            .catch(error => {
                console.error('Error creating offline note:', error);
                showError('Failed to create offline note');
            });
        return;
    }
    
    // Otherwise, use the server to create a note
    $.ajax({
        url: 'api/create_empty_note.php',
        type: "POST",
        datatype: "json",
        contentType: "application/json",
        data: JSON.stringify({ title: defaultTitle })
    }).done(function (response) {
        if (response['code'] == 0) {
            // Redirect to edit page for the newly created note
            window.location.replace("edit.php");
        } else {
            showError(response['message']);
        }
    }).fail(function() {
        // If we're offline, try to create an offline note
        if (window.OfflineNotesManager) {
            window.OfflineNotesManager.createNoteOffline(defaultTitle, '')
                .then(note => {
                    // Store the note ID in localStorage
                    localStorage.setItem('currentNoteId', note.id);
                    // Redirect to the edit page
                    window.location.replace('edit.php');
                })
                .catch(error => {
                    console.error('Error creating offline note:', error);
                    showError('Failed to create offline note');
                });
        } else {
            showError("Failed to create new note - you appear to be offline");
        }
    });
}

function showError(msg) {
    $("#errorDiv").show();
    $("#errorDiv").text(msg);

    window.setTimeout(function () { $("#errorDiv").hide() }, 2500)
}

