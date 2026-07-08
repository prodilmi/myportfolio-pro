/**
 * Broker Integration JavaScript
 */

document.addEventListener('DOMContentLoaded', function() {
    const brokerForm = document.getElementById('brokerForm');
    if (brokerForm) {
        brokerForm.addEventListener('submit', handleAddBroker);
    }
    
    // Handle sync buttons
    document.querySelectorAll('.btn-primary[data-broker-id]').forEach(btn => {
        btn.addEventListener('click', function() {
            const brokerId = this.getAttribute('data-broker-id');
            syncBroker(brokerId);
        });
    });
    
    // Handle disconnect buttons
    document.querySelectorAll('.btn-outline-danger[data-broker-id]').forEach(btn => {
        btn.addEventListener('click', function() {
            const brokerId = this.getAttribute('data-broker-id');
            disconnectBroker(brokerId);
        });
    });
});

async function handleAddBroker(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = {
        broker_name: formData.get('broker_name'),
        account_number: formData.get('account_number'),
        api_key: formData.get('api_key'),
        api_secret: formData.get('api_secret')
    };
    
    // Validate form
    if (!data.broker_name || !data.account_number || !data.api_key || !data.api_secret) {
        showNotification('Please fill in all fields', 'warning');
        return;
    }
    
    try {
        const response = await API.post('/api/brokers.php?action=connect', data);
        if (response.success) {
            showNotification('Broker connected successfully!', 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showNotification(response.error || 'Failed to connect broker', 'danger');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('An error occurred', 'danger');
    }
}

async function syncBroker(brokerId) {
    const btn = event.target;
    btn.disabled = true;
    btn.textContent = 'Syncing...';
    
    try {
        const response = await API.post(`/api/brokers.php?action=sync&broker_id=${brokerId}`, {});
        if (response.success) {
            showNotification(response.message || 'Sync completed!', 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showNotification(response.error || 'Failed to sync broker', 'danger');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('An error occurred', 'danger');
    } finally {
        btn.disabled = false;
        btn.textContent = 'Sync Now';
    }
}

async function disconnectBroker(brokerId) {
    if (confirm('Are you sure you want to disconnect this broker? You can reconnect anytime.')) {
        try {
            const response = await API.delete(`/api/brokers.php?action=disconnect&broker_id=${brokerId}`);
            if (response.success) {
                showNotification(response.message || 'Broker disconnected!', 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showNotification(response.error || 'Failed to disconnect broker', 'danger');
            }
        } catch (error) {
            console.error('Error:', error);
            showNotification('An error occurred', 'danger');
        }
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
