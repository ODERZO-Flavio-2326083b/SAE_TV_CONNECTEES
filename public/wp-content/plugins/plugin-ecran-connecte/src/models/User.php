<?php

namespace models;

use JsonSerializable;
use PDO;
use WP_User;

/**
 * Class User
 *
 * Cete classe gère la gestion des utilisateurs vis à vis de la base de données.
 *
 * @category Entity
 * @package  Models
 * @author   BUT Informatique, AMU <iut-aix-scol@univ-amu.fr>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @version  Release: 1.0.0
 * @link     https://www.example.com/docs/User Documentation de la classe
 * @since    2025-01-07
 */
class User extends Model implements Entity, JsonSerializable
{


    /**
     * Identifiant unique de l'utilisateur.
     *
     * Cette propriété contient l'ID unique de l'utilisateur dans la base de données.
     *
     * @var int
     */
    private int $_id;

    /**
     * Identifiant de connexion de l'utilisateur.
     *
     * Cette propriété contient le nom d'utilisateur
     * utilisé pour se connecter à l'application.
     *
     * @var string
     */
    private string $_login;

    /**
     * Mot de passe de l'utilisateur.
     *
     * Cette propriété contient le mot de passe crypté
     * de l'utilisateur pour l'authentification.
     *
     * @var string
     */
    private string $_password;

    /**
     * Adresse e-mail de l'utilisateur.
     *
     * Cette propriété contient l'adresse e-mail associée à l'utilisateur.
     *
     * @var string
     */
    private string $_email;

    /**
     * Rôle de l'utilisateur dans l'application.
     *
     * Cette propriété définit le rôle de l'utilisateur parmi les valeurs possibles :
     * 'television', 'secretaire', ou 'technicien'.
     * Chaque rôle a des permissions spécifiques dans l'application.
     *
     * @var string (television | secretaire | technicien)
     */
    private string $_role;

    /**
     * Liste des codes associés à l'utilisateur.
     *
     * Cette propriété contient un tableau d'objets
     * 'CodeAde', qui représentent les codes associés
     * à l'utilisateur pour des actions spécifiques.
     *
     * @var CodeAde[]
     */
    private array $_codes;

    /**
     * Identifiant du département auquel l'utilisateur appartient.
     *
     * Cette propriété contient l'ID du département assigné à l'utilisateur.
     * L'ID est utilisé pour lier l'utilisateur à un département dans l'application.
     *
     * @var int
     */
    private int $_id_department;


    /**
     * Vitesse de défilement de l'emploi du temps
     *
     * Cette propriété contient la vitesse
     *
     * @var array
     */
    private array $metadata;


