import { formatDatetime } from "./utils.js";
import { removeAtchImg } from "./removeAttachedImg.js";

function generateGrid(obj) {
    // Create responsive card with improved classes for better sizing across all devices
    var card = $("<div class='card note-card grid-card mode-target'></div>");
    
    // Add data attributes for responsive behaviors
    $(card).attr('data-note-id', obj['NoteID']);
    $(card).attr('data-note-type', obj['SharedNote'] ? 'shared' : 'personal');
    
    // Apply user's note color preference if it exists
    const noteColor = localStorage.getItem('noteColor');
    if (noteColor) {
        $(card).css('background-color', noteColor);
    }
    
    // Add action buttons that appear on hover
    var cardActions = $("<div class='card-actions'></div>");
    
    // Open button
    var openBtn = $(`<button class='btn btn-primary' title='Edit Note' onclick='openNote(${obj['NoteID']}, ${obj['Locked']})'><i class="far fa-edit"></i></button>`);
    $(cardActions).append(openBtn);
    
    // Add buttons based on note state and permissions
    if (obj['SharedNote'] == false) {
        // Delete button
        var delBtn = $(`<button class='btn btn-danger' title='Delete Note' onclick='deleteNote(${obj['NoteID']}, "${obj['Title']}", ${obj['Locked']})'><i class="fas fa-trash"></i></button>`);
        $(cardActions).append(delBtn);
        
        // Pin/Unpin button
        var pinBtn;
        if (obj['Pinned'] == true) {
            pinBtn = $(`<button class='btn btn-warning' title='Unpin Note' onclick='removePin(${obj['NoteID']})'><i class="fas fa-thumbtack"></i></button>`);
        } else {
            pinBtn = $(`<button class='btn btn-outline-warning' title='Pin Note' onclick='addPin(${obj['NoteID']})'><i class="fas fa-thumbtack"></i></button>`);
        }
        $(cardActions).append(pinBtn);
        
        // Lock/Unlock button
        var lockBtn;
        if (obj['Locked'] == false) {
            lockBtn = $(`<button class='btn btn-outline-secondary' title='Password Protect' onclick='lockNote(${obj['NoteID']})'><i class="fas fa-lock"></i></button>`);
        } else {
            lockBtn = $(`<button class='btn btn-secondary' title='Remove Password' onclick='removeLock(${obj['NoteID']})'><i class="fas fa-unlock"></i></button>`);
            
            // Change password button for locked notes
            var changeBtn = $(`<button class='btn btn-info' title='Change Password' onclick='changeNotePassword(${obj['NoteID']})'><i class="fas fa-key"></i></button>`);
            $(cardActions).append(changeBtn);
        }
        $(cardActions).append(lockBtn);
        
        // Remove attached image button
        if (obj['AttachedImg'] != null) {
            var removeAttachedImgBtn = $(`<button class='btn btn-outline-danger' title='Remove Image'><i class='fas fa-image'></i></button>`);
            $(removeAttachedImgBtn).attr('data-id', obj['NoteID']);
            $(removeAttachedImgBtn).attr('data-loc', obj['AttachedImg']);
            $(removeAttachedImgBtn).on('click', removeAtchImg);
            $(cardActions).append(removeAttachedImgBtn);
        }
    }
    
    $(card).append(cardActions);
    
    // Image section with drop functionality - enhanced for responsive design
    if (obj['AttachedImg'] != null) {
        var imageContainer = $("<div class='card-img-container position-relative overflow-hidden'></div>");
        var attachedImg = $(`<img class='card-img-top w-100 h-auto' src='${obj['AttachedImg']}' alt='Note image' loading='lazy' />`);
        
        if (obj['SharedNote'] == false) {
            $(attachedImg).on('drop', imgDropHandler);
            $(attachedImg).on('dragover', imgDragOverHandler);
            $(attachedImg).on('dragenter', imgDragEnter);
            $(attachedImg).on('dragleave', imgDragLeave);
            $(attachedImg).attr('data-id', obj['NoteID']);
        }
        $(imageContainer).append(attachedImg);
        $(card).append(imageContainer);
    } else if (obj['SharedNote'] == false) {
        // Only show drop area for own notes - optimized for mobile
        var dropArea = $("<div class='card-img-container text-center p-3 border-bottom d-flex flex-column justify-content-center align-items-center' style='background-color: rgba(0,0,0,0.05); min-height: 100px;'></div>");
        var dropIcon = $("<i class='fas fa-cloud-upload-alt fa-2x text-muted'></i>");
        var dropText = $("<p class='text-muted small mt-2 mb-0 d-none d-sm-block'>Drag & drop image here</p>");
        var mobileText = $("<p class='text-muted small mt-2 mb-0 d-block d-sm-none'>Tap to add image</p>");
        
        $(dropArea).append(dropIcon, dropText, mobileText);
        $(dropArea).on('drop', imgDropHandler);
        $(dropArea).on('dragover', imgDragOverHandler);
        $(dropArea).on('dragenter', imgDragEnter);
        $(dropArea).on('dragleave', imgDragLeave);
        $(dropArea).attr('data-id', obj['NoteID']);
        $(card).append(dropArea);
    }
    
    // Add note status indicators (pinned, locked) - add after image but with high z-index
    var statusIcons = $("<div class='position-absolute top-0 left-0 p-2' style='z-index: 100;'></div>");
    if (obj['Pinned'] == true) {
        $(statusIcons).append("<span class='badge badge-pill badge-warning mr-1' title='Pinned'><i class='fas fa-thumbtack'></i></span>");
    }
    if (obj['Locked'] == true) {
        $(statusIcons).append("<span class='badge badge-pill badge-secondary' title='Password Protected'><i class='fas fa-lock'></i></span>");
    }
    if (obj['SharedNote'] == true) {
        $(statusIcons).append("<span class='badge badge-pill badge-info ml-1' title='Shared Note'><i class='fas fa-share-alt'></i></span>");
    }
    $(card).append(statusIcons);

    // Card body with title and content
    var cardBody = $("<div class='card-body'></div>");
    var cardTitle = $("<h5 class='card-title'></h5>").text(obj['Title']);
    var cardText = $("<div class='card-text mb-3'></div>");
    
    // Note content preview (first ~100 characters)
    if (obj['Content'] && obj['Content'].length > 0) {
        var contentPreview = obj['Content'].substring(0, 100) + (obj['Content'].length > 100 ? "..." : "");
        var contentText = $("<p class='mb-2'></p>").text(contentPreview);
        $(cardText).append(contentText);
    }
    
    // Last modified date
    var lastModified = $("<small class='text-muted d-block mb-2'></small>").html("<i class='far fa-clock mr-1'></i> " + formatDatetime(obj['LastModified']));
    $(cardText).append(lastModified);
    
    // Labels section
    if (obj['Labels'] && obj['Labels'].length > 0) {
        var labelContainer = $("<div class='mt-2'></div>");
        var labelList = obj['Labels'];
        
        for (var j = 0; j < labelList.length; j++) {
            var label = $(`<span class='label-badge'>#${labelList[j]['Label']}</span>`);
            $(labelContainer).append(label);
        }
        
        $(cardText).append(labelContainer);
    }
    
    $(cardBody).append(cardTitle, cardText);
    $(card).append(cardBody);
    $("#mainContent").append(card);
}

export { generateGrid }