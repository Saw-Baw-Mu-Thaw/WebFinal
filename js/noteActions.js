function openNote(noteId, locked) {
    // console.log(noteId, locked);

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

function sendPwd(e) {


    var noteId = $(e.target).data('id')
    var Password = $('#notePwd').val();

    console.log('sending password : ', Password, noteId);

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
    // console.log("NOte ID : " + noteId)
    $("#errorDiv").hide();

    $(".modal-title").text("Delete " + title + "?");
    $("#delConfirmBtn").data("noteId", noteId)
    $('#delConfirmBtn').data("title", title)
    $("#delConfirmBtn").on('click', sendDelete);
    $("#deleteModal").modal('show');
}

function sendDelete(e) {
    // send ajax to delete note
    // console.log(e.target);

    var noteId = $(e.target).data("noteId")
    var title = $(e.target).data("title")
    // console.log("Delete Note " + noteId);

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
    // console.log("NOte ID : " + noteId)

    console.log('locking note');
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

    console.log('setting password')

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
    console.log("NOte ID : " + noteId)

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
}

function createNote() {
    var Title = $("#txtTitle").val()

    $.ajax({
        url: 'api/create_note.php',
        type: "POST",
        datatype: "json",
        contenttype: "application/json",
        data: JSON.stringify({ title: Title })
    }).done(function (response) {
        if (response['code'] == 0) {
            window.location.replace('edit.php');
        } else {
            // easiest way
            showError(response['message'])
        }
    })
}

function showError(msg) {
    $("#errorDiv").show();
    $("#errorDiv").text(msg);

    window.setTimeout(function () { $("#errorDiv").hide() }, 2500)
}

function showSharing(noteId) {
    // Store the current note ID
    window.currentNoteId = noteId;
    
    // Clear previous sharing list
    $("#sharingList").empty();
    
    // Get current sharing information
    $.ajax({
        url: 'api/get_note_sharing.php?noteId=' + noteId,
        type: 'GET',
        dataType: 'json'
    }).done(function(response) {
        if (response.code === 0) {
            // Populate sharing list
            response.sharing_info.forEach(function(share) {
                var item = $('<div class="list-group-item d-flex justify-content-between align-items-center"></div>');
                var info = $('<span></span>').text(share.email + ' (' + share.role + ')');
                var removeBtn = $('<button class="btn btn-danger btn-sm">Remove</button>')
                    .click(function() {
                        removeSharing(share.email);
                    });
                item.append(info, removeBtn);
                $("#sharingList").append(item);
            });
        } else {
            showError(response.message);
        }
    }).fail(function() {
        showError('Failed to load sharing information');
    });
    
    // Show the modal
    $("#sharingModal").modal('show');
}

function updateSharing() {
    var email = $("#shareEmail").val();
    var role = $("#shareRole").val();
    
    if (!email) {
        showError('Please enter an email address');
        return;
    }
    
    $.ajax({
        url: 'api/update_sharing.php',
        type: 'POST',
        dataType: 'json',
        contentType: 'application/json',
        data: JSON.stringify({
            noteId: window.currentNoteId,
            email: email,
            role: role
        })
    }).done(function(response) {
        if (response.code === 0) {
            // Refresh sharing list
            showSharing(window.currentNoteId);
            // Clear input
            $("#shareEmail").val('');
        } else {
            showError(response.message);
        }
    }).fail(function() {
        showError('Failed to update sharing permissions');
    });
}

function removeSharing(email) {
    $.ajax({
        url: 'api/remove_sharing.php?noteId=' + window.currentNoteId + '&email=' + encodeURIComponent(email),
        type: 'DELETE',
        dataType: 'json'
    }).done(function(response) {
        if (response.code === 0) {
            // Refresh sharing list
            showSharing(window.currentNoteId);
        } else {
            showError(response.message);
        }
    }).fail(function() {
        showError('Failed to remove sharing permissions');
    });
}