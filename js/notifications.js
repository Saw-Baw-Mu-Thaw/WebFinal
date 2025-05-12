$(document).ready(function() {
    // Load notifications on page load
    loadNotifications();
    
    // Set up click handler for notifications button
    $('#notificationsBtn').on('click', function() {
        // Mark all notifications as read when dropdown is opened
        $('.notification-item.unread').each(function() {
            var notificationId = $(this).data('notification-id');
            markNotificationRead(notificationId);
            $(this).removeClass('unread');
        });
        
        // Hide the badge
        $('#notificationBadge').addClass('d-none');
    });
    
    // Refresh notifications every 30 seconds
    setInterval(loadNotifications, 30000);
});

/**
 * Load notifications for the current user
 */
function loadNotifications() {
    $.ajax({
        url: 'api/get_notifications.php',
        type: 'GET',
        dataType: 'json'
    }).done(function(response) {
        if (response.code === 0) {
            $('#loadingNotifications').hide();
            
            // Clear existing notifications (except for loading and no notifications message)
            $('.notification-item').remove();
            
            if (response.notifications.length === 0) {
                $('#noNotificationsMsg').removeClass('d-none');
            } else {
                $('#noNotificationsMsg').addClass('d-none');
                
                // Count unread notifications
                var unreadCount = 0;
                
                // Add notifications to the dropdown
                response.notifications.forEach(function(notification) {
                    var isRead = notification.IsRead == 1;
                    
                    if (!isRead) {
                        unreadCount++;
                    }
                    
                    var notificationItem = $('<a class="dropdown-item notification-item ' + (!isRead ? 'unread' : '') + '" href="#"></a>');
                    notificationItem.data('notification-id', notification.NotificationID);
                    notificationItem.data('note-id', notification.NoteID);
                    
                    // Format date
                    var date = new Date(notification.CreatedAt);
                    var formattedDate = date.toLocaleString();
                    
                    // Create notification content
                    notificationItem.append('<div><strong>' + notification.Message + '</strong></div>');
                    notificationItem.append('<small class="text-muted">Note: ' + notification.NoteTitle + '</small><br>');
                    notificationItem.append('<small class="text-muted">' + formattedDate + '</small>');
                    
                    // Add click handler to open the note
                    notificationItem.on('click', function(e) {
                        e.preventDefault();
                        openNoteFromNotification(notification.NoteID);
                        
                        // Mark as read if it's unread
                        if (!isRead) {
                            markNotificationRead(notification.NotificationID);
                        }
                    });
                    
                    // Add to the container
                    $('#notificationsContainer').append(notificationItem);
                });
                
                // Update the badge
                if (unreadCount > 0) {
                    $('#notificationBadge').removeClass('d-none').text(unreadCount);
                } else {
                    $('#notificationBadge').addClass('d-none');
                }
            }
        }
    }).fail(function() {
        console.error('Failed to load notifications');
    });
}

/**
 * Mark a notification as read
 */
function markNotificationRead(notificationId) {
    $.ajax({
        url: 'api/mark_notification_read.php',
        type: 'POST',
        data: JSON.stringify({ notificationId: notificationId }),
        contentType: 'application/json',
        dataType: 'json'
    });
}

/**
 * Open a note from a notification
 */
function openNoteFromNotification(noteId) {
    // Use existing openNoteById function from noteActions.js if available
    if (typeof window.openNoteById === 'function') {
        window.openNoteById(noteId);
    } else {
        // Fallback method
        $.ajax({
            url: 'api/open_note.php',
            type: 'POST',
            data: JSON.stringify({ noteId: noteId }),
            contentType: 'application/json',
            dataType: 'json'
        }).done(function(response) {
            if (response.code === 0) {
                window.location.href = 'edit.php';
            }
        });
    }
}

// Add styles for unread notifications
var style = document.createElement('style');
style.innerHTML = `
    .notification-item.unread {
        background-color: #f8f9fa;
        font-weight: bold;
    }
    
    .notification-item:hover {
        background-color: #e9ecef;
    }
`;
document.head.appendChild(style); 