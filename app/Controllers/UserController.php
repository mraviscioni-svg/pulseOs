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
        } catch (\Throwable) {
            Session::flash('error', 'No se pudo crear el usuario.');
        }
        $this->redirect('/users');
    }

    public function edit(array $params): void
    {
        $user = (new UserModel())->findForTenant($this->tenantId(), (int) $params['id']);
        if (!$user) {
            $this->redirect('/users');
        }
        $roles = Database::connection()->query('SELECT id, name FROM roles ORDER BY id')->fetchAll();
        $this->view('users/edit', ['title' => 'Editar usuario', 'user' => $user, 'roles' => $roles]);
    }

    public function update(array $params): void
    {
        $data = $this->input();
        (new UserModel())->update($this->tenantId(), (int) $params['id'], $data);
        Session::flash('success', 'Usuario actualizado.');
        $this->redirect('/users');
    }

    public function toggle(array $params): void
    {
        (new UserModel())->setActive($this->tenantId(), (int) $params['id'], (int) ($this->input()['is_active'] ?? 0));
        Session::flash('success', 'Estado actualizado.');
        $this->redirect('/users');
    }
}
