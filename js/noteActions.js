function openNote(noteId, locked) {
    console.log(noteId, locked);

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

    // console.log('locking note');
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

function changeNotePassword(noteId) {
    // console.log('Change password of ', noteId)

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
            }, 500)

            window.setTimeout(function () { $("#pwdChangeError").hide() }, 2500)
        } else {
            $('#pwdChangeError').text(response['message'])
            $("#pwdChangeError").show();
            window.setTimeout(() => {
                $('#pwdChangeError').hide()
            }, 1000)
            $('#changePwdModal').modal('hide')
        }
    })
}

function showError(msg) {
    $("#errorDiv").show();
    $("#errorDiv").text(msg);

    window.setTimeout(function () { $("#errorDiv").hide() }, 2500)
}

