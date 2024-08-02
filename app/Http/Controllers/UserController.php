<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Affiche une liste de tous les utilisateurs.
     */
    public function index()
    {
        // Récupère tous les utilisateurs et les retourne en JSON
        return response()->json(User::all(), 200);
    }

    /**
     * Crée un nouvel utilisateur.
     */
    public function store(Request $request)
    {
        // Valide les données envoyées par la requête
        $request->validate([
            'name' => 'required|string|max:255',
            'telephone' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        // Crée un nouvel utilisateur
        $user = User::create([
            'name' => $request->name,
            'telephone' => $request->telephone,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Hash le mot de passe avant de l'enregistrer
        ]);

        // Retourne l'utilisateur créé en JSON
        return response()->json($user, 201);
    }

    /**
     * Affiche les détails d'un utilisateur spécifique.
     */
    public function show($id)
    {
        $user = User::find($id);

        if ($user) {
            return response()->json($user, 200);
        } else {
            return response()->json(['message' => 'Utilisateur non trouvé'], 404);
        }
    }

    /**
     * Met à jour un utilisateur spécifique.
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if ($user) {
            $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'telephone' => 'sometimes|required|string|max:255|unique:users,telephone,'.$id,
                'email' => 'sometimes|required|string|email|max:255|unique:users,email,'.$id,
                'password' => 'sometimes|nullable|string|min:6',
            ]);

            $user->name = $request->name ?? $user->name;
            $user->telephone = $request->telephone ?? $user->telephone;
            $user->email = $request->email ?? $user->email;
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            $user->save();

            return response()->json($user, 200);
        } else {
            return response()->json(['message' => 'Utilisateur non trouvé'], 404);
        }
    }

    /**
     * Supprime un utilisateur spécifique.
     */
    public function destroy($id)
    {
        $user = User::find($id);

        if ($user) {
            $user->delete();

            return response()->json(['message' => 'Utilisateur supprimé avec succès'], 200);
        } else {
            return response()->json(['message' => 'Utilisateur non trouvé'], 404);
        }
    }
}