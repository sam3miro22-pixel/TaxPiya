<div class="txp-mobile-card mt-3">
    <h2 class="h6 mb-3"><i class="fa-solid fa-key me-2"></i>Cambiar contraseña</h2>

    @if(!empty(auth()->user()->firebase_uid) && in_array(auth()->user()->user_role_id ?? 0, [2, 3]))
        <p class="small text-muted mb-3">
            Tu acceso principal es con Firebase (Google o correo). Si cambias contraseña aquí, solo aplica a acceso local de respaldo.
            Para cambiar la de Firebase, usa «Correo y contraseña» en el login o la cuenta de Google vinculada.
        </p>
    @endif

    @if(session('password_changed'))
        <div class="txp-alert-success small mb-3">Contraseña actualizada correctamente.</div>
    @endif
    @if($errors->has('oldpassword') || $errors->has('newpassword') || $errors->has('confirmpassword'))
        <div class="txp-auth-alert txp-auth-alert--error small mb-3">{{ $errors->first() }}</div>
    @endif

    <form method="post" action="{{ route('profile.password.update') }}" class="d-grid gap-2">
        @csrf
        <div>
            <label class="form-label small mb-1" for="oldpassword">Contraseña actual</label>
            <input type="password" class="form-control txp-input-dark" id="oldpassword" name="oldpassword" required autocomplete="current-password">
        </div>
        <div>
            <label class="form-label small mb-1" for="newpassword">Nueva contraseña</label>
            <input type="password" class="form-control txp-input-dark" id="newpassword" name="newpassword" required minlength="6" autocomplete="new-password">
        </div>
        <div>
            <label class="form-label small mb-1" for="confirmpassword">Confirmar nueva contraseña</label>
            <input type="password" class="form-control txp-input-dark" id="confirmpassword" name="confirmpassword" required minlength="6" autocomplete="new-password">
        </div>
        <button type="submit" class="txp-mobile-btn mt-1">
            <i class="fa-solid fa-lock"></i> Guardar nueva contraseña
        </button>
    </form>
</div>
