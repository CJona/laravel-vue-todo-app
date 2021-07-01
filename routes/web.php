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

// Middleware als je ingelogd bent / auth guest
Route::middleware(['auth:sanctum', 'verified'])->group(function() {
    // Dashboard pagina
    Route::get('/', function () {
        return Inertia::render('Dashboard', [
            'categories' =>
                Category::where('user_id', auth()->id())
                    ->orWhere('is_admin', true)
                    ->with('user')
                    ->get()->keyBy('id'),
            'tasks' => auth()->user()->tasks
        ]);
    })->name('dashboard');

    // Admin categorie pagina
    Route::get('/admin', function () {
        if(auth()->user()->is_admin === false) abort(403);

        return Inertia::render('Admin', [
            'admin_categories' => Category::where('is_admin', true)
                ->with('user')
                ->get()->keyBy('id'),
        ]);
    })->name('admin');

    // Nieuwe taak opslaan
    Route::post('/items', function (Request $request) {
        if($request->items) {
            $request->validate([
                'items' => ['required', 'array'],
                'items.*.title' => ['required', 'string', 'min:3'],
                'items.*.completed' => ['required', 'boolean']
            ]);

            auth()->user()->update([
                'tasks' => $request->items
            ]);
        } else {
            auth()->user()->update([
                'tasks' => []
            ]);
        }


        return redirect()->route('dashboard');
    });

    // Nieuwe categorie aanmaken
    Route::post('/category', function (Request $request) {

        $request->validate([
            'title' => ['required', 'string', 'min:3'],
            'is_admin' => ['required', 'boolean'],
        ]);

        // Controleren of het een admin categorie is EN of de gebruiker die het toevoegt ook een admin is
        if ($request->is_admin === true && auth()->user()->is_admin === true) {
            // Maken wij de categorie aan als admin categorie
            Category::create([
                'user_id' => auth()->id(),
                'title' => $request->title,
                'is_admin' => true
            ]);
            return redirect()->route('admin');
        } else {
            // Zoniet nirmale categorie
            Category::create([
                'user_id' => auth()->id(),
                'title' => $request->title,
                'is_admin' => false
            ]);
            return redirect()->route('dashboard');
        }

    });

    // Categorie verwijderen
    Route::delete('/category/{category}', function (Request $request, Category $category) {
        $request->validate([
            'is_admin' => ['required', 'boolean'],
        ]);

        if ($request->is_admin === true) {
            // admin categorie
            if(auth()->user()->is_admin === false || $category->is_admin === false) abort(403);
            $category->delete();
            return redirect()->route('admin');
        } else {
            // gebruiker categorie
            if($category->user_id !== auth()->id()) abort(403);
            $category->delete();
            return redirect()->route('dashboard');
        }
    });
});

