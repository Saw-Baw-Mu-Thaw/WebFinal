/**
 * Label Modal functionality
 * Handles opening the label modal and managing labels for notes
 */

// Global variable to store the current note ID
let currentNoteId = null;

/**
 * Opens the label modal for a specific note
 * @param {number} noteId The ID of the note to manage labels for
 */
function openLabelModal(noteId) {
    currentNoteId = noteId;
    
    // Clear previous labels
    $('#labelModalBody').empty();
    $('#labelList').empty();
    $('#txtLabelModal').val('');
    
    // Show loading indicator
    $('#labelLoadingSpinner').show();
    
    // Get the labels for this note
    $.ajax({
        url: `api/get_note_labels.php?noteId=${noteId}`,
        type: 'GET',
        dataType: 'json'
    }).done(function(response) {
        if (response.code === 0) {
            // Hide loading indicator
            $('#labelLoadingSpinner').hide();
            
            // Display note title
            $('#labelModalTitle').text(`Manage Labels: ${response.title}`);
            
            // Display existing labels
            const labels = response.labels || [];
            if (labels.length === 0) {
                $('#noLabelsMsg').show();
            } else {
                $('#noLabelsMsg').hide();
                
                // Add each label to the list
                for (let i = 0; i < labels.length; i++) {
                    const label = labels[i];
                    addLabelToModal(label);
                }
            }
            
            // Get all available labels for the dropdown
            $.ajax({
                url: 'api/get_labels.php',
                type: 'GET',
                dataType: 'json'
            }).done(function(labelsResponse) {
                if (labelsResponse.code === 0) {
                    const allLabels = labelsResponse.labels || [];
                    
                    // Clear dropdown
                    $('#availableLabels').empty();
                    
                    // Add default option
                    $('#availableLabels').append('<option value="">Select a label</option>');
                    
                    // Add each label to the dropdown
                    for (let i = 0; i < allLabels.length; i++) {
                        const label = allLabels[i];
                        // Check if this label is already assigned to the note
                        let isAssigned = false;
                        for (let j = 0; j < labels.length; j++) {
                            if (labels[j].LabelID === label.LabelID) {
                                isAssigned = true;
                                break;
                            }
                        }
                        
                        if (!isAssigned) {
                            $('#availableLabels').append(`<option value="${label.LabelID}">${label.Label}</option>`);
                        }
                    }
                }
            });
            
            // Show the modal
            $('#labelModal').modal('show');
        } else {
            showLabelError(response.message || 'Failed to load labels');
        }
    }).fail(function() {
        $('#labelLoadingSpinner').hide();
        showLabelError('Could not connect to server');
    });
}

/**
 * Adds a label to the modal display
 * @param {Object} label The label object to add
 */
function addLabelToModal(label) {
    const labelElem = $(`<li class="list-group-item d-flex justify-content-between align-items-center">
        <span>#${label.Label}</span>
        <div>
            <button class="btn btn-sm btn-danger removeLabel" data-id="${label.LabelID}">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </li>`);
    
    // Add event handler for remove button
    $(labelElem).find('.removeLabel').on('click', function() {
        removeLabelFromNote($(this).data('id'));
    });
    
    $('#labelList').append(labelElem);
}

/**
 * Creates a new label
 */
function createNewLabel() {
    const labelName = $('#txtLabelModal').val().trim();
    
    if (labelName.length === 0) {
        showLabelError('Label name cannot be empty');
        return;
    }
    
    // Show loading
    $('#labelBtnSpinner').show();
    $('#createLabelBtn').prop('disabled', true);
    
    $.ajax({
        url: 'api/add_label.php',
        type: 'POST',
        dataType: 'json',
        contentType: 'application/json',
        data: JSON.stringify({ label: labelName })
    }).done(function(response) {
        $('#labelBtnSpinner').hide();
        $('#createLabelBtn').prop('disabled', false);
        
        if (response.code === 0) {
            // Clear input
            $('#txtLabelModal').val('');
            
            // Add the new label to the note
            if (currentNoteId) {
                addLabelToNote(response.labelId, labelName);
            }
            
            showLabelSuccess('Label created successfully');
        } else {
            showLabelError(response.message || 'Failed to create label');
        }
    }).fail(function() {
        $('#labelBtnSpinner').hide();
        $('#createLabelBtn').prop('disabled', false);
        showLabelError('Could not connect to server');
    });
}

