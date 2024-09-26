<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Kreait\Firebase\Contract\Auth;

class UserService
{
    protected $userRepository;
    protected $cloudinaryService;
    protected $auth;

    public function __construct(
        UserRepository $userRepository,
        CloudinaryService $cloudinaryService,
        Auth $auth
    ) {
        $this->userRepository = $userRepository;
        $this->cloudinaryService = $cloudinaryService;
        $this->auth = $auth;
    }

    public function createUser(array $data, ?UploadedFile $image = null)
    {
        // Check if user exists in Firestore
        $existingUser = $this->userRepository->findByEmail($data['email']);
        if ($existingUser->size() > 0) {
            throw new \Exception('This email is already in use.');
        }

        // Create user in Firebase Authentication
        $firebaseUser = $this->auth->createUser([
            'email' => $data['email'],
            'password' => $data['password'],
        ]);

        // Prepare data for local database and Firestore
        $userData = [
            'prenom' => $data['prenom'],
            'nom' => $data['nom'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'statut' => 'actif',
            'role' => $data['role'] ?? 'Coach',
            'firebase_uid' => $firebaseUser->uid,
        ];

        if ($image) {
            $imageUrl = $this->cloudinaryService->uploadImage($image);
            $userData['image_url'] = $imageUrl;
        }

        // Save user in local database
        User::create($userData);

        // Save user in Firestore
        $this->userRepository->create($userData);

        return $firebaseUser;
    }

    

}