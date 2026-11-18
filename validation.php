<?php
declare(strict_types=1);

function validate_name(string $value): string
{
    $v = trim($value);
    if ($v === '') return 'Full name is required.';
    $len = mb_strlen($v);
    if ($len < 2)  return 'Name must be at least 2 characters.';
    if ($len > 50) return 'Name must be 50 characters or fewer.';
    if (!preg_match('/^\p{L}+(?:[ \'\-]\p{L}+)*$/u', $v)) {
        return 'Name can only contain letters, spaces, hyphens and apostrophes.';
    }
    return '';
}

function validate_student_id(string $value): string
{
    $v = trim($value);
    if ($v === '') return 'Student ID is required.';
    if (!ctype_digit($v)) return 'Student ID must contain digits only.';
    if (!preg_match('/^\d{8}$/', $v)) return 'Student ID must be exactly 8 digits.';
    return '';
}

function validate_email(string $value): string
{
    $v = trim($value);
    if ($v === '') return 'Email is required.';
    if (strlen($v) > 254 || !filter_var($v, FILTER_VALIDATE_EMAIL)) {
        return 'Enter a valid email address.';
    }
    return '';
}

function validate_password(string $value): string
{
    if ($value === '') return 'Password is required.';
    if (strlen($value) < 8)                    return 'Password must be at least 8 characters.';
    if (!preg_match('/[a-z]/', $value))        return 'Add at least one lowercase letter.';
    if (!preg_match('/[A-Z]/', $value))        return 'Add at least one uppercase letter.';
    if (!preg_match('/\d/', $value))           return 'Add at least one number.';
    if (!preg_match('/[^A-Za-z0-9]/', $value)) return 'Add at least one symbol (e.g. !@#$).';
    return '';
}

function validate_confirm(string $value, string $original): string
{
    if ($value === '') return 'Please confirm your password.';
    if ($value !== $original) return 'Passwords do not match.';
    return '';
}

function validate_signup(array $in): array
{
    $password = (string) ($in['password'] ?? '');
    $checks = [
        'name'       => validate_name((string) ($in['name'] ?? '')),
        'student_id' => validate_student_id((string) ($in['student_id'] ?? '')),
        'password'   => validate_password($password),
        'confirm'    => validate_confirm((string) ($in['confirm'] ?? ''), $password),
    ];
    return array_filter($checks, fn($msg) => $msg !== '');
}