<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Session;
use App\Core\Validator;
use App\Models\UserModel;

final class UserController extends Controller
{
    public function index(): void
    {
        $roles = Database::connection()->query('SELECT id, name, slug FROM roles ORDER BY id')->fetchAll();
        $this->view('users/index', [
            'title' => 'Usuarios',
            'users' => (new UserModel())->listForTenant($this->tenantId()),
            'roles' => $roles,
        ]);
    }

    public function store(): void
    {
        $data = $this->input();
        $v = new Validator();
        if (!$v->validate($data, [
            'name' => 'required|min:2',
            'email' => 'required|email',
            'password' => 'required|min:8',
            'role_id' => 'required|numeric',
        ])) {
            Session::flash('error', implode(' ', $v->errors()));
            $this->redirect('/users');
        }

        try {
            (new UserModel())->create($this->tenantId(), $data);
            Session::flash('success', 'Usuario creado.');
        } catch (\Throwable $e) {
            Session::flash('error', 'No se pudo crear el usuario.');
        }
        $this->redirect('/users');
    }
}
