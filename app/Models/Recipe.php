<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Recipe extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'meal_type',
        'calories',
        'protein',
        'carbs',
        'fat',
        'instructions',
        'prep_time',
        'cook_time',
        'servings',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ingredients(): BelongsToMany
    {
        return $this->belongsToMany(Ingredient::class, 'recipe_ingredient')
            ->withPivot('amount', 'unit')
            ->withTimestamps();
    }

    public function meals(): BelongsToMany
    {
        return $this->belongsToMany(Meal::class);
    }
}
