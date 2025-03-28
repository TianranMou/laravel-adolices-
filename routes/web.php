<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FamilyMemberController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\AdhesionController;
use App\Http\Controllers\StateSubController;
use App\Http\Controllers\AccueilController;
use App\Http\Controllers\AdherentController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\DematerializedTicketController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubventionController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\AchatsController;
//pages
use App\Http\Controllers\ErrorPageController;
use App\Http\Controllers\AccueilPageController;
use App\Http\Controllers\ReglementInterieurPageController;
use App\Http\Controllers\BureauPageController;
use App\Http\Controllers\AdhesionPageController ;
use App\Http\Controllers\AchatsPageController;
use App\Http\Controllers\BoutiquesPageController;

if(env("APP_DEBUG")){
    Route::get('/test',[BoutiquesPageController::class, 'index']);
}



// Endpoint pour récupérer le jeton CSRF (uniquement pour les tests)
Route::get('/csrf', function() {
    return response()->json(['csrf_token' => csrf_token()]);
});

//Parser
Route::get('/parsepdf', [PdfController::class, 'parsePdf']);

//Accueil
Route::get('/', [AccueilPageController::class, 'index'])->name('accueil');

//Erreurs
Route::get('/error/{errorId?}', [ErrorPageController::class, 'showError'])->name('error');

// Login and register handling
Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'authenticate']);
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/register', [RegisterController::class, 'index'])->name('register');
Route::post('/register', [RegisterController::class, 'store']);

// ReglementInterieur
Route::get('/reglement-interieur', [ReglementInterieurPageController::class, 'index'])->name('reglement_interieur');

// Bureau
Route::get('/bureau', [BureauPageController::class, 'index'])->name('bureau');
// achats
Route::get('/achats', [AchatsController::class, 'index'])->name('achats');
//Demande subvention
Route::get('/subvention_inquiry', [SubventionController::class, 'index']);
Route::post('/subvention_inquiry', [SubventionController::class, 'store'])->name('subventions.store');

//Accueil
Route::get('/', [AccueilPageController::class, 'index'])->name('accueil');

// Profile page
Route::prefix('profile')->middleware('auth')->group(function(){
    Route::get('/', [ProfileController::class, 'index'])->name('profile');
    Route::post('/', [ProfileController::class, 'update']);

});

//page achats
Route::get('/achats', [AchatsController::class, 'index'])->name('achats');

// apply auth middleware to all routes in this group
Route::middleware('auth')->group(function () {
    // Adhesion routes
    Route::prefix('adhesion')->group(function () {
        Route::get('/', [AdhesionController::class, 'index'])->name('adhesion');
        Route::post('/', [AdhesionController::class, 'createAdhesion']);
        // This route needs admin privileges
        Route::get('/{userId}', [AdhesionController::class, 'getAdhesionsByUser'])->middleware('admin');
    });

    // Subvention users routes
    Route::prefix('subventions')->name('subventions.')->group(function () {
        Route::get('/create', [SubventionController::class, 'create'])->name('create');
        Route::post('/', [SubventionController::class, 'store'])->name('store');
    });
});




// apply both auth and admin middleware to all routes in this group
Route::middleware(['auth', 'admin'])->group(function () {
    // Adherents routes
    Route::prefix('adherents')->group(function () {
        Route::get('/', [AdherentController::class, 'index'])->name('adherents');
        Route::get('/{user_id}', [AdherentController::class, 'getAdherent']);
        Route::post('/', [AdherentController::class, 'addAdherent']);
        Route::put('/{user_id}', [AdherentController::class, 'updateAdherent']);
        Route::delete('/{user_id}', [AdherentController::class, 'deleteAdherent']);
        Route::get('/year/{year}', [AdherentController::class, 'getAdherentsByYear']);
        Route::delete('/{user_id}/family-members/{member_id}', [AdherentController::class, 'deleteFamilyMember']);
    });

    // Subventions admin routes
    Route::prefix('subventions')->name('subventions.')->group(function () {
        Route::get('/', [SubventionController::class, 'index'])->name('index');
        Route::post('/{id}/validate', [SubventionController::class, 'validate'])->name('validate');
        Route::post('/{id}/refuse', [SubventionController::class, 'refuse'])->name('refuse');
    });
});

//Route::get('/tickets', [TicketController::class, 'tickets']);

// API Resources
// Route::apiResource('family-members', FamilyMemberController::class);
// Route::apiResource('shops', ShopController::class);
// Route::apiResource('state-subs', StateSubController::class);

//Route::resource('subventions', SubventionController::class);

Route::resource('tickets', TicketController::class);
//pour test
// Route::prefix('dematerialized-tickets')->group(function () {
//     Route::get('/', [DematerializedTicketController::class, 'getAll'])->name('dematerialized-tickets.getAll');
//     Route::get('/{id}', [DematerializedTicketController::class, 'getById'])->name('dematerialized-tickets.getById');
//     Route::put('/{id}', [DematerializedTicketController::class, 'updateId'])->name('dematerialized-tickets.updateId');
// });

//need view to be added
Route::prefix('dematerialized-tickets')->name('dematerialized-tickets.')->group(function () {
    Route::get('/', [DematerializedTicketController::class, 'index'])->name('index');
    Route::get('/{id}', [DematerializedTicketController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [DematerializedTicketController::class, 'edit'])->name('edit');
    Route::put('/{id}', [DematerializedTicketController::class, 'update'])->name('update');
});
