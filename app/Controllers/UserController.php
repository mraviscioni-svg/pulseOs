<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Concerns\ExportableList;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Session;
use App\Core\Validator;
use App\Models\UserModel;

final class UserController extends Controller
{
    use ExportableList;

    public function index(): void
    {
        $extra = [];
        if (!empty($_GET['invite'])) {
            $extra = [
                'modal' => $this->modalMeta('Invitar usuario', 'Creá un acceso con contraseña temporal.'),
                'user' => null,
                'roles' => $this->roles(),
            ];
        }

        $this->renderIndex($extra);
    }

    public function store(): void
    {
        $data = $this->input();
        $data['username'] = normalize_username((string) ($data['username'] ?? ''));

        $v = new Validator();
        if (!$v->validate($data, [
            'name' => 'required|min:2',
            'username' => 'required|min:3',
            'email' => 'required|email',
            'password' => 'required|min:8',
            'role_id' => 'required|numeric',
        ])) {
            Session::flash('error', implode(' ', $v->errors()));
            Session::set('_old', $data);
            $this->redirect('/users?invite=1');
        }

        if ((new UserModel())->usernameExists($data['username'])) {
            Session::flash('error', 'Ese usuario ya está en uso.');
            Session::set('_old', $data);
            $this->redirect('/users?invite=1');
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

        $this->renderIndex([
            'modal' => $this->modalMeta('Editar usuario', 'Los datos se guardan en tu tenant actual.'),
            'user' => $user,
            'roles' => $this->roles(),
        ]);
    }

    public function update(array $params): void
    {
        $data = $this->input();
        $data['username'] = normalize_username((string) ($data['username'] ?? ''));
        $id = (int) $params['id'];

        if ((new UserModel())->usernameExists($data['username'], $id)) {
            Session::flash('error', 'Ese usuario ya está en uso.');
            Session::set('_old', $data);
            $this->redirect('/users/' . $id . '/edit');
        }

        (new UserModel())->update($this->tenantId(), $id, $data);
        Session::flash('success', 'Usuario actualizado.');
        $this->redirect('/users');
    }

    public function toggle(array $params): void
    {
        (new UserModel())->setActive($this->tenantId(), (int) $params['id'], (int) ($this->input()['is_active'] ?? 0));
        Session::flash('success', 'Estado actualizado.');
        $this->redirect('/users');
    }

    /** @param array<string, mixed> $extra */
    private function renderIndex(array $extra = []): void
    {
        $q = trim((string) ($_GET['q'] ?? ''));
        $status = (string) ($_GET['status'] ?? 'all');
        if (!in_array($status, ['all', 'active', 'inactive'], true)) {
            $status = 'all';
        }

        $users = (new UserModel())->listForTenant(
            $this->tenantId(),
            $q !== '' ? $q : null,
            $status === 'all' ? null : $status
        );

        $rows = [];
        foreach ($users as $u) {
            $rows[] = [
                $u['username'] ?? '',
                $u['name'],
                $u['email'],
                $u['role_name'],
                $u['is_active'] ? 'Activo' : 'Inactivo',
            ];
        }

        $this->maybeExportList(
            'Usuarios',
            ['Usuario', 'Nombre', 'Email', 'Rol', 'Estado'],
            $rows,
            'usuarios'
        );

        $this->view('users/index', array_merge([
            'title' => 'Usuarios',
            'users' => $users,
            'roles' => $this->roles(),
            'q' => $q,
            'status' => $status,
        ], $extra));
    }

    /** @return list<array<string, mixed>> */
    private function roles(): array
    {
        return Database::connection()->query('SELECT id, name, slug FROM roles ORDER BY id')->fetchAll();
    }

    /** @return array{title: string, subtitle: string, closeUrl: string} */
    private function modalMeta(string $title, string $subtitle): array
    {
        return [
            'title' => $title,
            'subtitle' => $subtitle,
            'closeUrl' => url('/users'),
        ];
    }
}
