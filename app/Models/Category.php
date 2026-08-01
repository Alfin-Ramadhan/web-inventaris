<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $name
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Collection<int, Item> $items
 * @property-read int $total_quantity
 * @property-read int $available_quantity
 */
#[Fillable('name')]
final class Category extends Model
{
    /** @use HasFactory<CategoryFactory> */
    use HasFactory;

    use HasUuids;

    /**
     * @return HasMany<Item, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    /**
     * @return Attribute<int<0, max>, never>
     */
    public function totalQuantity(): Attribute
    {
        return Attribute::get(fn (): int => $this->items()->count());
    }

    /**
     * @return Attribute<int<0, max>, never>
     */
    public function availableQuantity(): Attribute
    {
        return Attribute::get(function (): int {
            return $this->items()
                ->whereDoesntHave('borrowings', fn (Builder $query) => $query->whereNull('returned_at'))
                ->count();
        });
    }
}