/**
 * Adds an existing label to the current note
 */
function addExistingLabel() {
    const labelId = $('#availableLabels').val();
    const labelText = $('#availableLabels option:selected').text();
    
    if (!labelId) {
        showLabelError('Please select a label');
        return;
    }
    
    addLabelToNote(labelId, labelText);
}

/**
 * Adds a label to the current note
 * @param {number} labelId The ID of the label to add
 * @param {string} labelText The text of the label
 */
function addLabelToNote(labelId, labelText) {
    if (!currentNoteId) {
        showLabelError('No note selected');
        return;
    }
    
    // Show loading
    $('#labelAddBtnSpinner').show();
    $('#addExistingLabelBtn').prop('disabled', true);
    
    $.ajax({
        url: 'api/add_note_label.php',
        type: 'POST',
        dataType: 'json',
        contentType: 'application/json',
        data: JSON.stringify({ 
            noteId: currentNoteId,
            labelId: labelId
        })
    }).done(function(response) {
        $('#labelAddBtnSpinner').hide();
        $('#addExistingLabelBtn').prop('disabled', false);
        
        if (response.code === 0) {
            // Add the label to the display
            addLabelToModal({
                LabelID: labelId,
                Label: labelText
            });
            
            // Remove from dropdown
            $(`#availableLabels option[value="${labelId}"]`).remove();
            
            // Hide no labels message if visible
            $('#noLabelsMsg').hide();
            
            showLabelSuccess('Label added to note');
        } else {
            showLabelError(response.message || 'Failed to add label to note');
        }
    }).fail(function() {
        $('#labelAddBtnSpinner').hide();
        $('#addExistingLabelBtn').prop('disabled', false);
        showLabelError('Could not connect to server');
    });
}

/**
 * Removes a label from the current note
 * @param {number} labelId The ID of the label to remove
 */
function removeLabelFromNote(labelId) {
    if (!currentNoteId) {
        showLabelError('No note selected');
        return;
    }
    
    $.ajax({
        url: 'api/remove_note_label.php',
        type: 'POST',
        dataType: 'json',
        contentType: 'application/json',
        data: JSON.stringify({ 
            noteId: currentNoteId,
            labelId: labelId
        })
    }).done(function(response) {
        if (response.code === 0) {
            // Remove the label from the display
            $(`#labelList .removeLabel[data-id="${labelId}"]`).closest('li').remove();
            
            // Get the label text
            const labelText = response.labelName;
            
            // Add back to dropdown if available
            if (labelText) {
                $('#availableLabels').append(`<option value="${labelId}">${labelText}</option>`);
            }
            
            // Show no labels message if no labels left
            if ($('#labelList li').length === 0) {
                $('#noLabelsMsg').show();
            }
            
            showLabelSuccess('Label removed from note');
        } else {
            showLabelError(response.message || 'Failed to remove label from note');
        }
    }).fail(function() {
        showLabelError('Could not connect to server');
    });
}

/**
 * Shows a success message in the label modal
 * @param {string} message The message to show
 */
function showLabelSuccess(message) {
    $('#labelErrorMsg').hide();
    $('#labelSuccessMsg').text(message).show();
    
    // Hide after 3 seconds
    setTimeout(function() {
        $('#labelSuccessMsg').hide();
    }, 3000);
}

/**
 * Shows an error message in the label modal
 * @param {string} message The message to show
 */
function showLabelError(message) {
    $('#labelSuccessMsg').hide();
    $('#labelErrorMsg').text(message).show();
    
    // Hide after 3 seconds
    setTimeout(function() {
        $('#labelErrorMsg').hide();
    }, 3000);
}

// Make functions globally available
window.openLabelModal = openLabelModal;
window.createNewLabel = createNewLabel;
window.addExistingLabel = addExistingLabel;
