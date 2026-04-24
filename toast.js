// Toast Notification Functions
function showSuccess(message) {
    createToast('success', message);
}

function showError(message) {
    createToast('error', message);
}

function showInfo(message) {
    createToast('info', message);
}

function showWarning(message) {
    createToast('warning', message);
}

function createToast(type, message) {
    // Remove existing toast container if any
    let container = document.querySelector('.toast-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'toast-container';
        document.body.appendChild(container);
    }
    
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    
    // Add icon based on type
    let icon = '';
    switch(type) {
        case 'success':
            icon = '<i class="fas fa-check-circle"></i>';
            break;
        case 'error':
            icon = '<i class="fas fa-times-circle"></i>';
            break;
        case 'info':
            icon = '<i class="fas fa-info-circle"></i>';
            break;
        case 'warning':
            icon = '<i class="fas fa-exclamation-circle"></i>';
            break;
    }
    
    toast.innerHTML = `${icon}<span>${message}</span>`;
    container.appendChild(toast);
    
    // Remove toast after animation completes (3 seconds)
    setTimeout(() => {
        toast.remove();
    }, 3000);
}
