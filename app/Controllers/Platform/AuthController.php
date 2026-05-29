<?php

declare(strict_types=1);

namespace App\Controllers\Platform;

use App\Core\Controller;
use App\Core\Session;
use App\Core\Validator;
use App\Services\AuthService;
use App\Services\PlatformAuthService;

final class AuthController extends Controller
{
    public function showLogin(): void
    {
        $this->view('admin/auth/login', ['title' => 'Admin ' . app_name()], 'layouts/admin-guest');
    }

    public function login(): void
    {
        $data = $this->input();
        $validator = new Validator();
        if (!$validator->validate($data, [
            'username' => 'required|min:3',
            'password' => 'required|min:6',
        ])) {
            Session::flash('error', 'Credenciales inválidas.');
            $this->redirect('/admin/login');
        }

        try {
            if (!(new PlatformAuthService())->attempt($data['username'], $data['password'])) {
                Session::flash('error', 'Usuario o contraseña incorrectos (probá admin / password).');
                $this->redirect('/admin/login');
            }
            \App\Core\Session::regenerate();
        } catch (\PDOException $e) {
            Session::flash('error', 'Error de base de datos. Importá 003 y 004 en phpMyAdmin.');
            if (config('app')['debug']) {
                Session::flash('error', 'DB: ' . $e->getMessage());
            }
            $this->redirect('/admin/login');
        } catch (\Throwable $e) {
            Session::flash('error', config('app')['debug'] ? $e->getMessage() : 'Error al iniciar sesión.');
            $this->redirect('/admin/login');
        }

        $this->redirect('/admin/tenants');
    }

    public function logout(): void
    {
        (new PlatformAuthService())->logout();
        $this->redirect('/admin/login');
    }

    public function stopImpersonate(): void
    {
        $return = (string) Session::get('platform_return_url', url('/admin/tenants'));
        (new AuthService())->stopImpersonation();
        Session::flash('success', 'Volviste al panel de administración.');
        $this->redirect($return);
    }
}
