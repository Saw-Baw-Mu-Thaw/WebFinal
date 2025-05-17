$(document).ready(function() {
    // Load user's avatar for the header
    loadUserAvatar();
});

function loadUserAvatar() {
    $.ajax({
        url: 'api/get_profile.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.code === 0 && response.profilePic) {
                // Update header avatar
                $('#headerAvatar').attr('src', response.profilePic);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading user avatar:', error);
        }
    });
} 