<?php

namespace Controllers;

use Core\Controller;
use Core\Helpers;
use DateTime;
use JetBrains\PhpStorm\NoReturn;
use Random\RandomException;

class PageController extends Controller
{
    private int $userId;
    private array $user;

    private const LUCKY_RULES = [
        900 => 0.7,
        600 => 0.5,
        300 => 0.3,
        0   => 0.1, // less than 300
    ];

    private const MAX_RANDOM = 1000;

    /**
     * @param string $token
     */
    public function __construct(string $token)
    {
        parent::__construct();

        $stmt = $this->db->prepare(
            "SELECT * FROM users WHERE token = :token AND active = 1 AND expires_at >= NOW()"
        );

        $stmt->execute(['token' => $token]);
        $user = $stmt->fetch();

        if (!$user) {
            die("Not valid link!");
        }

        $this->user = $user;
        $this->userId = (int)$user['id'];
    }

    /**
     * @return void
     * @throws RandomException
     */
    public function handleRequest(): void
    {
        $action = $_POST['action'] ?? null;

        switch ($action) {
            case 'regenerate':
                $this->regenerateToken();
                break;
            case 'deactivate':
                $this->deactivateToken();
                break;
            case 'imfeelinglucky':
                $this->imFeelingLucky();
                break;
            case 'history':
                $this->showHistory();
                break;
        }

        $this->renderPage();
    }

    /**
     * @return void
     * @throws RandomException
     */
    private function regenerateToken(): void
    {
        $newToken = Helpers::generateToken();
        $expires = (new DateTime('+7 days'))->format('Y-m-d H:i:s');

        $stmt = $this->db->prepare("UPDATE users SET token = :token, expires_at = :expires WHERE id = :id");
        $stmt->execute([
            'token' => $newToken,
            'expires' => $expires,
            'id' => $this->userId,
        ]);

        $this->user['token'] = $newToken;
        $this->user['expires_at'] = $expires;
        $newLink = Helpers::generateLink($newToken);
        echo "<p>New unique link: <a href='" . $newLink . "'>$newLink</a></p>";
    }

    /**
     * @return void
     */
    #[NoReturn] private function deactivateToken(): void
    {
        $stmt = $this->db->prepare("UPDATE users SET active = 0 WHERE id = :id");
        $stmt->execute(['id' => $this->userId]);
        die("Link is successfully deactivated!");
    }

    /**
     * @return void
     * @throws RandomException
     */
    private function imFeelingLucky(): void
    {
        $num = random_int(1, self::MAX_RANDOM);
        $win = $num % 2 === 0;
        $amount = 0.0;

        if ($win) {
            foreach (self::LUCKY_RULES as $threshold => $percent) {
                if ($num > $threshold) {
                    $amount = $num * $percent;
                    break;
                }
            }
        }

        $stmt = $this->db->prepare(
            "INSERT INTO lucky_history (user_id, number, result, amount) VALUES (:uid, :num, :res, :amt)"
        );

        $stmt->execute([
            'uid' => $this->userId,
            'num' => $num,
            'res' => $win ? 'Win' : 'Lose',
            'amt' => $amount,
        ]);

        echo "<p>Random number is: $num<br>Result: " . ($win ? 'Win' : 'Lose') . "<br>Win amount: $amount</p>";
    }

    /**
     * @return void
     */
    private function showHistory(): void
    {
        $stmt = $this->db->prepare("
            SELECT number, result, amount, created_at 
            FROM lucky_history WHERE user_id = :uid ORDER BY created_at DESC LIMIT 3
        ");

        $stmt->execute(['uid' => $this->userId]);
        $rows = $stmt->fetchAll();

        if (!$rows) {
            echo "<p>Empty history</p>";
            return;
        }

        echo "<h3>Last 3 results:</h3><ul>";
        foreach ($rows as $row) {
            echo "<li>{$row['created_at']}: number={$row['number']}, result={$row['result']}, amount={$row['amount']}</li>";
        }
        echo "</ul>";
    }

    private function renderPage(): void
    {
        $token = $this->user['token'];
        $link = Helpers::generateLink($token);

        echo <<<HTML
        <h2>Unique page for user: {$this->user['username']}</h2>
        <p>Unique link (valid until {$this->user['expires_at']}): <a href="$link">$link</a></p>

        <form method="POST">
            <button type="submit" name="action" value="regenerate">Regenerate Link</button>
            <button type="submit" name="action" value="deactivate">Deactivate Link</button>
            <button type="submit" name="action" value="imfeelinglucky">I'm Feeling Lucky</button>
            <button type="submit" name="action" value="history">History</button>
        </form>

        HTML;
    }
}
