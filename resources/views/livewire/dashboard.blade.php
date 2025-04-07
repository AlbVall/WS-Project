<?php

use function Livewire\Volt\{state, computed};
use App\Models\Recipe;
use App\Models\Meal;

state([
    'user' => null,
    'selectedWeek' => null,
    'activeTab' => 'weekly-plan',
    'showAddMealModal' => false,
    'showAddRecipeModal' => false,
    'selectedDay' => null,
    'selectedMealType' => null,
    'editingRecipe' => null,
    'editingMeal' => null,
]);

$recipes = computed(function() {
    return Recipe::where('user_id', auth()->id())->get();
});

$meals = computed(function() {
    $startOfWeek = now()->startOfWeek();
    $endOfWeek = now()->endOfWeek();
    
    return Meal::where('user_id', auth()->id())
        ->whereBetween('date', [$startOfWeek, $endOfWeek])
        ->get();
});

$logout = function() {
    auth()->logout();
    return redirect('/login');
};

$setActiveTab = function($tab) {
    $this->activeTab = $tab;
};

$openAddMealModal = function($day, $mealType) {
    $this->selectedDay = $day;
    $this->selectedMealType = $mealType;
    $this->editingMeal = null;
    $this->showAddMealModal = true;
};

$openAddRecipeModal = function() {
    $this->editingRecipe = null;
    $this->showAddRecipeModal = true;
};

$editRecipe = function($recipeId) {
    $this->editingRecipe = Recipe::find($recipeId);
    $this->showAddRecipeModal = true;
};

$deleteRecipe = function($recipeId) {
    $recipe = Recipe::find($recipeId);
    if ($recipe && $recipe->user_id === auth()->id()) {
        $recipe->delete();
    }
};

$editMeal = function($mealId) {
    $this->editingMeal = Meal::find($mealId);
    $this->selectedDay = $this->editingMeal->date->format('Y-m-d');
    $this->selectedMealType = $this->editingMeal->meal_type;
    $this->showAddMealModal = true;
};

$deleteMeal = function($mealId) {
    $meal = Meal::find($mealId);
    if ($meal && $meal->user_id === auth()->id()) {
        $meal->delete();
    }
};

$closeModal = function() {
    $this->showAddMealModal = false;
    $this->showAddRecipeModal = false;
    $this->editingRecipe = null;
    $this->editingMeal = null;
};

?>

