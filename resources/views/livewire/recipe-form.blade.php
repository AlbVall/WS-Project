<?php

use function Livewire\Volt\{state, computed};
use App\Models\Recipe;
use App\Models\Ingredient;

state([
    'name' => '',
    'description' => '',
    'meal_type' => 'breakfast',
    'calories' => '',
    'protein' => '',
    'carbs' => '',
    'fat' => '',
    'instructions' => '',
    'prep_time' => '',
    'cook_time' => '',
    'servings' => 1,
    'ingredients' => [],
    'newIngredient' => [
        'name' => '',
        'amount' => '',
        'unit' => '',
    ],
    'recipe' => null,
    'mealTypes' => [
        'breakfast' => 'Breakfast',
        'lunch' => 'Lunch',
        'dinner' => 'Dinner',
        'snack' => 'Snack',
    ],
    'units' => [
        'g' => 'Grams',
        'kg' => 'Kilograms',
        'ml' => 'Milliliters',
        'l' => 'Liters',
        'cup' => 'Cups',
        'tbsp' => 'Tablespoons',
        'tsp' => 'Teaspoons',
        'oz' => 'Ounces',
        'lb' => 'Pounds',
        'unit' => 'Units',
    ],
]);

$mealTypes = [
    'breakfast' => 'Breakfast',
    'lunch' => 'Lunch',
    'dinner' => 'Dinner',
    'snack' => 'Snack',
];

$units = [
    'g' => 'Grams',
    'kg' => 'Kilograms',
    'ml' => 'Milliliters',
    'l' => 'Liters',
    'cup' => 'Cups',
    'tbsp' => 'Tablespoons',
    'tsp' => 'Teaspoons',
    'oz' => 'Ounces',
    'lb' => 'Pounds',
    'piece' => 'Piece',
];

$addIngredient = function() {
    if (!empty($this->newIngredient['name']) && !empty($this->newIngredient['amount']) && !empty($this->newIngredient['unit'])) {
        $this->ingredients[] = [
            'name' => $this->newIngredient['name'],
            'amount' => $this->newIngredient['amount'],
            'unit' => $this->newIngredient['unit'],
        ];
        $this->newIngredient = [
            'name' => '',
            'amount' => '',
            'unit' => '',
        ];
    }
};

$removeIngredient = function($index) {
    unset($this->ingredients[$index]);
    $this->ingredients = array_values($this->ingredients);
};

$save = function() {
    $this->validate([
        'name' => 'required|min:2',
        'meal_type' => 'required|in:breakfast,lunch,dinner,snack',
        'calories' => 'required|integer|min:0',
        'protein' => 'nullable|integer|min:0',
        'carbs' => 'nullable|integer|min:0',
        'fat' => 'nullable|integer|min:0',
        'prep_time' => 'nullable|integer|min:0',
        'cook_time' => 'nullable|integer|min:0',
        'servings' => 'required|integer|min:1',
        'ingredients' => 'required|array|min:1',
        'ingredients.*.name' => 'required|string',
        'ingredients.*.amount' => 'required|numeric|min:0',
        'ingredients.*.unit' => 'required|string',
    ]);

    try {
        $recipe = $this->recipe ? Recipe::find($this->recipe->id) : new Recipe();
        
        $recipe->fill([
            'user_id' => auth()->id(),
            'name' => $this->name,
            'description' => $this->description,
            'meal_type' => $this->meal_type,
            'calories' => $this->calories,
            'protein' => $this->protein,
            'carbs' => $this->carbs,
            'fat' => $this->fat,
            'instructions' => $this->instructions,
            'prep_time' => $this->prep_time,
            'cook_time' => $this->cook_time,
            'servings' => $this->servings,
        ]);

        $recipe->save();

        // Handle ingredients
        $recipe->ingredients()->detach();
        foreach ($this->ingredients as $ingredient) {
            $ingredientModel = Ingredient::firstOrCreate(['name' => $ingredient['name']]);
            $recipe->ingredients()->attach($ingredientModel->id, [
                'amount' => $ingredient['amount'],
                'unit' => $ingredient['unit'],
            ]);
        }

        $this->dispatch('recipe-saved');
        $this->dispatch('close-modal');
    } catch (\Exception $e) {
        $this->addError('general', 'Failed to save recipe: ' . $e->getMessage());
    }
};

?>

<div class="space-y-6">
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700">Recipe Name</label>
        <input type="text" wire:model="name" id="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
        @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <div>
        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
        <textarea wire:model="description" id="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
    </div>

    <div>
        <label for="meal_type" class="block text-sm font-medium text-gray-700">Meal Type</label>
        <select wire:model="meal_type" id="meal_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            @foreach($mealTypes as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </select>
        @error('meal_type') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label for="calories" class="block text-sm font-medium text-gray-700">Calories</label>
            <input type="number" wire:model="calories" id="calories" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            @error('calories') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="protein" class="block text-sm font-medium text-gray-700">Protein (g)</label>
            <input type="number" wire:model="protein" id="protein" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            @error('protein') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="carbs" class="block text-sm font-medium text-gray-700">Carbs (g)</label>
            <input type="number" wire:model="carbs" id="carbs" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            @error('carbs') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="fat" class="block text-sm font-medium text-gray-700">Fat (g)</label>
            <input type="number" wire:model="fat" id="fat" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            @error('fat') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label for="prep_time" class="block text-sm font-medium text-gray-700">Prep Time (minutes)</label>
            <input type="number" wire:model="prep_time" id="prep_time" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            @error('prep_time') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="cook_time" class="block text-sm font-medium text-gray-700">Cook Time (minutes)</label>
            <input type="number" wire:model="cook_time" id="cook_time" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            @error('cook_time') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="servings" class="block text-sm font-medium text-gray-700">Servings</label>
            <input type="number" wire:model="servings" id="servings" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            @error('servings') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
    </div>

    <div>
        <label for="instructions" class="block text-sm font-medium text-gray-700">Instructions</label>
        <textarea wire:model="instructions" id="instructions" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
    </div>

    <div>
        <h3 class="text-lg font-medium text-gray-900 mb-4">Ingredients</h3>
        
        <!-- Add Ingredient Form -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
                <label for="ingredient_name" class="block text-sm font-medium text-gray-700">Ingredient Name</label>
                <input type="text" wire:model="newIngredient.name" id="ingredient_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>
            <div>
                <label for="ingredient_amount" class="block text-sm font-medium text-gray-700">Amount</label>
                <input type="number" wire:model="newIngredient.amount" id="ingredient_amount" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>
            <div>
                <label for="ingredient_unit" class="block text-sm font-medium text-gray-700">Unit</label>
                <select wire:model="newIngredient.unit" id="ingredient_unit" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">Select a unit</option>
                    @foreach($units as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <button wire:click="addIngredient" type="button" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Add Ingredient
        </button>

        <!-- Ingredients List -->
        <div class="mt-4 space-y-2">
            @foreach($ingredients as $index => $ingredient)
                <div class="flex items-center space-x-4 p-2 bg-gray-50 rounded-md">
                    <span class="flex-1">{{ $ingredient['name'] }}</span>
                    <span class="text-gray-500">{{ $ingredient['amount'] }} {{ $ingredient['unit'] }}</span>
                    <button wire:click="removeIngredient({{ $index }})" type="button" class="text-red-600 hover:text-red-800">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            @endforeach
        </div>
        @error('ingredients') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <div class="flex justify-end">
        <button wire:click="save" type="button" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            {{ $recipe ? 'Update Recipe' : 'Save Recipe' }}
        </button>
    </div>
</div> 