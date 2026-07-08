<?php
/**
 * Stock Model
 * @package MyPortfolioPro\Models
 */

namespace App\Models;

class Stock extends Model
{
    protected string $table = 'stocks';
    protected array $fillable = [
        'ticker',
        'company_name',
        'exchange',
        'sector',
        'country',
        'currency',
        'last_price',
        'last_price_updated',
    ];

    public function getByTicker(string $ticker): bool|array
    {
        return $this->findBy('ticker', strtoupper($ticker));
    }

    public function getBySector(string $sector): bool|array
    {
        return $this->where(['sector' => $sector]);
    }
}