<div class="min-h-screen bg-[#FDFEFE] relative overflow-hidden" x-data x-on:recipe-saved.window="$wire.closeModal()" x-on:close-modal.window="$wire.closeModal()">
    <!-- Decorative background elements -->
    <div class="absolute inset-0 bg-gradient-to-br from-[#A9DBB8]/5 via-[#2F6D22]/5 to-[#A9DBB8]/5"></div>
    <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=\"20\" height=\"20\" viewBox=\"0 0 20 20\" fill=\"none\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Ccircle cx=\"10\" cy=\"10\" r=\"1\" fill=\"%232F6D22\" fill-opacity=\"0.05\"/%3E%3C/svg%3E')] bg-repeat opacity-50"></div>
    <div class="absolute right-0 top-0 h-[500px] w-[500px] bg-gradient-to-br from-[#2F6D22]/20 to-[#A9DBB8]/20 rounded-full blur-3xl transform translate-x-1/2 -translate-y-1/2"></div>
    <div class="absolute left-0 bottom-0 h-[500px] w-[500px] bg-gradient-to-br from-[#A9DBB8]/20 to-[#2F6D22]/20 rounded-full blur-3xl transform -translate-x-1/2 translate-y-1/2"></div>

    <!-- Navigation Bar -->
    <nav class="bg-white/90 backdrop-blur-md shadow-lg border-b border-[#2F6D22]/10 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20">
                <!-- Logo and Brand -->
                <div class="flex items-center space-x-6">
                    <div class="flex items-center space-x-3">
                        <div class="relative">
                            <div class="absolute -inset-0.5 bg-gradient-to-r from-[#A9DBB8] to-[#2F6D22] rounded-xl blur opacity-60"></div>
                            <div class="relative h-12 w-12 bg-gradient-to-br from-[#A9DBB8] via-[#2F6D22] to-[#A9DBB8] rounded-xl flex items-center justify-center">
                                <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-[#A9DBB8] to-[#2F6D22]">Meal Planner</h1>
                            <p class="text-sm text-[#2F6D22]/60">Plan your meals with ease</p>
                        </div>
                    </div>
                </div>

                <!-- Right Navigation -->
                <div class="flex items-center space-x-6">
                    <!-- User Profile -->
                    <div class="flex items-center space-x-4 bg-white/50 rounded-2xl px-4 py-2.5 border border-[#2F6D22]/10 shadow-sm">
                        <div class="relative">
                            <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-[#A9DBB8]/10 to-[#2F6D22]/10 flex items-center justify-center">
                                <svg class="h-6 w-6 text-[#A9DBB8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <div class="absolute bottom-0 right-0 h-3 w-3 rounded-full bg-[#A9DBB8] border-2 border-white"></div>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-sm font-medium text-[#252422]">{{ auth()->user()->name }}</span>
                            <span class="text-xs text-[#2F6D22]/60">Premium Member</span>
                        </div>
                    </div>

                    <!-- Logout Button -->
                    <button wire:click="logout" class="relative">
                        <div class="absolute -inset-0.5 bg-gradient-to-r from-[#A9DBB8] to-[#2F6D22] rounded-xl blur opacity-60"></div>
                        <div class="relative px-6 py-2.5 bg-white rounded-xl border border-[#2F6D22]/10 text-sm font-medium text-[#252422]">
                            <div class="flex items-center space-x-2">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                <span>Logout</span>
                            </div>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 relative">
        <!-- Tabs -->
        <div class="bg-white/90 backdrop-blur-xl rounded-2xl shadow-lg border border-[#2F6D22]/10 p-2 mb-8 sticky top-24 z-40">
            <nav class="flex justify-between items-center">
                <div class="flex space-x-1">
                    <button wire:click="setActiveTab('weekly-plan')" 
                        class="relative px-6 py-3 rounded-xl font-medium text-sm {{ $activeTab === 'weekly-plan' ? 'text-white' : 'text-[#2F6D22]' }}">
                        @if($activeTab === 'weekly-plan')
                            <div class="absolute inset-0 bg-gradient-to-r from-[#A9DBB8] to-[#2F6D22] rounded-xl"></div>
                        @endif
                        <div class="relative flex items-center space-x-2">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span>Weekly Plan</span>
                        </div>
                    </button>

                    <button wire:click="setActiveTab('recipes')" 
                        class="relative px-6 py-3 rounded-xl font-medium text-sm {{ $activeTab === 'recipes' ? 'text-white' : 'text-[#2F6D22]' }}">
                        @if($activeTab === 'recipes')
                            <div class="absolute inset-0 bg-gradient-to-r from-[#A9DBB8] to-[#2F6D22] rounded-xl"></div>
                        @endif
                        <div class="relative flex items-center space-x-2">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            <span>Recipes</span>
                        </div>
                    </button>

                    <button wire:click="setActiveTab('shopping-list')" 
                        class="relative px-6 py-3 rounded-xl font-medium text-sm {{ $activeTab === 'shopping-list' ? 'text-white' : 'text-[#2F6D22]' }}">
                        @if($activeTab === 'shopping-list')
                            <div class="absolute inset-0 bg-gradient-to-r from-[#A9DBB8] to-[#2F6D22] rounded-xl"></div>
                        @endif
                        <div class="relative flex items-center space-x-2">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                            <span>Shopping List</span>
                        </div>
                    </button>

                    <button wire:click="setActiveTab('nutrition')" 
                        class="relative px-6 py-3 rounded-xl font-medium text-sm {{ $activeTab === 'nutrition' ? 'text-white' : 'text-[#2F6D22]' }}">
                        @if($activeTab === 'nutrition')
                            <div class="absolute inset-0 bg-gradient-to-r from-[#A9DBB8] to-[#2F6D22] rounded-xl"></div>
                        @endif
                        <div class="relative flex items-center space-x-2">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            <span>Nutrition</span>
                        </div>
                    </button>
                </div>
            </nav>
        </div>

        <!-- Content Area -->
        <div class="space-y-8">
            <!-- Weekly Plan Tab -->
            @if($activeTab === 'weekly-plan')
                <div class="bg-white/90 backdrop-blur-xl rounded-2xl shadow-lg border border-[#2F6D22]/10 overflow-hidden">
                    <div class="p-6">
                        <div class="grid grid-cols-7 gap-6">
                            @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                                <div class="bg-white rounded-xl shadow-sm border border-[#2F6D22]/10">
                                    <div class="p-4">
                                        <h3 class="text-lg font-bold text-[#252422] mb-4 pb-2 border-b border-[#2F6D22]/10">{{ $day }}</h3>
                                        
                                        @foreach(['Breakfast', 'Lunch', 'Dinner', 'Snacks'] as $mealType)
                                            <div class="mb-4 last:mb-0">
                                                <div class="flex items-center justify-between mb-2">
                                                    <span class="text-sm font-medium text-[#2F6D22]">{{ $mealType }}</span>
                                                    <button wire:click="openAddMealModal('{{ $day }}', '{{ strtolower($mealType) }}')"
                                                        class="relative">
                                                        <div class="relative h-8 w-8 rounded-full flex items-center justify-center bg-[#A9DBB8]/10 text-[#A9DBB8]">
                                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                            </svg>
                                                        </div>
                                                    </button>
                                                </div>

                                                <div class="space-y-2">
                                                    @foreach($this->meals->filter(function($meal) use ($day, $mealType) {
                                                        return $meal->date->format('l') === $day && $meal->meal_type === strtolower($mealType);
                                                    }) as $meal)
                                                        <div class="relative">
                                                            <div class="relative flex items-center justify-between p-3 bg-white rounded-lg border border-[#2F6D22]/10">
                                                                <div class="flex-1">
                                                                    <span class="text-sm font-medium text-[#252422]">{{ $meal->name }}</span>
                                                                    @if($meal->calories)
                                                                        <span class="text-xs text-[#2F6D22]/60 block">{{ $meal->calories }} cal</span>
                                                                    @endif
                                                                </div>
                                                                <div class="flex space-x-1">
                                                                    <button wire:click="editMeal({{ $meal->id }})" 
                                                                        class="p-1 rounded-lg text-[#A9DBB8]">
                                                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                                        </svg>
                                                                    </button>
                                                                    <button wire:click="deleteMeal({{ $meal->id }})" 
                                                                        class="p-1 rounded-lg text-red-500">
                                                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                                        </svg>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Recipes Tab -->
            @if($activeTab === 'recipes')
                <div class="bg-white/90 backdrop-blur-xl rounded-2xl shadow-lg border border-[#2F6D22]/10 overflow-hidden">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-8">
                            <div>
                                <h2 class="text-2xl font-bold text-[#252422]">My Recipes</h2>
                                <p class="text-sm text-[#2F6D22]/60 mt-1">Manage and organize your favorite recipes</p>
                            </div>
                            <button wire:click="openAddRecipeModal" class="relative">
                                <div class="absolute -inset-0.5 bg-gradient-to-r from-[#A9DBB8] to-[#2F6D22] rounded-xl blur opacity-60"></div>
                                <div class="relative flex items-center px-6 py-3 bg-white rounded-xl border border-[#2F6D22]/10 text-sm font-medium text-[#252422]">
                                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Add New Recipe
                                </div>
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($this->recipes as $recipe)
                                <div class="relative">
                                    <div class="relative bg-white rounded-xl shadow-sm border border-[#2F6D22]/10 overflow-hidden">
                                        <div class="p-6">
                                            <div class="flex justify-between items-start">
                                                <h3 class="text-lg font-bold text-[#252422]">{{ $recipe->name }}</h3>
                                                <div class="flex space-x-1">
                                                    <button wire:click="editRecipe({{ $recipe->id }})" 
                                                        class="p-2 rounded-lg text-[#A9DBB8]">
                                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                        </svg>
                                                    </button>
                                                    <button wire:click="deleteRecipe({{ $recipe->id }})" 
                                                        class="p-2 rounded-lg text-red-500">
                                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                            <p class="mt-2 text-sm text-[#2F6D22]/80">{{ $recipe->description }}</p>
                                            <div class="mt-4 flex items-center space-x-4">
                                                <div class="flex items-center space-x-2">
                                                    <div class="h-8 w-8 rounded-lg bg-[#A9DBB8]/10 flex items-center justify-center">
                                                        <svg class="h-4 w-4 text-[#A9DBB8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                    </div>
                                                    <span class="text-sm text-[#2F6D22]/60">{{ $recipe->prep_time }} min prep</span>
                                                </div>
                                                <div class="flex items-center space-x-2">
                                                    <div class="h-8 w-8 rounded-lg bg-[#2F6D22]/10 flex items-center justify-center">
                                                        <svg class="h-4 w-4 text-[#2F6D22]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                                        </svg>
                                                    </div>
                                                    <span class="text-sm text-[#2F6D22]/60">{{ $recipe->servings }} servings</span>
                                                </div>
                                            </div>
                                            <div class="mt-4 flex flex-wrap gap-2">
                                                <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-medium bg-[#A9DBB8]/10 text-[#A9DBB8]">
                                                    {{ ucfirst($recipe->meal_type) }}
                                                </span>
                                                <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-medium bg-[#2F6D22]/10 text-[#2F6D22]">
                                                    {{ $recipe->calories }} cal
                                                </span>
                                                <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-medium bg-[#A9DBB8]/10 text-[#A9DBB8]">
                                                    {{ $recipe->protein }}g protein
                                                </span>
                                                <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-medium bg-[#2F6D22]/10 text-[#2F6D22]">
                                                    {{ $recipe->carbs }}g carbs
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Shopping List Tab -->
            @if($activeTab === 'shopping-list')
                <div class="bg-white/90 backdrop-blur-xl rounded-2xl shadow-lg border border-[#2F6D22]/10 overflow-hidden">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-8">
                            <div>
                                <h2 class="text-2xl font-bold text-[#252422]">Shopping List</h2>
                                <p class="text-sm text-[#2F6D22]/60 mt-1">Everything you need for your meal plan</p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            @php
                                $allIngredients = collect();
                                foreach ($this->recipes as $recipe) {
                                    foreach ($recipe->ingredients as $ingredient) {
                                        $allIngredients->push([
                                            'name' => $ingredient->name,
                                            'amount' => $ingredient->pivot->amount,
                                            'unit' => $ingredient->pivot->unit,
                                            'recipe' => $recipe->name
                                        ]);
                                    }
                                }
                                $groupedIngredients = $allIngredients->groupBy('name')->map(function($group) {
                                    return [
                                        'name' => $group->first()['name'],
                                        'amount' => $group->sum('amount'),
                                        'unit' => $group->first()['unit'],
                                        'recipes' => $group->pluck('recipe')->unique()->values()
                                    ];
                                });
                            @endphp

                            @if($groupedIngredients->isEmpty())
                                <div class="text-center py-12">
                                    <div class="mx-auto h-24 w-24 rounded-full bg-[#A9DBB8]/10 flex items-center justify-center mb-4">
                                        <svg class="h-12 w-12 text-[#A9DBB8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-medium text-[#252422]">No ingredients yet</h3>
                                    <p class="text-sm text-[#2F6D22]/60 mt-1">Add some recipes to generate your shopping list</p>
                                </div>
                            @else
                                @foreach($groupedIngredients as $ingredient)
                                    <div class="relative">
                                        <div class="relative flex items-start space-x-4 p-4 bg-white rounded-xl border border-[#2F6D22]/10">
                                            <div class="flex-shrink-0 pt-1">
                                                <div class="relative">
                                                    <input type="checkbox" class="peer h-5 w-5 rounded border-[#2F6D22]/20 text-[#A9DBB8] focus:ring-[#A9DBB8]">
                                                    <div class="absolute inset-0 rounded border border-[#2F6D22]/20 peer-checked:border-[#A9DBB8]"></div>
                                                </div>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center justify-between">
                                                    <h4 class="text-sm font-medium text-[#252422]">{{ $ingredient['name'] }}</h4>
                                                    <span class="text-sm font-medium text-[#2F6D22]/60">{{ $ingredient['amount'] }} {{ $ingredient['unit'] }}</span>
                                                </div>
                                                <p class="mt-1 text-xs text-[#2F6D22]/60">Used in: {{ $ingredient['recipes']->join(', ') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Nutrition Tab -->
            @if($activeTab === 'nutrition')
                <div class="bg-white/90 backdrop-blur-xl rounded-2xl shadow-lg border border-[#2F6D22]/10 overflow-hidden">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-8">
                            <div>
                                <h2 class="text-2xl font-bold text-[#252422]">Nutritional Overview</h2>
                                <p class="text-sm text-[#2F6D22]/60 mt-1">Track your daily nutrition goals</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            <!-- Calories Card -->
                            <div class="relative">
                                <div class="relative bg-white rounded-xl p-6 border border-[#2F6D22]/10">
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="h-12 w-12 rounded-lg bg-[#A9DBB8]/10 flex items-center justify-center">
                                            <svg class="h-6 w-6 text-[#A9DBB8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                            </svg>
                                        </div>
                                        <span class="text-sm font-medium text-[#2F6D22]/60">Daily Goal: 2000</span>
                                    </div>
                                    <h3 class="text-3xl font-bold text-[#252422]">{{ $this->meals->sum('calories') }}</h3>
                                    <p class="text-sm font-medium text-[#2F6D22]/60 mt-1">Calories</p>
                                </div>
                            </div>

                            <!-- Protein Card -->
                            <div class="relative">
                                <div class="relative bg-white rounded-xl p-6 border border-[#2F6D22]/10">
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="h-12 w-12 rounded-lg bg-[#2F6D22]/10 flex items-center justify-center">
                                            <svg class="h-6 w-6 text-[#2F6D22]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                            </svg>
                                        </div>
                                        <span class="text-sm font-medium text-[#2F6D22]/60">Daily Goal: 150g</span>
                                    </div>
                                    <h3 class="text-3xl font-bold text-[#252422]">{{ $this->meals->sum('protein') }}g</h3>
                                    <p class="text-sm font-medium text-[#2F6D22]/60 mt-1">Protein</p>
                                </div>
                            </div>

                            <!-- Carbs Card -->
                            <div class="relative">
                                <div class="relative bg-white rounded-xl p-6 border border-[#2F6D22]/10">
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="h-12 w-12 rounded-lg bg-[#A9DBB8]/10 flex items-center justify-center">
                                            <svg class="h-6 w-6 text-[#A9DBB8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
                                            </svg>
                                        </div>
                                        <span class="text-sm font-medium text-[#2F6D22]/60">Daily Goal: 250g</span>
                                    </div>
                                    <h3 class="text-3xl font-bold text-[#252422]">{{ $this->meals->sum('carbs') }}g</h3>
                                    <p class="text-sm font-medium text-[#2F6D22]/60 mt-1">Carbs</p>
                                </div>
                            </div>

                            <!-- Fat Card -->
                            <div class="relative">
                                <div class="relative bg-white rounded-xl p-6 border border-[#2F6D22]/10">
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="h-12 w-12 rounded-lg bg-[#2F6D22]/10 flex items-center justify-center">
                                            <svg class="h-6 w-6 text-[#2F6D22]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                            </svg>
                                        </div>
                                        <span class="text-sm font-medium text-[#2F6D22]/60">Daily Goal: 70g</span>
                                    </div>
                                    <h3 class="text-3xl font-bold text-[#252422]">{{ $this->meals->sum('fat') }}g</h3>
                                    <p class="text-sm font-medium text-[#2F6D22]/60 mt-1">Fat</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </main>

    <!-- Add/Edit Meal Modal -->
    @if($showAddMealModal)
        <div class="fixed z-50 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-[#2F6D22] bg-opacity-75 transition-opacity backdrop-blur-sm" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg leading-6 font-bold text-[#252422]" id="modal-title">
                                        {{ $editingMeal ? 'Edit Meal' : 'Add Meal' }} for {{ $selectedDay }} - {{ ucfirst($selectedMealType) }}
                                    </h3>
                                    <button wire:click="closeModal" class="text-[#2F6D22]/60">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                                <div class="mt-4">
                                    <livewire:meal-form 
                                        :meal="$editingMeal" 
                                        :selected-day="$selectedDay" 
                                        :selected-meal-type="$selectedMealType" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Add/Edit Recipe Modal -->
    @if($showAddRecipeModal)
        <div class="fixed z-50 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-[#2F6D22] bg-opacity-75 transition-opacity backdrop-blur-sm" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg leading-6 font-bold text-[#252422]" id="modal-title">
                                        {{ $editingRecipe ? 'Edit Recipe' : 'Add New Recipe' }}
                                    </h3>
                                    <button wire:click="closeModal" class="text-[#2F6D22]/60">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                                <div class="mt-4">
                                    <livewire:recipe-form :recipe="$editingRecipe" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
