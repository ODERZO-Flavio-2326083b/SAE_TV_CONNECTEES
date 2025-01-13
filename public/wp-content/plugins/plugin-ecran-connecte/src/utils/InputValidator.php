<?php
/**
 * Fichier User.php
 *
 * Ce fichier contient la classe `User`, qui représente une entité utilisateur
 * dans l'application.
 * Cette classe permet de gérer les informations de l'utilisateur,
 * telles que son identifiant,
 * son nom, son email, et d'autres informations pertinentes.
 * Elle fournit également des méthodes pour insérer,
 * mettre à jour, supprimer et récupérer
 * des utilisateurs dans la base de données.
 *
 * PHP version 7.4 or later
 *
 * @category Entity
 * @package  Models
 * @author   John Doe <johndoe@example.com>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @version  GIT: abcd1234abcd5678efgh9012ijkl3456mnop6789
 * @link     https://www.example.com/docs/User
 * Documentation de la classe
 * @since    2025-01-07
 */
namespace utils;


/**
 * Class User
 *
 * Représente une entité utilisateur dans l'application. Cette classe permet
 * de gérer les informations de l'utilisateur, telles que son identifiant,
 * son nom, son email et d'autres informations pertinentes. Elle fournit
 * des méthodes pour insérer, mettre à jour, supprimer et récupérer des utilisateurs
 * dans la base de données.
 *
 * @category Entity
 * @package  Models
 * @author   John Doe <johndoe@example.com>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @version  Release: 1.0.0
 * @link     https://www.example.com/docs/User Documentation de la classe
 * @since    2025-01-07
 */
class InputValidator
{


    /**
     * Valide si un login est une chaîne de caractères valide.
     *
     * Un login est valide s'il est une chaîne de caractères de longueur
     * comprise entre 4 et 25 caractères inclus.
     *
     * @param mixed $login Le login à valider.
     *
     * @return bool Retourne `true` si le login est valide, sinon `false`.
     */
    public static function isValidLogin( mixed $login ): bool
    {
        return    is_string($login)
               && strlen($login) >= 4
               && strlen($login) <= 25;
    }

    /**
     * Vérifie la validité des mots de passe.
     *
     * @param mixed $password        mot de passe
     * @param mixed $passwordConfirm confirmation du mot de passe
     *
     * @return bool true ou false, si les mots de passes sont corrects
     */
    public static function isValidPassword( mixed $password,
        mixed $passwordConfirm 
    ): bool {
        return    is_string($password)
               && strlen($password) >= 8
               && strlen($password) <= 25
               && $password === $passwordConfirm;
    }

    /**
     * Vérifie la validité de l'email.
     *
     * Utilise la fonction native de WordPress `is_email()` pour valider si
     * l'email respecte le format attendu d'une adresse email.
     *
     * @param mixed $email L'adresse email à valider.
     *
     * @return bool Retourne `true` si l'email est valide, sinon `false`.
     */
    public static function isValidEmail( mixed $email ): bool
    {
        return is_email($email);
    }
}
