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
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CommuniquerController;
use App\Http\Controllers\TemplateMailController;
use App\Http\Controllers\RocketChatController;
use App\Http\Controllers\HelloAssoCallbackController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ContactController;
//pages
use App\Http\Controllers\ErrorPageController;
use App\Http\Controllers\AccueilPageController;
use App\Http\Controllers\ReglementInterieurPageController;
use App\Http\Controllers\BureauPageController;
use App\Http\Controllers\AdhesionPageController ;
use App\Http\Controllers\AchatsPageController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\BoutiquesPageController;

if(env("APP_DEBUG")){
    Route::get('/test',function(){
        return view('templateMail.templateMail');
    });
}

// Endpoint pour récupérer le jeton CSRF (uniquement pour les tests)
Route::get('/csrf', function() {
    return response()->json(['csrf_token' => csrf_token()]);
});

//Ajouter un produit
Route::get('/ajouter-produit', [ProductController::class, 'create'])->name('produit.create');
Route::post('/ajouter-produit', [ProductController::class, 'store'])->name('produits.store');

//Nb de ticket par produit
Route::get('/tickets/product/{product_id}', [TicketController::class, 'showTicket'])->name('tickets.show');
Route::get('/get-product/{product_id}', [ProductController::class, 'getProduct']);

//Redirection choix tickets
Route::get('/choisir-type-ticket/{product_id}', function ($product_id) {
    return view('choisir-type-ticket', compact('product_id'));
})->name('tickets.choose');

Route::get('/redirect-ticket', function (Illuminate\Http\Request $request) {
    $product_id = $request->query('product_id');
    $ticket_type = $request->query('ticket_type');

    if ($ticket_type == 'majestic') {
        return redirect()->route('tickets.majestic', ['product_id' => $product_id]);
    } else {
        return redirect()->route('tickets.standard', ['product_id' => $product_id]);
    }
})->name('tickets.redirect');




//Parser
Route::get('/parsepdf', [PdfController::class, 'parsePdf']);

//Upload ticket Majestic
Route::get('/ajouter-ticket-majestic/{product_id}', [TicketController::class, 'create'])->name('tickets.majestic');
Route::post('/ajouter-ticket-majestic/{product_id}', [TicketController::class, 'uploadTickets']);

//shop
Route::get('/shop/{id}', [ShopController::class, 'show'])->name('shop.show');
Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
Route::post('/helloasso/callback', [HelloAssoCallbackController::class, 'handleCallback'])->name('helloasso.callback');
Route::get('/helloasso/test', [HelloAssoCallbackController::class, 'testConnection'])->name('helloasso.test');
Route::get('/helloasso/test-callback', [HelloAssoCallbackController::class, 'testCallback'])->name('helloasso.test-callback');



/**
 * Connection routes
 */
// Login and register handling
Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'authenticate']);
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/register', [RegisterController::class, 'index'])->name('register');
Route::post('/register', [RegisterController::class, 'store']);

// Password reset routes
Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');


/**
 * Routes that do not require middleware
 */
//Accueil
Route::get('/', [AccueilPageController::class, 'index'])->name('accueil');

//Erreurs
Route::get('/error/{errorId?}', [ErrorPageController::class, 'showError'])->name('error');

// ReglementInterieur
Route::prefix('reglement-interieur')->group(function () {
    Route::get('/', [ReglementInterieurPageController::class, 'index'])->name('reglement_interieur');
    Route::get('/download', [ReglementInterieurPageController::class, 'download'])
        ->name('reglement.download');
});

// Bureau
Route::get('/bureau', [BureauPageController::class, 'index'])->name('bureau');

// RocketChat API routes
Route::prefix('api/rocket-chat')->middleware('auth')->group(function () {
    Route::get('/search-users', [RocketChatController::class, 'searchUsers']);
    Route::get('/channels', [RocketChatController::class, 'getChannels']);
    Route::post('/send-message', [RocketChatController::class, 'sendMessage']);
    Route::post('/send', [RocketChatController::class, 'sendDirectMessage'])->name('rocketchat.send');
});

