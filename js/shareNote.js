$(document).ready(function() {
    // Hide share button if not owner
    checkOwnership();
    
    // Set up event handlers
    $('#shareModal').on('show.bs.modal', loadCollaborators);
    $('#addShareBtn').on('click', shareNote);
});

/**
 * Check if the current user is the owner of the note
 */
function checkOwnership() {
    $.ajax({
        url: "api/get_note_contents.php",
        type: "GET",
        datatype: "json"
    }).done(function(response) {
        // If it's a shared note and the role is not EDITOR or VIEWER, hide the share button
        if (response.hasOwnProperty('NoteID')) {
            noteId = response.NoteID;
        }
        
        // Check if user is owner of the note
        $.ajax({
            url: 'api/get_collaborators.php',
            type: 'GET',
            data: { noteId: noteId },
            dataType: 'json'
        }).fail(function() {
            // If the call fails, the user is probably not the owner
            $('#shareBtn').hide();
        });
    });
}

/**
 * Load collaborators for the current note
 */
function loadCollaborators() {
    // Reset modal state
    $('#shareEmail').val('');
    $('#sharePermission').val('VIEWER');
    $('#shareErrorMsg').addClass('d-none').text('');
    $('#shareSuccessMsg').addClass('d-none').text('');
    $('#loadingCollaborators').show();
    $('#noCollaboratorsMsg').addClass('d-none');
    $('#collaboratorsListGroup').empty();
    
    $.ajax({
        url: 'api/get_collaborators.php',
        type: 'GET',
        data: { noteId: noteId },
        dataType: 'json'
    }).done(function(response) {
        $('#loadingCollaborators').hide();
        
        if (response.code === 0) {
            if (response.collaborators.length === 0) {
                $('#noCollaboratorsMsg').removeClass('d-none');
            } else {
                // Populate collaborators list
                response.collaborators.forEach(function(collaborator) {
                    var listItem = $('<li class="list-group-item d-flex justify-content-between align-items-center"></li>');
                    
                    // Email and role
                    var userInfo = $('<div></div>');
                    userInfo.append('<strong>' + collaborator.Email + '</strong>');
                    userInfo.append('<br><span class="badge badge-' + 
                        (collaborator.Role === 'EDITOR' ? 'success' : 'primary') + '">' + 
                        (collaborator.Role === 'EDITOR' ? 'Can edit' : 'View only') + '</span>');
                    
                    // Action buttons
                    var actionBtns = $('<div></div>');
                    
                    // Change permission button
                    var changePermBtn = $('<button class="btn btn-sm btn-outline-primary mr-2">Change</button>');
                    changePermBtn.click(function() {
                        var newRole = collaborator.Role === 'EDITOR' ? 'VIEWER' : 'EDITOR';
                        shareNoteWithUser(collaborator.Email, newRole);
                    });
                    
                    // Revoke access button
                    var revokeBtn = $('<button class="btn btn-sm btn-outline-danger">Revoke</button>');
                    revokeBtn.click(function() {
                        revokeAccess(collaborator.Collaborator);
                    });
                    
                    actionBtns.append(changePermBtn);
                    actionBtns.append(revokeBtn);
                    
                    listItem.append(userInfo);
                    listItem.append(actionBtns);
                    
                    $('#collaboratorsListGroup').append(listItem);
                });
            }
        } else {
            showShareError(response.message);
        }
    }).fail(function() {
        $('#loadingCollaborators').hide();
        showShareError('Failed to load collaborators.');
    });
}

/**
 * Share the note with a user
 */
function shareNote() {
    var email = $('#shareEmail').val().trim();
    var permission = $('#sharePermission').val();
    
    // Validate email
    if (!email) {
        showShareError('Please enter an email address.');
        return;
    }
    
    // Validate email format
    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        showShareError('Please enter a valid email address.');
        return;
    }
    
    shareNoteWithUser(email, permission);
}

/**
 * Share the note with a specific user and permission
 */
function shareNoteWithUser(email, permission) {
    // Hide any previous messages
    $('#shareErrorMsg').addClass('d-none');
    $('#shareSuccessMsg').addClass('d-none');
    
    $.ajax({
        url: 'api/check_email.php',
        type: 'GET',
        data: { email: email },
        dataType: 'json'
    }).done(function(response) {
        if (response.code === 0 && response.exists) {
            // Email exists, proceed with sharing
            $.ajax({
                url: 'api/share_note.php',
                type: 'POST',
                data: JSON.stringify({ 
                    noteId: noteId,
                    email: email,
                    role: permission
                }),
                contentType: 'application/json',
                dataType: 'json'
            }).done(function(response) {
                if (response.code === 0) {
                    // Success
                    showShareSuccess('Note shared successfully' + 
                        (response.emailSent ? ' and email notification sent.' : '.'));
                    
                    // Clear form
                    $('#shareEmail').val('');
                    
                    // Reload collaborators list
                    loadCollaborators();
                } else {
                    showShareError(response.message);
                }
            }).fail(function() {
                showShareError('Failed to share note. Please try again.');
            });
        } else {
            showShareError('Email not found. The user must be registered in the system.');
        }
    }).fail(function() {
        showShareError('Failed to verify email. Please try again.');
    });
}

/**
 * Revoke access for a collaborator
 */
function revokeAccess(collaboratorId) {
    if (confirm('Are you sure you want to revoke access for this user?')) {
        $.ajax({
            url: 'api/revoke_sharing.php',
            type: 'POST',
            data: JSON.stringify({ 
                noteId: noteId,
                collaboratorId: collaboratorId
            }),
            contentType: 'application/json',
            dataType: 'json'
        }).done(function(response) {
            if (response.code === 0) {
                // Success, reload collaborators list
                loadCollaborators();
            } else {
                showShareError(response.message);
            }
        }).fail(function() {
            showShareError('Failed to revoke access. Please try again.');
        });
    }
}

/**
 * Show an error message in the share modal
 */
function showShareError(message) {
    $('#shareErrorMsg').removeClass('d-none').text(message);
    $('#shareSuccessMsg').addClass('d-none');
}

/**
 * Show a success message in the share modal
 */
function showShareSuccess(message) {
    $('#shareSuccessMsg').removeClass('d-none').text(message);
    $('#shareErrorMsg').addClass('d-none');
} 