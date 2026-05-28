<?php

declare(strict_types=1);

namespace App\Controllers\Platform;

use App\Core\Controller;
use App\Core\Session;
use App\Core\Validator;
use App\Services\PlatformAuthService;

final class AuthController extends Controller
{
    public function showLogin(): void
    {
        $this->view('admin/auth/login', ['title' => 'Admin PulseOS'], 'layouts/admin-guest');
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

        if (!(new PlatformAuthService())->attempt($data['username'], $data['password'])) {
            Session::flash('error', 'Usuario o contraseña incorrectos.');
            $this->redirect('/admin/login');
        }

        $this->redirect('/admin/tenants');
    }

    public function logout(): void
    {
        (new PlatformAuthService())->logout();
        $this->redirect('/admin/login');
    }
}
