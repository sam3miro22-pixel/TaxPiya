<?php

namespace App\Http\Controllers;

use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfilePasswordController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'oldpassword'       => ['required', 'string'],
            'newpassword'       => ['required', 'string', 'min:6', 'max:64'],
            'confirmpassword'   => ['required', 'same:newpassword'],
        ], [
            'oldpassword.required'     => 'Ingresa tu contraseña actual.',
            'newpassword.required'     => 'Ingresa la nueva contraseña.',
            'newpassword.min'          => 'La nueva contraseña debe tener al menos 6 caracteres.',
            'confirmpassword.same'     => 'La confirmación no coincide con la nueva contraseña.',
        ]);

        $user = Users::find(auth()->id());
        if (!$user) {
            return back()->withErrors(['oldpassword' => 'Sesión no válida.']);
        }

        if (!Hash::check($request->oldpassword, (string) $user->password)) {
            return back()->withErrors(['oldpassword' => 'La contraseña actual no es correcta.']);
        }

        $user->update(['password' => Hash::make($request->newpassword)]);

        return back()->with('password_changed', true);
    }
}
