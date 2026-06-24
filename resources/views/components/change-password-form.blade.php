<div class="txp-mobile-card mt-3">
    <h2 class="h6 mb-3"><i class="fa-solid fa-key me-2"></i>Cambiar contraseña</h2>

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
