<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\UserService;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(Request $request)
{
    $role = $request->query('role');
    $users = User::byRole($role)->get();
    return response()->json($users);
}




    public function store(Request $request)  
{
    // Validation des données
    $request->validate([
        'prenom' => 'required|string|max:255',
        'nom' => 'required|string|max:255',
        'email' => 'required|email',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'password' => 'required|string|min:8|confirmed',
        'role' => 'nullable|in:Admin,Coach,CM,Apprenant', // Ajoutez la validation pour le rôle

    ]);

    try {
        $data = $request->only(['prenom', 'nom', 'email', 'password','role']);
        $image = $request->file('image'); // Récupérer l'image téléchargée
        
        // Appel au service pour créer l'utilisateur avec les données et l'image
        $this->userService->createUser($data, $image);

        return response()->json([
            'success' => true,
            'message' => 'Utilisateur créé avec succès dans Firestore',
        ], 201);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la création de l\'utilisateur: ' . $e->getMessage()
        ], 500);
    }
}



}