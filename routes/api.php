<?php


use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProtectedController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/test-firebase', function () {
    try {
        // Créer une instance de Firestore
        $factory = (new Factory)->withServiceAccount(base_path(env('FIREBASE_CREDENTIALS')));
        $firestore = $factory->createFirestore()->database();

        // Essayons d'écrire quelque chose dans Firestore
        $docRef = $firestore->collection('test')->document('connection_test');
        $docRef->set([
            'timestamp' => time(),
            'message' => 'Test de connexion réussi'
        ]);

        // Lisons ce que nous venons d'écrire
        $snapshot = $docRef->snapshot();
        $value = $snapshot->data();

        return response()->json([
            'success' => true,
            'message' => 'Connexion à Firebase réussie',
            'data' => $value
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur de connexion à Firebase: ' . $e->getMessage()
        ], 500);
    }
});

Route::post('/create-user', [UserController::class, 'store']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

Route::middleware([config('app.auth_mode') === 'firebase' ? 'auth.firebase' : 'auth:api'])->group(function () {
    Route::get('/v1/users', [UserController::class, 'index']);
    Route::patch('/v1/users/{id}', [UserController::class, 'update']);

});

//Route::post('/test-firebase-auth', [AuthController::class, 'testFirebaseAuth']);
