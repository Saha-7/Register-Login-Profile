$(document).ready(function() {
    // Check if user is already logged in
    const sessionToken = localStorage.getItem('sessionToken');
    if (sessionToken) {
        window.location.href = 'profile.html';
    }
    
    // Handle form submission
    $('#registerForm').on('submit', function(e) {
        e.preventDefault();
        
        const email = $('#email').val().trim();
        const password = $('#password').val().trim();
        const confirmPassword = $('#confirmPassword').val().trim();
        
        // Validate passwords match
        if (password !== confirmPassword) {
            showMessage('Passwords do not match', 'danger');
            return;
        }
        
        // Validate password length
        if (password.length < 6) {
            showMessage('Password must be at least 6 characters', 'danger');
            return;
        }
        
        // Send AJAX request
        $.ajax({
            url: 'http://localhost/guvi-internship/backend/api/register.php',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                email: email,
                password: password
            }),
            success: function(response) {
                if (response.success) {
                    showMessage(response.message, 'success');
                    
                    // Redirect to login after 2 seconds
                    setTimeout(function() {
                        window.location.href = 'index.html';
                    }, 2000);
                } else {
                    showMessage(response.message, 'danger');
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                if (response && response.message) {
                    showMessage(response.message, 'danger');
                } else {
                    showMessage('Registration failed. Please try again.', 'danger');
                }
            }
        });
    });
});

function showMessage(message, type) {
    const messageDiv = $('#message');
    messageDiv.removeClass('alert-success alert-danger');
    messageDiv.addClass('alert-' + type);
    messageDiv.text(message);
    messageDiv.show();
}