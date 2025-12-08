$(document).ready(function() {
    // Check if user is already logged in
    const sessionToken = localStorage.getItem('sessionToken');
    if (sessionToken) {
        window.location.href = 'profile.html';
    }
    
    // Handle form submission
    $('#loginForm').on('submit', function(e) {
        e.preventDefault();
        
        const email = $('#email').val().trim();
        const password = $('#password').val().trim();
        
        // Send AJAX request
        $.ajax({
            url: '/backend/api/login.php',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                email: email,
                password: password
            }),
            success: function(response) {
                if (response.success) {
                    // Store session token in localStorage
                    localStorage.setItem('sessionToken', response.sessionToken);
                    localStorage.setItem('userEmail', response.email);
                    
                    showMessage('Login successful! Redirecting...', 'success');
                    
                    // Redirect to profile page
                    setTimeout(function() {
                        window.location.href = 'profile.html';
                    }, 1500);
                } else {
                    showMessage(response.message, 'danger');
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                if (response && response.message) {
                    showMessage(response.message, 'danger');
                } else {
                    showMessage('Login failed. Please try again.', 'danger');
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