    /**
     * Insère un nouvel utilisateur avec un rôle spécifique et, le cas échéant,
     * associe des codes à cet utilisateur. Cette méthode utilise la fonction
     * 'wp_insert_user' pour créer l'utilisateur avec les données appropriées
     * (login, mot de passe, email, rôle).
     *
     * Si le rôle de l'utilisateur est 'television', des codes spécifiques
     * sont ajoutés à la table 'ecran_code_user'.
     *
     * @return int|\WP_Error L'ID de l'utilisateur créé en cas de succès.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function insert(): int|\WP_Error
    {
        $userData = array(
            'user_login' => $this->getLogin(),
            'user_pass' => $this->getPassword(),
            'user_email' => $this->getEmail(),
            'role' => $this->getRole(),
        );
        $id = wp_insert_user($userData);
        if ($this->getRole() == 'television') {
            foreach ($this->getCodes() as $code) {
                $request = $this->getDatabase()->prepare(
                    'INSERT INTO ecran_code_user (user_id, code_ade_id) 
                     VALUES (:userId, :codeAdeId)'
                );
                $request->bindParam(':userId', $id, PDO::PARAM_INT);
                $request->bindValue(':codeAdeId', $code->getId(), PDO::PARAM_INT);
                $request->execute();

                foreach ($this->metadata as $key => $value) {
                    add_user_meta($id, $key, $value, true);
                }            }
        }
        $database = $this->getDatabase();
        $request = $database->prepare(
            'INSERT INTO ecran_user_departement (dept_id, user_id) 
             VALUES (:dept_id, :user_id)'
        );
        $request->bindValue(':dept_id', $this->getIdDepartment());
        $request->bindParam(':user_id', $id, PDO::PARAM_INT);
        $request->execute();

        return $id;
    }

    /**
     * Met à jour le mot de passe d'un utilisateur dans la base de données.
     * Si le rôle de l'utilisateur est 'enseignant' ou 'directeuretude',
     * seul le code associé est mis à jour. Sinon, les anciens codes sont
     * supprimés de la table 'ecran_code_user' avant d'ajouter les nouveaux
     * codes.
     *
     * @return int Le nombre de lignes affectées par la mise à jour, indiquant
     *             si l'opération a réussi.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function update(): int
    {
        $database = $this->getDatabase();
        $request = $database->prepare(
            'UPDATE wp_users 
             SET user_pass = :password 
             WHERE ID = :id'
        );
        $request->bindValue(':id', $this->getId(), PDO::PARAM_INT);
        $request->bindValue(':password', $this->getPassword(), PDO::PARAM_STR);
        $request->execute();
        $request = $database->prepare(
            'DELETE FROM ecran_code_user 
             WHERE user_id = :id'
        );
        $request->bindValue(':id', $this->getId(), PDO::PARAM_INT);
        $request->execute();
        foreach ($this->getCodes() as $code) {
            if ($code instanceof CodeAde && !is_null($code->getId())) {
                $request = $database->prepare(
                    'INSERT INTO ecran_code_user (user_id, code_ade_id) 
                     VALUES (:userId, :codeAdeId)'
                );
                $request->bindValue(':userId', $this->getId(), PDO::PARAM_INT);
                $request->bindValue(':codeAdeId', $code->getId(), PDO::PARAM_INT);
                $request->execute();
            }
        }
        return $request->rowCount();
    }

    /**
     * Supprime un utilisateur de la base de données ainsi que toutes les
     * informations associées dans la table 'wp_usermeta'. La méthode supprime
     * d'abord l'utilisateur de la table 'wp_users' et récupère le nombre de lignes
     * affectées. Ensuite, elle supprime toutes les métadonnées associées à cet
     * utilisateur dans 'wp_usermeta'.
     *
     * @return int Le nombre de lignes supprimées dans la table 'wp_users', indiquant
     *             si l'utilisateur a été trouvé et supprimé avec succès.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function delete(): int
    {
        $database = $this->getDatabase();
        $request = $database->prepare(
            'DELETE FROM wp_users 
                                       WHERE ID = :id'
        );
        $request->bindValue(':id', $this->getId(), PDO::PARAM_INT);
        $request->execute();
        $count = $request->rowCount();
        $request = $database->prepare(
            'DELETE FROM wp_usermeta 
                                       WHERE user_id = :id'
        );
        $request->bindValue(':id', $this->getId(), PDO::PARAM_INT);
        $request->execute();
        return $count;
    }

    /**
     * Récupère les informations d'un utilisateur à partir de la base de données
     * en utilisant son ID. La méthode exécute une requête SQL pour sélectionner
     * l'utilisateur correspondant dans la table 'wp_users'. Si l'utilisateur est
     * trouvé, les données sont transformées en entité et renvoyées. Sinon, la
     * méthode retourne 'false'.
     *
     * @param int $id L'identifiant de l'utilisateur à récupérer.
     *
     * @return  false|User L'entité utilisateur si trouvée, sinon 'false'.
     * @version 1.0
     * @date    2024-10-15
     */
    public function get( $id ): false|User
    {
        $request = $this->getDatabase()->prepare(
            'SELECT wp.ID as ID, user_login, user_pass, user_email, 
       d.dept_id as dept_id 
             FROM wp_users wp LEFT JOIN ecran_user_departement d 
                 ON d.user_id = wp.ID 
             WHERE wp.ID = :id LIMIT 1'
        );
        $request->bindParam(':id', $id, PDO::PARAM_INT);
        $request->execute();
        if ($request->rowCount() > 0) {
            return $this->setEntity($request->fetch());
        }
        return false;
    }

