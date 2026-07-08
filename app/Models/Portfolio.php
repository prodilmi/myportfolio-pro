<?php
/**
 * Portfolio Model
 * @package MyPortfolioPro\Models
 */

namespace App\Models;

class Portfolio extends Model
{
    protected string $table = 'portfolios';
    protected array $fillable = [
        'user_id',
        'broker_id',
        'name',
        'description',
        'currency',
        'is_active',
        'total_invested',
        'current_value',
        'cash_balance',
    ];

    public function getUserPortfolios(int $userId): bool|array
    {
        return $this->where(['user_id' => $userId]);
    }

    public function getWithTransactions(int $portfolioId): bool|array
    {
        $portfolio = $this->find($portfolioId);
        if (!$portfolio) {
            return false;
        }
        $portfolio['transactions'] = $this->db->findAll('transactions', ['portfolio_id' => $portfolioId]);
        return $portfolio;
    }

    public function calculateTotalValue(int $portfolioId): float
    {
        $result = $this->db->query(
            'SELECT SUM(t.quantity * p.close) as total FROM transactions t 
             LEFT JOIN prices p ON t.stock_id = p.stock_id 
             WHERE t.portfolio_id = ? AND t.type = "buy"',
            [$portfolioId]
        );
        return (float)($result[0]['total'] ?? 0);
    }
}
