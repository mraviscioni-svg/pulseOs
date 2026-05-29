<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Core\Validator;
use App\Models\UserModel;
use App\Services\AuthService;
use App\Services\PasswordResetService;
use App\Services\TenantRegistrationService;

final class AuthController extends Controller
{
    public function showLogin(): void
    {
        $tenantSlug = trim((string) ($_GET['tenant'] ?? ''));
        $prefillUsername = trim((string) ($_GET['username'] ?? old('username', '')));
        $loginTenant = null;

        if ($tenantSlug !== '') {
            $stmt = \App\Core\Database::connection()->prepare(
                'SELECT name, slug, is_active FROM tenants WHERE slug = :slug LIMIT 1'
            );
            $stmt->execute(['slug' => $tenantSlug]);
            $loginTenant = $stmt->fetch() ?: null;
        }

        $this->view('auth/login', [
            'title' => 'Iniciar sesión',
            'loginTenant' => $loginTenant,
            'tenantSlug' => $tenantSlug,
            'prefillUsername' => $prefillUsername,
        ], 'layouts/guest');
    }

    public function login(): void
    {
        $data = $this->input();
        $tenantSlug = trim((string) ($data['tenant'] ?? $_GET['tenant'] ?? ''));
        $validator = new Validator();
        if (!$validator->validate($data, [
            'username' => 'required|min:3',
            'password' => 'required|min:6',
        ])) {
            Session::flash('error', 'Credenciales inválidas.');
            Session::set('_old', $data);
            $this->redirect($tenantSlug !== '' ? tenant_login_url($tenantSlug, $data['username'] ?? null) : '/login');
        }

        try {
            $username = normalize_username((string) $data['username']);
            $userModel = new UserModel();
            $resolved = $userModel->resolveLoginUser($username, $tenantSlug !== '' ? $tenantSlug : null);

            if ($resolved['ambiguous']) {
                Session::flash('error', 'Este usuario existe en más de un comercio. Ingresá desde el enlace de acceso de tu comercio.');
                Session::set('_old', $data);
                $this->redirect('/login');
            }

            $auth = new AuthService();
            if (!$auth->attempt($data['username'], $data['password'], $tenantSlug !== '' ? $tenantSlug : null)) {
                $msg = $tenantSlug !== ''
                    ? 'Usuario o contraseña incorrectos para este comercio.'
                    : 'Usuario o contraseña incorrectos.';
                Session::flash('error', $msg);
                $this->redirect($tenantSlug !== '' ? tenant_login_url($tenantSlug, $data['username'] ?? null) : '/login');
            }
            \App\Core\Session::regenerate();
        } catch (\PDOException $e) {
            Session::flash('error', 'Error de base de datos. Verificá migraciones y .env en el servidor.');
            if (config('app')['debug']) {
                Session::flash('error', 'DB: ' . $e->getMessage());
            }
            $this->redirect($tenantSlug !== '' ? tenant_login_url($tenantSlug) : '/login');
        } catch (\Throwable $e) {
            Session::flash('error', config('app')['debug'] ? $e->getMessage() : 'Error al iniciar sesión.');
            $this->redirect($tenantSlug !== '' ? tenant_login_url($tenantSlug) : '/login');
        }

        $this->redirect('/dashboard');
    }

    public function showRegister(): void
    {
        $this->view('auth/register', [
            'title' => 'Registrar empresa',
            'businessTypes' => config('business_types'),
        ], 'layouts/guest');
    }

    public function register(): void
    {
        $data = $this->input();
        $data['username'] = normalize_username((string) ($data['username'] ?? ''));

        $validator = new Validator();
        if (!$validator->validate($data, [
            'company_name' => 'required|min:2',
            'owner_name' => 'required|min:2',
            'username' => 'required|min:3',
            'email' => 'required|email',
            'password' => 'required|min:8',
            'business_type' => 'required',
        ])) {
            Session::flash('error', implode(' ', $validator->errors()));
            Session::set('_old', $data);
            $this->redirect('/register');
        }

        try {
            (new TenantRegistrationService())->register($data);
            Session::flash('success', 'Empresa creada. Iniciá sesión con tu usuario.');
            $this->redirect('/login');
        } catch (\Throwable $e) {
            Session::flash('error', 'No se pudo registrar: ' . $e->getMessage());
            $this->redirect('/register');
        }
    }

    public function logout(): void
    {
        if (is_platform_impersonating()) {
            $return = (string) Session::get('platform_return_url', url('/admin/tenants'));
            (new AuthService())->stopImpersonation();
            Session::flash('success', 'Volviste al panel de administración.');
            $this->redirect($return);

            return;
        }

        (new AuthService())->logout();
        $this->redirect('/login');
    }

    public function showForgotPassword(): void
    {
        $this->view('auth/forgot', ['title' => 'Recuperar contraseña'], 'layouts/guest');
    }

    public function forgotPassword(): void
    {
        $username = normalize_username((string) ($this->input()['username'] ?? ''));
        $resetUrl = (new PasswordResetService())->request($username);
        if ($resetUrl && config('app')['debug']) {
            Session::flash('success', 'Link de recuperación (modo debug): ' . $resetUrl);
        } else {
            Session::flash('success', 'Si el usuario existe y tiene email, recibirás un enlace.');
        }
        $this->redirect('/login');
    }

    public function showResetPassword(array $params): void
    {
        $this->view('auth/reset', [
            'title' => 'Nueva contraseña',
            'token' => $params['token'] ?? '',
        ], 'layouts/guest');
    }

    public function resetPassword(array $params): void
    {
        $data = $this->input();
        $validator = new Validator();
        if (!$validator->validate($data, ['password' => 'required|min:8'])) {
            Session::flash('error', 'Contraseña inválida (mínimo 8 caracteres).');
            $this->redirect('/reset-password/' . $params['token']);
        }

        $ok = (new PasswordResetService())->reset($params['token'], $data['password']);
        if (!$ok) {
            Session::flash('error', 'El enlace expiró o no es válido.');
            $this->redirect('/forgot-password');
        }

        Session::flash('success', 'Contraseña actualizada. Iniciá sesión.');
        $this->redirect('/login');
    }
}
