<?php

namespace utils;

class InputValidator {
    public static function isValidLogin($login) {
        return    is_string($login)
               && strlen($login) >= 4
               && strlen($login) <= 25;
    }

    public static function isValidPassword($password, $passwordConfirm) {
        return    is_string($password)
               && strlen($password) >= 8
               && strlen($password) <= 25
               && $password === $passwordConfirm;
    }

    public static function isValidEmail($email) {
        return is_email($email);
    }
}