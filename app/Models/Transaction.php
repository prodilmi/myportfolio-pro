<?php
/**
 * Transaction Model
 * @package MyPortfolioPro\Models
 */

namespace App\Models;

class Transaction extends Model
{
    protected string $table = 'transactions';
    protected array $fillable = [
        'portfolio_id',
        'stock_id',
        'type',
        'quantity',
        'price',
        'total_amount',
        'commission',
        'net_amount',
        'currency',
        'transaction_date',
        'notes',
    ];

    public function getPortfolioTransactions(int $portfolioId): bool|array
    {
        return $this->where(['portfolio_id' => $portfolioId]);
    }

    public function getStockTransactions(int $stockId): bool|array
    {
        return $this->where(['stock_id' => $stockId]);
    }
}
