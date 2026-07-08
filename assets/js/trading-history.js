/**
 * Trading History JavaScript
 */

document.addEventListener('DOMContentLoaded', function() {
    const tradeForm = document.getElementById('tradeForm');
    if (tradeForm) {
        tradeForm.addEventListener('submit', handleAddTrade);
    }
    
    // Set today's date as default
    const tradeDateInput = document.querySelector('input[name="trade_date"]');
    if (tradeDateInput) {
        tradeDateInput.value = new Date().toISOString().split('T')[0];
    }
    
    // Handle edit buttons
    document.querySelectorAll('.btn-outline-primary[data-trade-id]').forEach(btn => {
        btn.addEventListener('click', function() {
            const tradeId = this.getAttribute('data-trade-id');
            editTrade(tradeId);
        });
    });
    
    // Handle delete buttons
    document.querySelectorAll('.btn-outline-danger[data-trade-id]').forEach(btn => {
        btn.addEventListener('click', function() {
            const tradeId = this.getAttribute('data-trade-id');
            deleteTrade(tradeId);
        });
    });
});

async function handleAddTrade(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = {
        portfolio_id: formData.get('portfolio_id'),
        symbol: formData.get('symbol').toUpperCase(),
        type: formData.get('type'),
        quantity: parseFloat(formData.get('quantity')),
        price: parseFloat(formData.get('price')),
        fees: parseFloat(formData.get('fees') || 0),
        trade_date: formData.get('trade_date')
    };
    
    try {
        const response = await API.post('/api/trades.php?action=create', data);
        if (response.success) {
            showNotification('Trade added successfully!', 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showNotification(response.error || 'Failed to add trade', 'danger');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('An error occurred', 'danger');
    }
}

async function editTrade(tradeId) {
    console.log('Edit trade:', tradeId);
    // TODO: Implement edit functionality
}

async function deleteTrade(tradeId) {
    if (confirm('Are you sure you want to delete this trade?')) {
        try {
            const response = await API.delete(`/api/trades.php?action=delete&id=${tradeId}`);
            if (response.success) {
                showNotification('Trade deleted successfully!', 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showNotification(response.error || 'Failed to delete trade', 'danger');
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