    /**
     * Récupère une liste d'utilisateurs à partir de la base de données.
     * Cette méthode exécute une requête SQL pour sélectionner un certain
     * nombre d'utilisateurs à partir de la table 'wp_users' en
     * fonction de l'offset ('$begin') et du nombre d'éléments
     * ('$numberElement'). Les résultats sont renvoyés sous forme
     * d'une liste d'entités. Si aucun utilisateur n'est trouvé, un
     * tableau vide est retourné.
     *
     * @param int $begin         L'offset pour la pagination, par défaut
     *                           0.
     * @param int $numberElement Le nombre d'utilisateurs à récupérer, par défaut 25.
     *
     * @return array Une liste d'entités utilisateur, ou un tableau vide si aucun
     * utilisateur n'est trouvé.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function getList(int $begin = 0, int $numberElement = 25): array
    {
        $request = $this->getDatabase()->prepare(
            'SELECT ID, user_login, user_pass, user_email 
             FROM wp_users user JOIN wp_usermeta meta ON user.ID = meta.user_id 
                 LIMIT :begin, :numberElement'
        );
        $request->bindValue(':begin', $begin, PDO::PARAM_INT);
        $request->bindValue(':numberElement', $numberElement, PDO::PARAM_INT);
        $request->execute();
        if ($request->rowCount() > 0) {
            return $this->setEntityList($request->fetchAll());
        }
        return [];
    }

    /**
     * Récupère une liste d'utilisateurs ayant un rôle spécifique.
     * Cette méthode exécute une requête SQL pour sélectionner
     * les utilisateurs de la table 'wp_users' qui ont un rôle
     * donné dans la table 'wp_usermeta'. Les utilisateurs sont
     * triés par leur nom d'utilisateur ('user_login').
     * Jusqu'à 1000 utilisateurs peuvent être retournés.
     *
     * @param string $role Le rôle à filtrer dans la base de données.
     *
     * @return array Une liste d'entités utilisateur correspondant au rôle spécifié.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function getUsersByRole(string $role): array
    {
        $request = $this->getDatabase()->prepare(
            'SELECT wp.ID as ID, user_login, user_pass, user_email, d.dept_id 
             FROM wp_users wp 
                 JOIN wp_usermeta meta ON wp.ID = meta.user_id
                 JOIN ecran_user_departement d ON d.user_id = wp.ID 
                                                      AND meta.meta_value = :role 
             ORDER BY wp.user_login LIMIT 1000'
        );
        $size = strlen($role);
        $role = 'a:1:{s:' . $size . ':"' . $role . '";b:1;}';
        $request->bindParam(':role', $role, PDO::PARAM_STR);
        $request->execute();
        return $this->setEntityList($request->fetchAll());
    }

    /**
     * Récupère les codes associés à une liste d'utilisateurs.
     * Cette méthode parcourt chaque utilisateur fourni et exécute
     * une requête SQL pour obtenir les codes associés à chaque utilisateur
     * dans les tables 'ecran_code_ade' et 'ecran_code_user'.
     * Les résultats sont ensuite attribués à chaque utilisateur sous forme
     * d'une liste de codes.
     *
     * @param array $users Un tableau d'objets utilisateur pour lesquels
     *                     les codes doivent être récupérés.
     *
     * @return array Un tableau d'objets utilisateur mis à jour avec leurs
     *               codes associés.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function getMyCodes(array $users): array
    {
        foreach ($users as $user) {
            $request = $this->getDatabase()->prepare(
                'SELECT code.id, type, title, code, dept_id
                 FROM ecran_code_ade code, ecran_code_user user
                 WHERE user.user_id = :id AND user.code_ade_id = code.id
                 ORDER BY code.id LIMIT 100'
            );
            $id = $user->getId();
            $request->bindParam(':id', $id, PDO::PARAM_INT);
            $request->execute();
            $code = new CodeAde();
            if ($request->rowCount() <= 0) {
                $codes = [];
            } else {
                $codes = $code->setEntityList($request->fetchAll());
            }
            $user->setCodes($codes);
        }
        return $users;
    }

    /**
     * Vérifie si un utilisateur existe en fonction de son nom d'utilisateur ou de
     * son email.
     * Cette méthode exécute une requête SQL pour rechercher un utilisateur
     * dans la table 'wp_users' en utilisant le nom d'utilisateur ou l'email fournis.
     * Elle renvoie une liste d'utilisateurs correspondant aux critères de recherche.
     *
     * @param string $login Le nom d'utilisateur à vérifier.
     * @param string $email L'email à vérifier.
     *
     * @return array Un tableau d'objets utilisateur correspondants, ou un tableau
     *               vide
     *               si aucun utilisateur n'est trouvé.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function checkUser(string $login, string $email): array
    {
        $request = $this->getDatabase()->prepare(
            'SELECT wp.ID as ID, user_login, user_pass, user_email, 
       d.dept_id as dept_id 
             FROM wp_users wp
                 JOIN ecran_user_departement d ON d.user_id = wp.ID
             WHERE user_login = :login OR user_email = :email 
             LIMIT 2'
        );
        $request->bindParam(':login', $login, PDO::PARAM_STR);
        $request->bindParam(':email', $email, PDO::PARAM_STR);
        $request->execute();
        return $this->setEntityList($request->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * Récupère la liste des utilisateurs associés à un code spécifique.
     * Cette méthode exécute une requête SQL pour récupérer les détails des
     * utilisateurs dans la table 'wp_users' qui sont liés à un code dans la table
     * 'ecran_code_user'.
     *
     * @return array Un tableau d'objets utilisateur correspondants, ou un tableau
     *               vide si aucun utilisateur n'est lié au code.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function getUserLinkToCode() : array
    {
        $request = $this->getDatabase()->prepare(
            'SELECT ID, user_login, user_pass, user_email 
             FROM ecran_code_user 
                 JOIN wp_users ON ecran_code_user.user_id = wp_users.ID 
             WHERE user_id = :userId LIMIT 300'
        );
        $request->bindValue(':id_user', $this->getId(), PDO::PARAM_INT);
        $request->execute();
        return $this->setEntityList($request->fetchAll());
    }

    /**
     * Crée un nouveau code de suppression de compte pour un utilisateur.
     * Cette méthode insère un enregistrement dans la table
     * 'ecran_code_delete_account' en associant l'ID de l'utilisateur à un code
     * fourni.
     *
     * @param string $code Le code à associer à l'utilisateur pour la suppression de
     *                     compte.
     *
     * @return void
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function createCode($code)
    {
        $request = $this->getDatabase()->prepare(
            'INSERT INTO ecran_code_delete_account (user_id, code) 
             VALUES (:user_id, :code)'
        );
        $request->bindValue(':user_id', $this->getId(), PDO::PARAM_INT);
        $request->bindParam(':code', $code, PDO::PARAM_STR);
        $request->execute();
    }

    /**
     * Met à jour le code de suppression de compte associé à un utilisateur.
     * Cette méthode modifie l'enregistrement existant dans la table
     * 'ecran_code_delete_account' en remplaçant l'ancien code par le nouveau code
     * fourni.
     *
     * @param string $code Le nouveau code à associer à l'utilisateur pour la
     *                     suppression de compte.
     *
     * @return void
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function updateCode($code)
    {
        $request = $this->getDatabase()->prepare(
            'UPDATE ecran_code_delete_account 
             SET code = :code 
             WHERE user_id = :id'
        );
        $request->bindValue(':id', $this->getId(), PDO::PARAM_INT);
        $request->bindParam(':code', $code, PDO::PARAM_STR);
        $request->execute();
    }

    /**
     * Supprime le code de suppression de compte associé à un utilisateur.
     * Cette méthode efface l'enregistrement de la table 'ecran_code_delete_account'
     * correspondant à l'identifiant de l'utilisateur spécifié.
     *
     * @return int Le nombre de lignes affectées par la requête DELETE.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function deleteCode(): int
    {
        $request = $this->getDatabase()->prepare(
            'DELETE 
             FROM ecran_code_delete_account 
             WHERE user_id = :id'
        );
        $request->bindValue(':id', $this->getId(), PDO::PARAM_INT);
        $request->execute();
        return $request->rowCount();
    }

    /**
     * Récupère le code de suppression de compte associé à l'utilisateur.
     * Cette méthode effectue une requête pour obtenir le code stocké dans
     * la table 'ecran_code_delete_account' en fonction de l'identifiant de
     * l'utilisateur.
     *
     * @return string|null Le code de suppression de compte si trouvé, sinon null.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function getCodeDeleteAccount(): ?string
    {
        $request = $this->getDatabase()->prepare(
            'SELECT code 
             FROM ecran_code_delete_account 
             WHERE user_id = :id 
             LIMIT 1'
        );
        $request->bindValue(':id', $this->getId(), PDO::PARAM_INT);
        $request->execute();
        $result = $request->fetch();
        return $result['code'];
    }

    /**
     * Configure un objet User avec les données fournies.
     * Cette méthode crée une nouvelle instance de l'utilisateur et l'initialise
     * avec les informations de la base de données. Elle récupère également les
     * codes associés à cet utilisateur et les organise en fonction de leur type.
     *
     * @param array $data Tableau associatif contenant les données de l'utilisateur.
     *                    Les clés attendues incluent 'ID', 'user_login',
     *                    'user_pass', 'user_email', et d'autres selon la structure
     *                    de la base de données.
     *
     * @return User L'instance de l'utilisateur configurée avec les données.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function setEntity($data) : User
    {
        $entity = new User();
        $entity->setId($data['ID']);
        $entity->setLogin($data['user_login']);
        $entity->setPassword($data['user_pass']);
        $entity->setEmail($data['user_email']);
        $entity->setRole(get_user_by('ID', $data['ID'])->roles[0]);
        $entity->setIdDepartment(($data['dept_id']) ?: 0);
        $request = $this->getDatabase()->prepare(
            'SELECT id, title, code, type, dept_id
             FROM ecran_code_ade 
                 JOIN ecran_code_user ON ecran_code_ade.id = 
                                         ecran_code_user.code_ade_id 
             WHERE ecran_code_user.user_id = :id'
        );
        $request->bindValue(':id', $data['ID']);
        $request->execute();
        $codeAde = new CodeAde();
        $codes = $codeAde->setEntityList($request->fetchAll());
        $entity->setCodes($codes);
        return $entity;
    }


    /**
     * Configure une liste d'entités utilisateur à partir des données fournies.
     * Cette méthode prend un tableau de données, crée des instances d'utilisateurs
     * en utilisant la méthode setEntity pour chaque entrée, et renvoie un tableau
     * d'entités utilisateurs configurées.
     *
     * @param array $dataList Tableau contenant les données des utilisateurs à
     *                        traiter.
     *
     * @return array Un tableau d'instances d'utilisateur configurées.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function setEntityList($dataList) : array
    {
        $listEntity = array();
        foreach ($dataList as $data) {
            $listEntity[] = $this->setEntity($data);
        }
        return $listEntity;
    }

    /**
     * Récupère l'identifiant de l'utilisateur.
     *
     * Cette méthode retourne l'ID unique de l'utilisateur dans la base de données.
     *
     * @return int L'ID de l'utilisateur.
     */
    public function getId() : int
    {
        return $this->_id;
    }

