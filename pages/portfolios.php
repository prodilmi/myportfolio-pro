<?php
/**
 * Portfolios List Page
 */

use App\Core\Auth;

$auth = new Auth($db);
$user = $auth->getUser();

if (!$user) {
    header('Location: ' . BASE_URL . 'index.php?page=login');
    exit;
}

$portfolios = $db->findAll('portfolios', ['user_id' => $user['id']], ['order_by' => 'created_at DESC']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portfolios - MyPortfolioPro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo ASSETS_URL; ?>css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'components/navbar.php'; ?>
    
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1>My Portfolios</h1>
                        <p class="text-muted">Manage and track all your investment portfolios</p>
                    </div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newPortfolioModal">+ New Portfolio</button>
                </div>
            </div>
        </div>
        
        <div class="row">
            <?php if (empty($portfolios)): ?>
                <div class="col-12">
                    <div class="alert alert-info">
                        <p>You don't have any portfolios yet. <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#newPortfolioModal">Create one now</button></p>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($portfolios as $portfolio): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <h5 class="card-title"><?php echo htmlspecialchars($portfolio['name']); ?></h5>
                                    <span class="badge bg-success">Active</span>
                                </div>
                                <p class="card-text text-muted small"><?php echo htmlspecialchars($portfolio['description'] ?? 'No description'); ?></p>
                                <div class="mt-4 mb-4">
                                    <p class="mb-2"><strong>Invested:</strong> $<?php echo number_format($portfolio['total_invested'], 2); ?></p>
                                    <p class="mb-2"><strong>Current Value:</strong> $<?php echo number_format($portfolio['current_value'], 2); ?></p>
                                    <?php 
                                    $gain = $portfolio['current_value'] - $portfolio['total_invested'];
                                    $class = $gain >= 0 ? 'text-success' : 'text-danger';
                                    ?>
                                    <p class="mb-0 <?php echo $class; ?>"><strong>Gain/Loss:</strong> $<?php echo number_format($gain, 2); ?></p>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent border-top">
                                <a href="<?php echo BASE_URL; ?>index.php?page=portfolio-detail&id=<?php echo $portfolio['id']; ?>" class="btn btn-sm btn-primary">View Details</a>
                                <button class="btn btn-sm btn-outline-danger" onclick="deletePortfolio(<?php echo $portfolio['id']; ?>)">Delete</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- New Portfolio Modal -->
    <div class="modal fade" id="newPortfolioModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create New Portfolio</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="newPortfolioForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Portfolio Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Create Portfolio</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <?php include 'components/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.getElementById('newPortfolioForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(this);
        const data = {
            name: formData.get('name'),
            description: formData.get('description'),
            total_invested: 0,
            current_value: 0
        };
        
        try {
            const response = await fetch('/api/portfolios.php?action=create', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(data)
            });
            const result = await response.json();
            if (result.success) {
                location.reload();
            }
        } catch (error) {
            console.error('Error:', error);
        }
    });
    
    async function deletePortfolio(id) {
        if (confirm('Are you sure?')) {
            try {
                const response = await fetch(`/api/portfolios.php?action=delete&id=${id}`, {method: 'DELETE'});
                const result = await response.json();
                if (result.success) {
                    location.reload();
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }
    }
    </script>
</body>
</html>
