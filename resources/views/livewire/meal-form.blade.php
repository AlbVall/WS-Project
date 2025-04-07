<?php

use function Livewire\Volt\{state, computed};
use App\Models\Meal;
use App\Models\Recipe;

state([
    'name' => '',
    'calories' => '',
    'protein' => '',
    'carbs' => '',
    'fat' => '',
    'notes' => '',
    'recipe_id' => null,
    'meal' => null,
    'selectedDay' => '',
    'selectedMealType' => '',
]);

$recipes = computed(function() {
    return Recipe::where('user_id', auth()->id())
        ->where('meal_type', $this->selectedMealType)
        ->get();
});

$save = function() {
    $this->validate([
        'name' => 'required|min:2',
        'calories' => 'nullable|integer|min:0',
        'protein' => 'nullable|integer|min:0',
        'carbs' => 'nullable|integer|min:0',
        'fat' => 'nullable|integer|min:0',
        'recipe_id' => 'nullable|exists:recipes,id',
    ]);

    try {
        $meal = $this->meal ? Meal::find($this->meal->id) : new Meal();
        
        // Convert day name to date
        $date = now()->startOfWeek()->modify($this->selectedDay)->format('Y-m-d');
        
        $meal->fill([
            'user_id' => auth()->id(),
            'recipe_id' => $this->recipe_id,
            'name' => $this->name,
            'meal_type' => $this->selectedMealType,
            'date' => $date,
            'calories' => $this->calories,
            'protein' => $this->protein,
            'carbs' => $this->carbs,
            'fat' => $this->fat,
            'notes' => $this->notes,
        ]);

        $meal->save();

        $this->dispatch('meal-saved');
        $this->dispatch('close-modal');
    } catch (\Exception $e) {
        $this->addError('general', 'Failed to save meal: ' . $e->getMessage());
    }
};

?>

<div class="space-y-6">
    @error('general')
        <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg relative" role="alert">
            <span class="block sm:inline">{{ $message }}</span>
        </div>
    @enderror

    <div>
        <label for="name" class="block text-sm font-medium text-gray-700">Meal Name</label>
        <input type="text" wire:model="name" id="name" 
            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-colors duration-200">
        @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">Day</label>
            <div class="mt-1 block w-full rounded-lg bg-gray-50 border-gray-300 shadow-sm sm:text-sm p-2.5">
                {{ $selectedDay }}
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Meal Type</label>
            <div class="mt-1 block w-full rounded-lg bg-gray-50 border-gray-300 shadow-sm sm:text-sm p-2.5">
                {{ ucfirst($selectedMealType) }}
            </div>
        </div>
    </div>

    <div>
        <label for="recipe_id" class="block text-sm font-medium text-gray-700">Select Recipe (Optional)</label>
        <select wire:model="recipe_id" id="recipe_id" 
            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-colors duration-200">
            <option value="">Select a recipe</option>
            @foreach($this->recipes as $recipe)
                <option value="{{ $recipe->id }}">{{ $recipe->name }}</option>
            @endforeach
        </select>
        @error('recipe_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Nutritional Information</label>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="calories" class="block text-sm font-medium text-gray-600">Calories</label>
                <input type="number" wire:model="calories" id="calories" 
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-colors duration-200">
                @error('calories') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="protein" class="block text-sm font-medium text-gray-600">Protein (g)</label>
                <input type="number" wire:model="protein" id="protein" 
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-colors duration-200">
                @error('protein') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="carbs" class="block text-sm font-medium text-gray-600">Carbs (g)</label>
                <input type="number" wire:model="carbs" id="carbs" 
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-colors duration-200">
                @error('carbs') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="fat" class="block text-sm font-medium text-gray-600">Fat (g)</label>
                <input type="number" wire:model="fat" id="fat" 
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-colors duration-200">
                @error('fat') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>
    </div>

    <div>
        <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
        <textarea wire:model="notes" id="notes" rows="3" 
            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-colors duration-200"></textarea>
    </div>

    <div class="flex justify-end">
        <button wire:click="save" type="button" 
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
            {{ $meal ? 'Update Meal' : 'Save Meal' }}
        </button>
    </div>
</div> 