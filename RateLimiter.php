<?php
declare(strict_types=1);
namespace App;

class RateLimiter {
    private int $decaySeconds;
    private int $maxAttempts;
    private string$key;

    public function __construct(string $key, int $maxAttempts = 5, int $decaySeconds = 60) {
        $this->maxAttempts = $maxAttempts;
        $this->decaySeconds = $decaySeconds;
        $this->key = $key;
    }

    public  function attempt(): bool {
        $data = $_SESSION['rate_limits'][$this->key] ?? null;
        $now = time();

        // Reset if window has expired
        if ($data && ($now - $data['start']) > $this->decaySeconds) {
            unset($_SESSION['rate_limits'][$this->key]);
            $data = null;
        }
     
        if (!$data) {
            $_SESSION['rate_limits'][$this->key] = ['attempts' => 1, 'start' => $now];
            return true;
        }

        $_SESSION['rate_limits'][$this->key]['attempts']++;

        return $_SESSION['rate_limits'][$this->key]['attempts'] <= $this->maxAttempts;
    }

    public function remainingSeconds(): int {
        $data = $_SESSION['rate_limits'][$this->key] ?? null;
        if (!$data) return 0;
        return max(0, $this->decaySeconds - (time() - $data['start']));
    }
}