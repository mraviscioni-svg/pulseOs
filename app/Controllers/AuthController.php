<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Core\Validator;
use App\Services\AuthService;
use App\Services\TenantRegistrationService;

final class AuthController extends Controller
{
    public function showLogin(): void
    {
        $this->view('auth/login', ['title' => 'Iniciar sesión'], 'layouts/guest');
    }

    public function login(): void
    {
        $data = $this->input();
        $validator = new Validator();
        if (!$validator->validate($data, [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ])) {
            Session::flash('error', 'Credenciales inválidas.');
            Session::set('_old', $data);
            $this->redirect('/login');
        }

        $auth = new AuthService();
        if (!$auth->attempt($data['email'], $data['password'])) {
            Session::flash('error', 'Email o contraseña incorrectos.');
            $this->redirect('/login');
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
        $validator = new Validator();
        if (!$validator->validate($data, [
            'company_name' => 'required|min:2',
            'owner_name' => 'required|min:2',
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
            Session::flash('success', 'Empresa creada. Iniciá sesión.');
            $this->redirect('/login');
        } catch (\Throwable $e) {
            Session::flash('error', 'No se pudo registrar: ' . $e->getMessage());
            $this->redirect('/register');
        }
    }

    public function logout(): void
    {
        (new AuthService())->logout();
        $this->redirect('/login');
    }

    public function showForgotPassword(): void
    {
        $this->view('auth/forgot', ['title' => 'Recuperar contraseña'], 'layouts/guest');
    }

    public function forgotPassword(): void
    {
        Session::flash('success', 'Si el email existe, recibirás instrucciones (MVP: contactá soporte).');
        $this->redirect('/login');
    }
}