// RocketChat routes
Route::get('/api/rocket-chat/test', [RocketChatController::class, 'testConnection']);
Route::get('/api/rocket-chat/search-users', [RocketChatController::class, 'searchUsers']);

// Test page for RocketChat functionality
Route::get('/rocket-chat-test', function() {
    return view('rocket-chat-test');
});

/**
 * Routes that require authentification
 */
Route::middleware('auth')->group(function () {
    // Profile page
    Route::prefix('profile')->group(function(){
        Route::get('/', [ProfileController::class, 'index'])->name('profile');
        Route::post('/', [ProfileController::class, 'update']);
        Route::post('/update', [ProfileController::class, 'update'])->name('profile.update');
    });

    // Adhesion routes
    Route::prefix('adhesion')->group(function () {
        Route::get('/', [AdhesionController::class, 'index'])->name('adhesion');
        Route::post('/', [AdhesionController::class, 'createAdhesion']);
        // This route needs admin privileges
        Route::get('/{userId}', [AdhesionController::class, 'getAdhesionsByUser'])->middleware('admin');
    });

    // Subvention users routes
    Route::prefix('demande-subvention')->name('demande-subvention.')->group(function () {
        Route::get('/', [SubventionController::class, 'index'])->name('index');
        Route::get('/download', [SubventionController::class, 'download'])->name('download');
        Route::post('/', [SubventionController::class, 'store'])->name('store');
        Route::get('/document/{userId}/{filename}', [SubventionController::class, 'viewDocument'])->name('document.view');
    });

    // achats
    Route::get('/achats', [AchatsController::class, 'index'])->name('achats');

    // tickets
    Route::get('/tickets/{ticketId}/view', [TicketController::class, 'viewTicket'])->name('tickets.view');

    // Contact routes
    Route::prefix('contact')->group(function () {
        Route::get('/', [ContactController::class, 'index'])->name('contact');
        Route::get('/shop/{shopId}', [ContactController::class, 'getShopAdministrators'])->name('contact.shop-administrators');
        Route::get('/all-administrators', [ContactController::class, 'getAllAdministrators']);
    });
});

/**
 * Routes that require authentication and admin rights
 */
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

    // Template Mail routes
    Route::prefix('template-mail')->name('template-mail.')->group(function () {
        Route::get('/', [TemplateMailController::class, 'index'])->name('index');
        Route::post('/', [TemplateMailController::class, 'store'])->name('store');
        Route::get('/{id}', [TemplateMailController::class, 'show'])->name('show');
        Route::put('/{id}', [TemplateMailController::class, 'update'])->name('update');
        Route::delete('/{id}', [TemplateMailController::class, 'destroy'])->name('destroy');
    });

    // Subventions admin routes
    Route::prefix('subventions')->name('subventions.')->group(function () {
        Route::get('/', [SubventionController::class, 'indexPending'])->name('index');
        Route::put('/{inquiry}', [SubventionController::class, 'update'])->name('update');
    });

    // Communiquer routes
    Route::prefix('communiquer')->group(function () {
        Route::get('/', [CommuniquerController::class, 'index'])->name('communiquer');
        Route::post('/', [CommuniquerController::class, 'sendCommunication']);
        Route::get('/confirm', [CommuniquerController::class, 'confirmSendCommunication'])->name('communiquer.confirm');
        Route::post('/template/save', [CommuniquerController::class, 'saveTemplate']);
    });

    // Template mail gestion routes
    Route::prefix('template-mail')->name('template-mail.')->group(function () {
        Route::get('/', [TemplateMailController::class, 'index'])->name('index');
        Route::post('/', [TemplateMailController::class, 'store'])->name('store');
        Route::get('/{id}', [TemplateMailController::class, 'show'])->name('show');
        Route::put('/{id}', [TemplateMailController::class, 'update'])->name('update');
        Route::delete('/{id}', [TemplateMailController::class, 'destroy']);
    });

    Route::get('/boutiques' , [BoutiquesPageController::class, 'index'])->name('boutiques');
});








// éléments à trier/clean

//Route::get('/tickets', [TicketController::class, 'tickets']);

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

