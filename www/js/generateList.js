import { formatDatetime } from "./utils.js";

function generateList(response) {
    // Create a responsive table with modern styling
    var tableContainer = $("<div class='table-responsive'></div>");
    var table = $("<table class='table table-hover mode-target'></table>");
    
    // Apply user's note color preference as background
    const noteColor = localStorage.getItem('noteColor');
    if (noteColor) {
        $(table).css('background-color', noteColor);
    }
    
    // Create table header
    var thead = $("<thead class='thead-light'></thead>");
    var headRow = $("<tr></tr>");
    $(headRow).append(
        "<th scope='col' width='5%'>#</th>",
        "<th scope='col' width='25%'>Title</th>",
        "<th scope='col' width='15%'>Last Modified</th>",
        "<th scope='col' width='10%'>Status</th>",
        "<th scope='col' width='25%'>Labels</th>",
        "<th scope='col' width='20%'>Actions</th>"
    );
    $(thead).append(headRow);
    $(table).append(thead);

    // Create table body
    var tbody = $("<tbody></tbody>");

    for (var i = 0; i < response.length; i++) {
        var obj = response[i];
        var trow = $("<tr class='note-list-item'></tr>");
        
        // Index column
        var index = $(`<td>${i + 1}</td>`);
        
        // Title column with locked/shared indicators
        var titleContent = obj['Title'];
        if (obj['Locked']) {
            titleContent = `<i class="fas fa-lock text-secondary mr-2" title="Password Protected"></i> ${titleContent}`;
        }
        if (obj['SharedNote']) {
            titleContent = `<i class="fas fa-share-alt text-info mr-2" title="Shared Note"></i> ${titleContent}`;
        }
        var title = $(`<td class="font-weight-bold">${titleContent}</td>`);
        
        // Last modified column with formatted date
        var lastModified = $(`<td><small class="text-muted"><i class="far fa-clock mr-1"></i>${formatDatetime(obj['LastModified'])}</small></td>`);
        
        // Status column (pinned status)
        var statusCell = $("<td></td>");
        if (obj['Pinned'] == true) {
            $(statusCell).html('<span class="badge badge-warning"><i class="fas fa-thumbtack mr-1"></i>Pinned</span>');
        } else {
            $(statusCell).html('<span class="badge badge-light text-muted">Unpinned</span>');
        }
        
        // Labels column
        var labelCell = $("<td></td>");
        var labelList = obj['Labels'];
        
        if (labelList && labelList.length > 0) {
            for (var j = 0; j < labelList.length; j++) {
                var label = $(`<span class='label-badge'>#${labelList[j]['Label']}</span>`);
                $(labelCell).append(label);
            }
        } else {
            $(labelCell).html('<small class="text-muted">No labels</small>');
        }
        
        // Actions column with buttons
        var actionCell = $("<td class='text-right'></td>");
        var btnGroup = $("<div class='btn-group btn-group-sm'></div>");
        
        // Open button
        var openBtn = $(`<button class='btn btn-primary' title='Edit Note' onclick='openNote(${obj['NoteID']}, ${obj['Locked']})'><i class="far fa-edit"></i></button>`);
        $(btnGroup).append(openBtn);
        
        // Add conditional buttons
        if (!obj['SharedNote']) {
            // Delete button
            var delBtn = $(`<button class='btn btn-danger' title='Delete Note' onclick='deleteNote(${obj['NoteID']}, "${obj['Title']}", ${obj['Locked']})'><i class="fas fa-trash"></i></button>`);
            $(btnGroup).append(delBtn);
            
            // Pin/Unpin button
            var pinBtn;
            if (obj['Pinned']) {
                pinBtn = $(`<button class='btn btn-warning' title='Unpin Note' onclick='removePin(${obj['NoteID']})'><i class="fas fa-thumbtack"></i></button>`);
            } else {
                pinBtn = $(`<button class='btn btn-outline-warning' title='Pin Note' onclick='addPin(${obj['NoteID']})'><i class="fas fa-thumbtack"></i></button>`);
            }
            $(btnGroup).append(pinBtn);
            
            // Lock/Unlock button
            var lockBtn;
            if (obj['Locked']) {
                lockBtn = $(`<button class='btn btn-secondary' title='Remove Password' onclick='removeLock(${obj['NoteID']})'><i class="fas fa-unlock"></i></button>`);
                
                // Change password button for locked notes
                var changeBtn = $(`<button class='btn btn-info' title='Change Password' onclick='changeNotePassword(${obj['NoteID']})'><i class="fas fa-key"></i></button>`);
                $(btnGroup).append(changeBtn);
            } else {
                lockBtn = $(`<button class='btn btn-outline-secondary' title='Password Protect' onclick='lockNote(${obj['NoteID']})'><i class="fas fa-lock"></i></button>`);
            }
            $(btnGroup).append(lockBtn);
        }
        
        $(actionCell).append(btnGroup);
        $(trow).append(index, title, lastModified, statusCell, labelCell, actionCell);
        $(tbody).append(trow);
    }

    $(table).append(tbody);
    $(tableContainer).append(table);
    $("#mainContent").append(tableContainer);
    
    // Add empty state if no notes
    if (response.length === 0) {
        var emptyState = $(`
            <div class="text-center p-5 w-100">
                <i class="fas fa-sticky-note fa-3x text-muted mb-3"></i>
                <h4>No notes yet</h4>
                <p class="text-muted">Create your first note by clicking the + button</p>
            </div>
        `);
        $("#mainContent").append(emptyState);
    }
}

export { generateList }