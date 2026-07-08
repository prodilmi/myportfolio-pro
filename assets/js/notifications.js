/**
 * Notifications JavaScript
 */

document.addEventListener('DOMContentLoaded', function() {
    const settingsForm = document.getElementById('notificationSettings');
    if (settingsForm) {
        settingsForm.addEventListener('submit', handleSaveSettings);
    }
    
    const markAllReadBtn = document.getElementById('markAllRead');
    if (markAllReadBtn) {
        markAllReadBtn.addEventListener('click', markAllAsRead);
    }
    
    // Load notifications periodically
    loadNotifications();
    setInterval(loadNotifications, 30000); // Refresh every 30 seconds
});

async function loadNotifications() {
    try {
        const response = await API.get('/api/notifications.php?action=list&limit=50');
        if (response.success) {
            updateNotificationUI(response.data);
        }
    } catch (error) {
        console.error('Error loading notifications:', error);
    }
}

function updateNotificationUI(notifications) {
    const unreadCount = notifications.filter(n => !n.read).length;
    
    // Update unread badge if exists
    const badge = document.querySelector('[id="notificationBadge"]');
    if (badge) {
        badge.textContent = unreadCount;
        badge.style.display = unreadCount > 0 ? 'block' : 'none';
    }
}

async function markAllAsRead() {
    try {
        const response = await API.post('/api/notifications.php?action=mark_all_read', {});
        if (response.success) {
            showNotification('All notifications marked as read', 'success');
            document.querySelectorAll('.notification-item.bg-light').forEach(item => {
                item.classList.remove('bg-light');
                const badge = item.querySelector('.badge');
                if (badge) badge.remove();
            });
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

async function deleteNotification(notificationId) {
    try {
        const response = await API.delete(`/api/notifications.php?action=delete&notification_id=${notificationId}`);
        if (response.success) {
            showNotification('Notification deleted', 'info');
            const item = document.querySelector(`[data-notification-id="${notificationId}"]`);
            if (item) {
                item.remove();
            }
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

async function handleSaveSettings(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const settings = {
        price_alert: formData.get('price_alert') === 'on',
        performance_alert: formData.get('performance_alert') === 'on',
        portfolio_alert: formData.get('portfolio_alert') === 'on',
        news_alert: formData.get('news_alert') === 'on',
        email_frequency: formData.get('email_frequency')
    };
    
    try {
        // Save to localStorage for now
        localStorage.setItem('notificationSettings', JSON.stringify(settings));
        showNotification('Settings saved successfully!', 'success');
    } catch (error) {
        console.error('Error:', error);
        showNotification('Failed to save settings', 'danger');
    }
}

function showNotification(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    const container = document.querySelector('.container-fluid');
    container.insertBefore(alertDiv, container.firstChild);
    
    setTimeout(() => alertDiv.remove(), 3000);
}