    /**
     * Définit l'identifiant de l'utilisateur.
     *
     * Cette méthode permet de définir l'ID de l'utilisateur.
     *
     * @param int $_id L'identifiant de l'utilisateur.
     *
     * @return void
     */
    public function setId($_id) : void
    {
        $this->_id = $_id;
    }

    /**
     * Récupère le nom d'utilisateur (login).
     *
     * Cette méthode retourne le nom d'utilisateur de l'utilisateur.
     *
     * @return string Le nom d'utilisateur.
     */
    public function getLogin() : string
    {
        return $this->_login;
    }

    /**
     * Définit le nom d'utilisateur (login).
     *
     * Cette méthode permet de définir le nom d'utilisateur pour l'utilisateur.
     *
     * @param string $_login Le nom d'utilisateur.
     *
     * @return void
     */
    public function setLogin($_login) : void
    {
        $this->_login = $_login;
    }

    /**
     * Récupère le mot de passe de l'utilisateur.
     *
     * Cette méthode retourne le mot de passe de l'utilisateur, généralement crypté.
     *
     * @return string Le mot de passe de l'utilisateur.
     */
    public function getPassword() : string
    {
        return $this->_password;
    }

    /**
     * Définit le mot de passe de l'utilisateur.
     *
     * Cette méthode permet de définir le mot de passe de l'utilisateur.
     *
     * @param string $_password Le mot de passe de l'utilisateur.
     *
     * @return void
     */
    public function setPassword($_password) : void
    {
        $this->_password = $_password;
    }

