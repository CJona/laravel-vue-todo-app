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
        $user = auth()->user();

        return Inertia::render('Dashboard', [
            'categories' =>
                Category::where('user_id', '=', $user->id)
                    ->orWhere('is_admin', '=', true)
                    ->with('user') // $category->user | ->id, ->email
                    ->get()->keyBy('id'),
            'tasks' => $user->tasks
        ]);
    })->name('dashboard');

    // Admin categorie pagina
    Route::get('/admin', function () {
        $user = auth()->user();

        if( $user->is_admin === false) abort(403);

        return Inertia::render('Admin', [
            'admin_categories' => Category::where('is_admin', true)
                ->with('user')
                ->get()->keyBy('id'),
        ]);
    })->name('admin');

    // Nieuwe taak opslaan
    Route::post('/tasks', function (Request $request) {
        $user = auth()->user();

        if($request->items) {
            $request->validate([
                'items' => ['required', 'array'],
                'items.*.title' => ['required', 'string', 'min:3'],
                'items.*.completed' => ['required', 'boolean']
            ]);

            $user->update([
                'tasks' => $request->items
            ]);
        } else {
            $user->update([
                'tasks' => []
            ]);
        }

        return redirect()->route('dashboard');
    })->name('items.store');

    // Nieuwe categorie aanmaken
    Route::post('/category', function (Request $request) {

        $request->validate([
            'title' => ['required', 'string', 'min:3'],
            'on_admin' => ['required', 'boolean'],
        ]);

        // Controleren of het een admin categorie is EN of de gebruiker die het toevoegt ook een admin is
        if ($request->on_admin === true && auth()->user()->is_admin === true) {
            // Maken wij de categorie aan als admin categorie
            Category::create([
                'user_id' => auth()->id(),
                'title' => $request->title,
                'is_admin' => true
            ]);
            return redirect()->route('admin');
        } else {
            // Zoniet normale categorie
            Category::create([
                'user_id' => auth()->id(),
                'title' => $request->title,
                'is_admin' => false
            ]);
            return redirect()->route('dashboard');
        }

    })->name('category.store');

    // Categorie verwijderen
    Route::delete('/category/{category}', function (Request $request, Category $category) {
        $request->validate([
            'on_admin' => ['required', 'boolean'],
        ]);

        if ($request->on_admin === true) {
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
    })->name('category.delete');

    // categorie bewerken
    Route::put('/category/{category}', function (Request $request, Category $category) {
        $request->validate([
            'title' => ['required', 'string', 'min:3'],
            'on_admin' => ['required', 'boolean'],
        ]);

        if ($request->on_admin === true) {
            // admin categorie
            if(auth()->user()->is_admin === false || $category->is_admin === false) abort(403);
            $category->update(['title' => $request->title]);
            return redirect()->route('admin');
        } else {
            // gebruiker categorie
            if($category->user_id !== auth()->id()) abort(403);
            $category->update(['title' => $request->title]);
            return redirect()->route('dashboard');
        }
    })->name('category.update');
});

