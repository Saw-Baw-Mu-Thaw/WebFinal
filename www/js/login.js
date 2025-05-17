$(document).ready(function () {
    hideError();

    // Clear error when user starts typing
    $('#username, #password').on('input', function () {
        hideError();
    });

    $('#loginForm').on("submit", function (e) {
        e.preventDefault();
        
        // Basic validation
        const username = $('#username').val().trim();
        const password = $('#password').val().trim();
        
        if (!username) {
            showError('Please enter your username');
            return;
        }
        
        if (!password) {
            showError('Please enter your password');
            return;
        }

        // Show loading state
        const submitBtn = $(this).find('button[type="submit"]');
        const originalBtnText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Logging in...');

        $.ajax({
            url: "api/login_service.php",
            type: "POST",
            dataType: "json",
            contentType: 'application/json',
            data: JSON.stringify({ username: username, password: password })
        }).done(function (json) {
            if (json['code'] == 0) {
                window.location.replace("index.php");
            } else {
                $('#username').val(username);
                $('#password').val('');
                showError(json['message']);
                // Reset button state
                submitBtn.prop('disabled', false).html(originalBtnText);
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {
            showError('An error occurred. Please try again.');
            // Reset button state
            submitBtn.prop('disabled', false).html(originalBtnText);
        });
    });
});

function showError(message) {
    const errorDiv = $('#errorDiv');
    errorDiv.removeClass('d-none').addClass('show alert-danger');
    errorDiv.html(`<i class="fas fa-exclamation-circle mr-2"></i>${message}`);
    
    // Scroll to error message if it's not visible
    if (!isElementInViewport(errorDiv[0])) {
        errorDiv[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
}

function hideError() {
    const errorDiv = $('#errorDiv');
    errorDiv.removeClass('show alert-danger').addClass('d-none');
    errorDiv.html('');
}

// Helper function to check if element is in viewport
function isElementInViewport(el) {
    const rect = el.getBoundingClientRect();
    return (
        rect.top >= 0 &&
        rect.left >= 0 &&
        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
        rect.right <= (window.innerWidth || document.documentElement.clientWidth)
    );
}