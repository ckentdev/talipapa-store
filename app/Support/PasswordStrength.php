<?php

namespace App\Support;

class PasswordStrength
{
    public const MIN_LENGTH = 8;

    /**
     * @return array<string, array{label: string, regex: string|null}>
     */
    public static function hints(): array
    {
        return [
            'length' => [
                'label' => 'Minimum number of characters is '.self::MIN_LENGTH.'.',
                'regex' => null,
            ],
            'lowercase' => [
                'label' => 'Should contain lowercase.',
                'regex' => '/[a-z]/',
            ],
            'uppercase' => [
                'label' => 'Should contain uppercase.',
                'regex' => '/[A-Z]/',
            ],
            'number' => [
                'label' => 'Should contain numbers.',
                'regex' => '/[0-9]/',
            ],
            'special' => [
                'label' => 'Should contain special characters.',
                'regex' => '/[^A-Za-z0-9]/',
            ],
        ];
    }

    /**
     * @return array{score: int, level: string, label: string, checks: array<string, bool>}
     */
    public static function evaluate(string $password): array
    {
        $checks = self::runChecks($password);
        $score = count(array_filter($checks));

        return [
            'score' => $score,
            'level' => self::levelForScore($score),
            'label' => self::labelForScore($score),
            'checks' => $checks,
        ];
    }

    public static function isStrong(string $password): bool
    {
        $checks = self::runChecks($password);

        return count(array_filter($checks)) === count($checks);
    }

    /**
     * @return array<string, bool>
     */
    public static function runChecks(string $password): array
    {
        $checks = [];

        foreach (self::hints() as $key => $hint) {
            if ($key === 'length') {
                $checks[$key] = strlen($password) >= self::MIN_LENGTH;
            } else {
                $checks[$key] = (bool) preg_match($hint['regex'], $password);
            }
        }

        return $checks;
    }

    public static function levelForScore(int $score): string
    {
        return match (true) {
            $score === 0 => 'empty',
            $score <= 2 => 'weak',
            $score === 3 => 'fair',
            $score === 4 => 'good',
            default => 'strong',
        };
    }

    public static function labelForScore(int $score): string
    {
        return match (true) {
            $score === 0 => 'Empty',
            $score <= 2 => 'Weak',
            $score === 3 => 'Fair',
            $score === 4 => 'Good',
            default => 'Strong',
        };
    }
}
