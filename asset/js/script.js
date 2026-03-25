// Student Course Hub - Main JavaScript

document.addEventListener('DOMContentLoaded', function() {
    
    
    // DELETE CONFIRMATION
   
    const deleteButtons = document.querySelectorAll('.icon-delete, .btn-delete, .delete-link, .btn-danger, a[onclick*="confirm"]');
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            let message = 'Are you sure you want to delete this item? This action cannot be undone.';
            if (this.getAttribute('onclick')) {
                // If there's already an onclick, let it handle
                return;
            }
            if (!confirm(message)) {
                e.preventDefault();
                e.stopPropagation();
            }
        });
    });
    
    
    // AUTO-HIDE MESSAGES
    
    const messages = document.querySelectorAll('.success-message, .error-messages, .success-box, .error-box, .alert-success, .alert-danger');
    messages.forEach(message => {
        setTimeout(() => {
            message.style.transition = 'opacity 0.5s ease';
            message.style.opacity = '0';
            setTimeout(() => {
                if (message.parentNode) {
                    message.style.display = 'none';
                }
            }, 500);
        }, 5000);
    });
    
    
    // EMAIL VALIDATION
    
    const emailInputs = document.querySelectorAll('input[type="email"]');
    emailInputs.forEach(input => {
        // Remove any existing error on input
        input.addEventListener('input', function() {
            this.style.borderColor = '#e2e8f0';
            const errorMsg = this.parentNode.querySelector('.field-error');
            if (errorMsg) {
                errorMsg.remove();
            }
        });
        
        input.addEventListener('blur', function() {
            const email = this.value.trim();
            const emailPattern = /^[^\s@]+@([^\s@]+\.)+[^\s@]+$/;
            
            if (email && !emailPattern.test(email)) {
                this.style.borderColor = '#ef4444';
                
                // Check if error message already exists
                let errorMsg = this.parentNode.querySelector('.field-error');
                if (!errorMsg) {
                    errorMsg = document.createElement('small');
                    errorMsg.className = 'field-error';
                    errorMsg.style.color = '#ef4444';
                    errorMsg.style.fontSize = '12px';
                    errorMsg.style.display = 'block';
                    errorMsg.style.marginTop = '5px';
                    errorMsg.textContent = 'Please enter a valid email address';
                    this.parentNode.appendChild(errorMsg);
                }
            } else {
                this.style.borderColor = '#e2e8f0';
                const errorMsg = this.parentNode.querySelector('.field-error');
                if (errorMsg) {
                    errorMsg.remove();
                }
            }
        });
    });
    
    
    // IMAGE PREVIEW
  
    const fileInputs = document.querySelectorAll('input[type="file"]');
    fileInputs.forEach(input => {
        input.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Find or create preview container
                    let preview = input.parentNode.querySelector('.image-preview');
                    if (!preview) {
                        preview = document.createElement('div');
                        preview.className = 'image-preview';
                        preview.style.marginTop = '10px';
                        input.parentNode.appendChild(preview);
                    }
                    preview.innerHTML = `<img src="${e.target.result}" style="max-height: 100px; border-radius: 8px; border: 1px solid #e2e8f0; padding: 4px;">`;
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    });
    
   
    // AUTO-SUBMIT STATUS SELECTS
    
    const statusSelects = document.querySelectorAll('.status-select');
    statusSelects.forEach(select => {
        select.addEventListener('change', function() {
            const form = this.closest('form');
            if (form) {
                form.submit();
            }
        });
    });
    
   
   
    
    // Add search input for tables if needed
    function initTableSearch() {
        const searchInputs = document.querySelectorAll('.table-search');
        searchInputs.forEach(input => {
            input.addEventListener('keyup', function() {
                const filter = this.value.toLowerCase();
                const table = document.querySelector(this.dataset.table || '.data-table');
                if (!table) return;
                
                const rows = table.querySelectorAll('tbody tr');
                rows.forEach(row => {
                    let textContent = '';
                    const cells = row.querySelectorAll('td');
                    cells.forEach(cell => {
                        textContent += cell.textContent.toLowerCase();
                    });
                    
                    if (textContent.indexOf(filter) > -1) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        });
    }
    initTableSearch();
    
    
    const toggleButtons = document.querySelectorAll('.toggle-status');
    toggleButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            if (!confirm('Change programme visibility?')) {
                e.preventDefault();
            }
        });
    });
    
   
    // FORM VALIDATION FOR REQUIRED FIELDS
    
    const forms = document.querySelectorAll('form[data-validate]');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.style.borderColor = '#ef4444';
                    isValid = false;
                    
                    // Add error message
                    let errorMsg = field.parentNode.querySelector('.required-error');
                    if (!errorMsg) {
                        errorMsg = document.createElement('small');
                        errorMsg.className = 'required-error';
                        errorMsg.style.color = '#ef4444';
                        errorMsg.style.fontSize = '12px';
                        errorMsg.style.display = 'block';
                        errorMsg.style.marginTop = '5px';
                        errorMsg.textContent = 'This field is required';
                        field.parentNode.appendChild(errorMsg);
                    }
                } else {
                    field.style.borderColor = '#e2e8f0';
                    const errorMsg = field.parentNode.querySelector('.required-error');
                    if (errorMsg) {
                        errorMsg.remove();
                    }
                }
            });
            
            if (!isValid) {
                e.preventDefault();
            }
        });
        
        // Remove error on input
        form.querySelectorAll('[required]').forEach(field => {
            field.addEventListener('input', function() {
                this.style.borderColor = '#e2e8f0';
                const errorMsg = this.parentNode.querySelector('.required-error');
                if (errorMsg) {
                    errorMsg.remove();
                }
            });
        });
    });
    
    
    // CONFIRMATION FOR LINK ACTIONS
    // ============================================
    const confirmLinks = document.querySelectorAll('.confirm-action');
    confirmLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const message = this.getAttribute('data-confirm') || 'Are you sure?';
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });
    
    console.log('Student Course Hub JS loaded successfully');
});