    /**
     * Récupère l'adresse e-mail de l'utilisateur.
     *
     * Cette méthode retourne l'adresse e-mail de l'utilisateur.
     *
     * @return string L'adresse e-mail de l'utilisateur.
     */
    public function getEmail() : string
    {
        return $this->_email;
    }

    /**
     * Définit l'adresse e-mail de l'utilisateur.
     *
     * Cette méthode permet de définir l'adresse e-mail de l'utilisateur.
     *
     * @param string $_email L'adresse e-mail de l'utilisateur.
     *
     * @return void
     */
    public function setEmail($_email) : void
    {
        $this->_email = $_email;
    }

    /**
     * Récupère le rôle de l'utilisateur.
     *
     * Cette méthode retourne le rôle de l'utilisateur.
     * Le rôle détermine les permissions de l'utilisateur.
     *
     * @return string Le rôle de l'utilisateur.
     */
    public function getRole() : string
    {
        return $this->_role;
    }

    /**
     * Définit le rôle de l'utilisateur.
     *
     * Cette méthode permet de définir le rôle de l'utilisateur,
     * qui peut être 'television', 'secretaire', ou 'technicien'.
     *
     * @param string $_role Le rôle de l'utilisateur.
     *
     * @return void
     */
    public function setRole(string $_role): void
    {
        $this->_role = $_role;
    }

