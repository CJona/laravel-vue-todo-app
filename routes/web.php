<?php

use App\Models\Category;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use phpDocumentor\Reflection\Types\True_;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/**
 * Middleware die controleert of jij ingelogd bent,
 * Als jij niet ingelogd bent word je naar de /login gestuurd
 */

Route::middleware(['auth:sanctum', 'verified'])->group(function () {

    /**
     * Dashboard pagina tonen
     */
    Route::get('/', function () {
        $user = auth()->user(); // Ingelogde gebruiker

        // Vue.js pagina weergeven (/resources/js/Pages/Dashboard.vue)
        return Inertia::render('Dashboard', [
            'categories' =>
                Category::where('user_id', '=', $user->id) // Categorieen ophalen van de gebruiker
                    ->orWhere('is_admin', '=', true) // Globale categorieen ophalen
                    ->with('user') // Gebruiker relatie bij de categorieen inladen
                    ->get()->keyBy('id'), // Database ID gebruiken als index
            'tasks' => $user->tasks // De taken van de gebruiker inladen
        ]);
    })->name('dashboard'); // Route naam

    Route::middleware(['admin_guard'])-> group(function() {

        /**
         * Admin pagina voor de categorieen tonen
         */
        Route::get('/admin', function () {
            $user = auth()->user(); // Ingelogde gebruiker

            if ($user->is_admin === false) {
                abort(403); // Als de gebruiker GEEN admin is, foutmelding tonen
            }

            // Vue.js pagina weergeven (/resources/js/Pages/Admin.vue)
            return Inertia::render('Admin', [
                'admin_categories' => Category::where('is_admin', true) // Alle admin categorieen ophalen
                ->with('user') // Gebruiker relatie bij de categorieen inladen
                ->get()->keyBy('id'), // Database ID gebruiken als index
            ]);
        })->name('admin'); // Route naam


    });

    /**
     * Route om de taken van de gebruiker op te slaan
     * @param Request $request | Bevat formuliergegevens
     */
    Route::post('/tasks', function (Request $request) {
        $user = auth()->user(); // Ingelogde gebruiker

        if ($request->items) { // Als wij vanuit de pagina de taken van de gebruiker ontvangen
            $request->validate([ // Controleer of de structuur van de data overeenkomt met wat wij nodig hebben
                'items' => ['required', 'array'],
                'items.*.title' => ['required', 'string', 'min:3'],
                'items.*.completed' => ['required', 'boolean']
            ]);

            $user->update([ // Update de gebruiker's taken
                'tasks' => $request->items
            ]);
        } else { // Als wij GEEN taken ontvangen zorgen wij ervoor dat de gebruiker op zijn minst een lege array heeft voor de taken
            $user->update([
                'tasks' => []
            ]);
        }

        return redirect()->route('dashboard'); // Terugsturen naar de dashboard
    })->name('items.store'); // Route naam

    /**
     * Route om een categorie voor de gebruiker aan te maken
     * @param Request $request | Bevat formuliergegevens
     */
    Route::post('/category', function (Request $request) {
        $user = auth()->user(); // Ingelogde gebruiker

        $request->validate([ // Controleer of de structuur van de data overeenkomt met wat wij nodig hebben
            'title' => ['required', 'string', 'min:3'],
            'on_admin' => ['required', 'boolean'],
        ]);

        if ($request->on_admin === true && $user->is_admin === true) { // Controleren of de gebruiker op de admin pagina is EN of de gebruiker die het toevoegt ook een admin is
            Category::create([ // Categorie aanmaken voor de gebruiker
                'user_id' => $user->id,
                'title' => $request->title,
                'is_admin' => true
            ]);
            return redirect()->route('admin'); // Terugsturen naar admin pagina
        } else { // De gebruiker is NIET op de admin dashboard en is GEEN admin
            Category::create([ // Dan maken wij een normale categorie voor de gebruiker aan
                'user_id' => $user->id,
                'title' => $request->title,
                'is_admin' => false
            ]);
            return redirect()->route('dashboard'); // Terugsturen naar de gebruiker dashboard
        }
    })->name('category.store'); // Route naam

    /**
     * Route om de categorie van een gebruiker te verwijderen
     * @param Request $request | Bevat formuliergegevens
     * @param Category $category | De categorie die verwijderd wordt
     */
    Route::delete('/category/{category}', function (Request $request, Category $category) {
        $user = auth()->user(); // Ingelogde gebruiker

        $request->validate([ // Controleer of de structuur van de data overeenkomt met wat wij nodig hebben
            'on_admin' => ['required', 'boolean'],
        ]);

        if ($request->on_admin === true) { // Controleren of de gebruiker op de admin pagina is
            if ($user->is_admin === false || $category->is_admin === false) {
                abort(403); // Als de gebruiker GEEN admin is OF als de categorie GEEN admin categorie is, tonen wij een foutmelding
            }
            $category->delete(); // Categorie verwijderen
            return redirect()->route('admin'); // Terugsturen naar admin pagina
        } else { // Als de gebruiker op de dashboard pagina is (geen admin dus)
            if ($category->user_id !== $user->id) {
                abort(403); // Als de categorie die verwijderd wordt NIET van de ingelogde gebruiker is, tonen wij een foutmelding
            }
            $category->delete(); // Categorie verwijderen
            return redirect()->route('dashboard'); // Terugsturen naar de dashboard pagina
        }
    })->name('category.delete'); // Route naam

    /**
     * Route om de categorie van de gebruiker te bewerken
     * @param Request $request | Bevat formuliergegevens
     * @param Category $category | De categorie die verwijderd wordt
     */
    Route::put('/category/{category}', function (Request $request, Category $category) {
        $user = auth()->user(); // Ingelogde gebruiker

        $request->validate([ // Controleer of de structuur van de data overeenkomt met wat wij nodig hebben
            'title' => ['required', 'string', 'min:3'],
            'on_admin' => ['required', 'boolean'],
        ]);

        if ($request->on_admin === true) { // Controleren of de gebruiker op de admin pagina is
            if ($user->is_admin === false || $category->is_admin === false) {
                abort(403); // Als de gebruiker GEEN admin is OF als de categorie GEEN admin categorie is, tonen wij een foutmelding
            }
            $category->update(['title' => $request->title]); // De titel van de categorie bewerken
            return redirect()->route('admin'); // Terugsturen naar de admin pagina
        } else { // Als de gebruiker op de dashboard pagina is (geen admin dus)
            if ($category->user_id !== $user->id) {
                abort(403); // Als de categorie die bewerkt wordt NIET van de ingelogde gebruiker is, tonen wij een foutmelding
            }
            $category->update(['title' => $request->title]); // De titel van de categorie bewerken
            return redirect()->route('dashboard'); // Terugsturen naar de dashboard pagina
        }
    })->name('category.update'); // Route naam
});
