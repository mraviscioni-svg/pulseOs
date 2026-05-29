<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Models\UserModel;

final class PasswordResetService
{
    public function request(string $username): ?string
    {
        $username = normalize_username($username);
        $user = (new UserModel())->findByUsernameGlobal($username);
        if (!$user || empty($user['email'])) {
            return null;
        }

        $email = (string) $user['email'];
        $token = bin2hex(random_bytes(32));
        $db = Database::connection();
        $db->prepare('DELETE FROM password_resets WHERE email = :email')->execute(['email' => $email]);
        $stmt = $db->prepare(
            'INSERT INTO password_resets (email, token, expires_at) VALUES (:email, :token, DATE_ADD(NOW(), INTERVAL 2 HOUR))'
        );
        $stmt->execute(['email' => $email, 'token' => hash('sha256', $token)]);

        $resetUrl = url('/reset-password/' . $token);
        $this->sendMail($email, $resetUrl);

        return $resetUrl;
    }

    public function reset(string $plainToken, string $newPassword): bool
    {
        $hash = hash('sha256', $plainToken);
        $db = Database::connection();
        $stmt = $db->prepare(
            'SELECT * FROM password_resets WHERE token = :token AND expires_at > NOW() ORDER BY id DESC LIMIT 1'
        );
        $stmt->execute(['token' => $hash]);
        $row = $stmt->fetch();
        if (!$row) {
            return false;
        }

        $user = (new UserModel())->findByEmailGlobal($row['email']);
        if (!$user) {
            return false;
        }

        $upd = $db->prepare('UPDATE users SET password = :password WHERE id = :id');
        $upd->execute([
            'password' => password_hash($newPassword, PASSWORD_DEFAULT),
            'id' => $user['id'],
        ]);
        $db->prepare('DELETE FROM password_resets WHERE email = :email')->execute(['email' => $row['email']]);

        return true;
    }

    private function sendMail(string $to, string $resetUrl): void
    {
        $from = $_ENV['MAIL_FROM'] ?? 'noreply@localhost';
        $app = app_name();
        $subject = 'Recuperar contraseña — ' . $app;
        $body = "Hola,\n\nUsá este enlace para restablecer tu contraseña (válido 2 horas):\n{$resetUrl}\n\n— {$app}";
        $headers = 'From: ' . $from . "\r\nContent-Type: text/plain; charset=UTF-8";
        @mail($to, $subject, $body, $headers);
    }
}
