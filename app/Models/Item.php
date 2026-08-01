<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ItemFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string|null $category_id
 * @property string $name
 * @property string|null $inventory_number
 * @property int $quantity
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Category|null $category
 * @property-read Collection<int, Borrowing> $borrowings
 * @property-read int $available_quantity
 */
#[Fillable('category_id', 'name', 'quantity', 'inventory_number')]
final class Item extends Model
{
    /** @use HasFactory<ItemFactory> */
    use HasFactory;

    use HasUuids;

    /**
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return HasMany<Borrowing, $this>
     */
    public function borrowings(): HasMany
    {
        return $this->hasMany(Borrowing::class);
    }

    /**
     * @return Attribute<int<0, max>, never>
     */
    public function availableQuantity(): Attribute
    {
        return Attribute::get(function (): int {
            $activeBorrowingsCount = $this->borrowings()
                ->whereNull('returned_at')
                ->count();

            return (int) max(0, $this->quantity - $activeBorrowingsCount);
        });
    }

    public function getRouteKeyName(): string
    {
        return 'inventory_number';
    }
}
