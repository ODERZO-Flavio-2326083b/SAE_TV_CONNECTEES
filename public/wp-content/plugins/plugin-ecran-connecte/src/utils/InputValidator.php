<?php

namespace utils;


/**
 * Classe permettant de vérifier la validité de certains inputs
 * utilisateurs dans les formulaires.
 */
class InputValidator {

    /**
     * Vérifie la validité du login.
     *
     * @param mixed $login
     *
     * @return bool true ou false, si le login est correct
     */
    public static function isValidLogin( mixed $login ): bool {
        return    is_string($login)
               && strlen($login) >= 4
               && strlen($login) <= 25;
    }

    /**
     * Vérifie la validité des mots de passe.
     *
     * @param mixed $password mot de passe
     * @param mixed $passwordConfirm confirmation du mot de passe
     *
     * @return bool true ou false, si les mots de passes sont corrects
     */
    public static function isValidPassword( mixed $password,
                                            mixed $passwordConfirm ): bool {
        return    is_string($password)
               && strlen($password) >= 8
               && strlen($password) <= 25
               && $password === $passwordConfirm;
    }

    /**
     * Vérifie la validité de l'email.
     *
     * @param mixed $email
     *
     * @return bool true ou false, si l'email est correct
     */
    public static function isValidEmail( mixed $email ): bool {
        return is_email($email);
    }
}