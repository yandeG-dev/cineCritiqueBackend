<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPasswordMail;

class PasswordResetController extends Controller
{
    public function forgot(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();
        if (!$user) return response()->json(['message' => 'Utilisateur non trouvé'], 404);

        $token = Str::random(60);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            ['token' => $token, 'created_at' => Carbon::now()]
        );

        // Générer le lien pour le front-end
        $url = url("/reset-password-form?token=$token&email=".$request->email);

        // Envoyer l'email
        Mail::to($request->email)->send(new ResetPasswordMail($url));

        // Retour pour Postman si tu veux tester sans mail
        return response()->json([
            'message' => 'Lien de réinitialisation généré.',
            'token' => $token,
            'url' => $url
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|confirmed|min:8',
        ]);

        $record = DB::table('password_reset_tokens')->where('email', $request->email)->first();
        if (!$record || $record->token !== $request->token) {
            return response()->json(['message' => 'Token invalide ou expiré'], 400);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) return response()->json(['message' => 'Utilisateur non trouvé'], 404);

        $user->password = Hash::make($request->password); // ✅ hash sécurisé
        $user->save();

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json(['message' => 'Mot de passe réinitialisé avec succès']);
    }
}
