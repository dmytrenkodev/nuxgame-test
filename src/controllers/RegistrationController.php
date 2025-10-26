<?php

namespace Controllers;

use Core\Controller;
use Core\Helpers;
use DateTime;
use Random\RandomException;

class RegistrationController extends Controller
{
    /**
     * @return void
     * @throws RandomException
     */
    public function handleRequest(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $phone = trim($_POST['phone'] ?? '');

            if (!$username || !$phone) {
                $this->renderError("Fill all fields.");
                return;
            }

            // base "validation"
            $cleanPhone = preg_replace('/\D/', '', $phone);
            if (strlen($cleanPhone) < 9 || strlen($cleanPhone) > 15) {
                $this->renderError("Phone number must be 9-15 digits.");
                return;
            }

            $token = Helpers::generateToken();
            $expires = (new DateTime('+7 days'))->format('Y-m-d H:i:s');

            $stmt = $this->db->prepare("
                INSERT INTO users (username, phone, token, expires_at)
                VALUES (:username, :phone, :token, :expires_at)
            ");

            $stmt->execute([
                'username' => $username,
                'phone' => $cleanPhone,
                'token' => $token,
                'expires_at' => $expires,
            ]);

            $link = Helpers::generateLink($token);
            $this->renderSuccess($link);
        } else {
            $this->renderForm();
        }
    }

    private function renderForm(): void
    {
        echo <<<HTML
        <h2>Register</h2>
        <form method="POST">
            <label>Username:<br><input type="text" name="username" required></label><br><br>
            <label>Phone:<br><input type="text" name="phone" required></label><br><br>
            <button type="submit">Register</button>
        </form>
        HTML;
    }

    private function renderSuccess(string $link): void
    {
        echo "<p>Unique link (valid 7 days): <a href='{$link}'>{$link}</a></p>";
    }

    private function renderError(string $message): void
    {
        echo "<p style='color:red;'>{$message}</p>";
        $this->renderForm();
    }
}
