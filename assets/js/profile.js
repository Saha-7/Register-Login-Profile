$(document).ready(function() {
    // Check if user is logged in
    const sessionToken = localStorage.getItem('sessionToken');
    if (!sessionToken) {
        window.location.href = 'index.html';
        return;
    }
    
    // Load profile data
    loadProfile();
    
    // Handle profile update
    $('#profileForm').on('submit', function(e) {
        e.preventDefault();
        updateProfile();
    });
    
    // Handle logout
    $('#logoutBtn').on('click', function() {
        logout();
    });
});

function loadProfile() {
    const sessionToken = localStorage.getItem('sessionToken');
    
    $.ajax({
        url: 'http://localhost/guvi-internship/backend/api/profile.php',
        type: 'GET',
        headers: {
            'Authorization': sessionToken
        },
        success: function(response) {
            if (response.success) {
                const profile = response.profile;
                $('#email').val(profile.email);
                $('#age').val(profile.age);
                $('#dob').val(profile.dob);
                $('#contact').val(profile.contact);
            } else {
                showMessage(response.message, 'danger');
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            if (response && response.message === 'Session expired. Please login again.') {
                localStorage.removeItem('sessionToken');
                localStorage.removeItem('userEmail');
                window.location.href = 'index.html';
            } else {
                showMessage('Failed to load profile', 'danger');
            }
        }
    });
}

function updateProfile() {
    const sessionToken = localStorage.getItem('sessionToken');
    const age = $('#age').val().trim();
    const dob = $('#dob').val().trim();
    const contact = $('#contact').val().trim();
    
    // Validate contact number (10 digits)
    if (!/^\d{10}$/.test(contact)) {
        showMessage('Contact number must be exactly 10 digits', 'danger');
        return;
    }
    
    $.ajax({
        url: 'http://localhost/guvi-internship/backend/api/update_profile.php',
        type: 'POST',
        contentType: 'application/json',
        headers: {
            'Authorization': sessionToken
        },
        data: JSON.stringify({
            age: age,
            dob: dob,
            contact: contact
        }),
        success: function(response) {
            if (response.success) {
                showMessage(response.message, 'success');
            } else {
                showMessage(response.message, 'danger');
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            if (response && response.message === 'Session expired. Please login again.') {
                localStorage.removeItem('sessionToken');
                localStorage.removeItem('userEmail');
                window.location.href = 'index.html';
            } else if (response && response.message) {
                showMessage(response.message, 'danger');
            } else {
                showMessage('Failed to update profile', 'danger');
            }
        }
    });
}

function logout() {
    localStorage.removeItem('sessionToken');
    localStorage.removeItem('userEmail');
    window.location.href = 'index.html';
}

function showMessage(message, type) {
    const messageDiv = $('#message');
    messageDiv.removeClass('alert-success alert-danger');
    messageDiv.addClass('alert-' + type);
    messageDiv.text(message);
    messageDiv.show();
    
    // Hide message after 5 seconds
    setTimeout(function() {
        messageDiv.fadeOut();
    }, 5000);
}