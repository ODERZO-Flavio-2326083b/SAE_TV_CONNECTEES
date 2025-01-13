<?php
/**
 * Fichier InputValidator.php
 *
 * Ce fichier contient la classe 'InputValidator', qui est responsable
 * de la validation des entrées utilisateur dans les formulaires.
 * Elle permet de vérifier si les valeurs saisies respectent certains critères
 * (comme la validité d'une adresse email, un mot de passe assez complexe, etc.).
 * Cette classe facilite la gestion de la validation des formulaires et assure
 * que les données envoyées sont sécurisées avant d'être traitées.
 *
 * PHP version 7.4 or later
 *
 * @category Util
 * @package  Utils
 * @author   BUT Informatique, AMU <iut-aix-scol@univ-amu.fr>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @version  GIT: abcd1234abcd5678efgh9012ijkl3456mnop6789
 * @link     https://www.example.com/docs/InputValidator
 * Documentation de la classe
 * @since    2025-01-07
 */

namespace utils;

/**
 * Class InputValidator
 *
 * Classe permettant de vérifier la validité de certains inputs utilisateur
 * dans les formulaires. Elle fournit des méthodes pour valider les données,
 * comme les emails, les mots de passe, les noms, etc. Ces validations garantissent
 * que les données envoyées par les utilisateurs sont sécurisées et conformes aux
 * exigences du système.
 *
 * @category Util
 * @package  Utils
 * @author   BUT Informatique, AMU <iut-aix-scol@univ-amu.fr>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @version  Release: 1.0.0
 * @link     https://www.example.com/docs/InputValidator Documentation de la classe
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
     * @return bool Retourne 'true' si le login est valide, sinon 'false'.
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
     * Utilise la fonction native de WordPress 'is_email()' pour valider si
     * l'email respecte le format attendu d'une adresse email.
     *
     * @param mixed $email L'adresse email à valider.
     *
     * @return bool Retourne 'true' si l'email est valide, sinon 'false'.
     */
    public static function isValidEmail( mixed $email ): bool
    {
        return is_email($email);
    }
}
