// assets/js/script.js
// Enhanced version with form validation, confirm dialogs, etc.

document.addEventListener('DOMContentLoaded', function () {

    console.log("Student Course Hub scripts loaded – version 2026-03");

    // 1. Confirm delete for any link with class .delete-confirm or href*="delete"
    document.querySelectorAll('.delete-confirm, a[href*="delete"]').forEach(link => {
        link.addEventListener('click', function (e) {
            const moduleName = this.getAttribute('data-name') || 'this item';
            if (!confirm(`Are you sure you want to delete ${moduleName}?`)) {
                e.preventDefault();
            }
        });
    });

    // 2. Client-side validation for interest registration form
    const interestForm = document.querySelector('form[action="interest.php"]');
    if (interestForm) {
        interestForm.addEventListener('submit', function (e) {
            const nameInput  = interestForm.querySelector('input[name="name"]');
            const emailInput = interestForm.querySelector('input[name="email"]');

            let valid = true;
            let messages = [];

            if (!nameInput.value.trim()) {
                messages.push("Name is required.");
                valid = false;
            }

            if (!emailInput.value.trim()) {
                messages.push("Email is required.");
                valid = false;
            } else if (!emailInput.value.includes('@') || !emailInput.value.includes('.')) {
                messages.push("Please enter a valid email address.");
                valid = false;
            }

            if (!valid) {
                e.preventDefault();
                alert(messages.join("\n"));
                // Optional: highlight invalid fields
                if (!nameInput.value.trim()) nameInput.classList.add('is-invalid');
                if (!emailInput.checkValidity()) emailInput.classList.add('is-invalid');
            }
        });

        // Remove invalid class when user starts typing
        interestForm.querySelectorAll('input').forEach(input => {
            input.addEventListener('input', function () {
                this.classList.remove('is-invalid');
            });
        });
    }

    // 3. Optional: Show success toast (if you add Bootstrap toasts in PHP)
    // Example usage in PHP: <div class="toast show bg-success text-white">Saved!</div>
    const toasts = document.querySelectorAll('.toast');
    toasts.forEach(toast => {
        new bootstrap.Toast(toast).show();
    });

    // 4. Future: add more features here (e.g. live search, dynamic year/module display, etc.)
});