    /**
     * Récupère les codes associés à l'utilisateur.
     *
     * Cette méthode retourne un tableau d'objets 'CodeAde' associés à l'utilisateur.
     *
     * @return CodeAde[] Un tableau de codes associés à l'utilisateur.
     */
    public function getCodes(): array
    {
        return $this->_codes;
    }

    /**
     * Définit les codes associés à l'utilisateur.
     *
     * Cette méthode permet de définir un tableau de codes associés à l'utilisateur.
     *
     * @param CodeAde[] $_codes Un tableau d'objets 'CodeAde'.
     *
     * @return void
     */
    public function setCodes(array $_codes): void
    {
        $this->_codes = $_codes;
    }

    /**
     * Récupère l'identifiant du département auquel l'utilisateur appartient.
     *
     * Cette méthode retourne l'ID du département de l'utilisateur.
     *
     * @return int L'ID du département de l'utilisateur.
     */
    public function getIdDepartment(): int
    {
        return $this->_id_department;
    }

    /**
     * Définit l'identifiant du département auquel l'utilisateur appartient.
     *
     * Cette méthode permet de définir l'ID du département de l'utilisateur.
     *
     * @param int $id_department L'identifiant du département.
     *
     * @return void
     */
    public function setIdDepartment(int $id_department): void
    {
        $this->_id_department = $id_department;
    }

    public function getMetadata()
    {
        return $this->metadata;
    }

    public function setMetadata($metadata): void
    {
        $this->metadata = $metadata;
    }

    public function addMetadata(string $key, mixed $value){
        $this->metadata[$key] = $value;
    }


    /**
     * Convertit l'objet en un tableau associatif pour la sérialisation JSON.
     *
     * Cette méthode définit quelles propriétés de l'objet doivent être incluses dans
     * la sortie JSON lorsqu'on utilise json_encode().
     *
     * @return array Un tableau associatif des propriétés à inclure dans le JSON.
     */
    public function jsonSerialize(): array
    {
        return array(
            'id' => $this->_id,
            'name' => $this->_login
        );
    }
}
