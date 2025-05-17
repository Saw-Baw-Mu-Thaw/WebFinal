$(document).ready(function() {
    // Load user profile data
    loadUserProfile();
    
    // Handle profile picture preview
    $('#profilePictureInput').change(function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#avatarPreview').attr('src', e.target.result);
            }
            reader.readAsDataURL(file);
        }
    });
    
    // Handle form submission
    $('#saveProfileBtn').click(function() {
        updateProfile();
    });
    
    // Password validation
    $('#newPasswordInput, #confirmPasswordInput').on('input', function() {
        validatePasswords();
    });
});

function loadUserProfile() {
    $.ajax({
        url: 'api/get_profile.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.code === 0) {
                // Update profile information
                $('#profileUsername, #displayUsername').text(response.username);
                $('#profileEmail, #displayEmail').text(response.email);
                $('#verificationStatus').text(response.verified == 1 ? 'Verified' : 'Not Verified');
                
                // Update form fields
                $('#usernameInput').val(response.username);
                $('#emailInput').val(response.email);
                
                // Update profile picture
                if (response.profilePic) {
                    $('#profileImage, #avatarPreview').attr('src', response.profilePic);
                }
            } else {
                alert('Error loading profile: ' + response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error: ' + status + ' - ' + error);
            alert('Failed to load profile information. Please try again later.');
        }
    });
}

function updateProfile() {
    // Validate passwords if new password is provided
    if ($('#newPasswordInput').val() && !validatePasswords()) {
        return;
    }
    
    // Create form data
    const formData = new FormData($('#editProfileForm')[0]);
    
    // Show loading state
    $('#saveProfileBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');
    
    // Hide previous messages
    $('#profileEditError, #profileEditSuccess').addClass('d-none');
    
    $.ajax({
        url: 'api/update_profile.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.code === 0) {
                // Show success message
                $('#profileEditSuccess').removeClass('d-none').text('Profile updated successfully!');
                
                // Reload profile data
                loadUserProfile();
                
                // Clear password fields
                $('#currentPasswordInput, #newPasswordInput, #confirmPasswordInput').val('');
                
                // Update profile picture if a new one was uploaded
                if (response.profilePic) {
                    $('#profileImage').attr('src', response.profilePic);
                }
                
                // Auto close modal after short delay
                setTimeout(function() {
                    $('#editProfileModal').modal('hide');
                    $('#profileEditSuccess').addClass('d-none');
                }, 2000);
            } else {
                // Show error message
                $('#profileEditError').removeClass('d-none').text('Error: ' + response.message);
            }
        },
        error: function(xhr, status, error) {
            // Parse error message from response if possible
            let errorMessage = 'Failed to update profile. Please try again.';
            try {
                const errorResponse = JSON.parse(xhr.responseText);
                if (errorResponse.message) {
                    errorMessage = errorResponse.message;
                }
            } catch (e) {
                console.error('Error parsing error response:', e);
            }
            
            // Show error message
            $('#profileEditError').removeClass('d-none').text('Error: ' + errorMessage);
        },
        complete: function() {
            // Reset button state
            $('#saveProfileBtn').prop('disabled', false).text('Save Changes');
        }
    });
}

function validatePasswords() {
    const newPassword = $('#newPasswordInput').val();
    const confirmPassword = $('#confirmPasswordInput').val();
    
    // If both fields have values and they don't match
    if (newPassword && confirmPassword && newPassword !== confirmPassword) {
        $('#profileEditError').removeClass('d-none').text('Passwords do not match!');
        return false;
    } else {
        $('#profileEditError').addClass('d-none');
        return true;
    }
} 