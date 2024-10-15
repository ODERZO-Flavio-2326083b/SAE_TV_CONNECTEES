<?php

namespace Models;

use JsonSerializable;
use PDO;
use WP_User;

/**
 * Class User
 *
 * User entity
 *
 * @package Models
 */
class User extends Model implements Entity, JsonSerializable
{

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $login;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string (Student | Teacher | Television | Secretary | Study Director | Technician)
     */
    private $role;

    /**
     * @var CodeAde[]
     */
    private $codes;

    /**
     * Insert an user in the database
     *
     * @return bool
     */
    public function insert() {
        // Take 7 lines to create an user with a specific role
        $userData = array(
            'user_login' => $this->getLogin(),
            'user_pass' => $this->getPassword(),
            'user_email' => $this->getEmail(),
            'role' => $this->getRole()
        );
        $id = wp_insert_user($userData);
        /*
        $id = wp_create_user($this->getLogin(), $this->getPassword(), $this->getEmail());
	    $user = new WP_User($id);
	    $user->add_role($this->getRole());
        */
        /*
        $request = $this->getDatabase()->prepare('INSERT INTO wp_users (user_login, user_pass, user_nicename, user_email, user_url, user_registered, user_activation_key, user_status, display_name) VALUES (:login, :password, :name, :email, :url, NOW(), :key, :status, :display_name)');

        $nul = " ";
        $zero = "0";

        $request->bindValue(':login', $this->getLogin(), PDO::PARAM_STR);
        $request->bindValue(':password', $this->getPassword(), PDO::PARAM_STR);
        $request->bindValue(':name', $this->getLogin(), PDO::PARAM_STR);
        $request->bindValue(':email', $this->getEmail(), PDO::PARAM_STR);
        $request->bindParam(':url', $nul);
        $request->bindParam(':key', $nul);
        $request->bindParam(':status', $zero, PDO::PARAM_INT);
        $request->bindValue(':display_name', $this->getLogin(), PDO::PARAM_STR);

        $request->execute();

        $id = $this->getDatabase()->lastInsertId();

        $capabilities = 'wp_capabilities';
        $role = 'a:1:{s:'.strlen($this->getRole()).':"'.$this->getRole().'";b:1;}';

        $request = $this->getDatabase()->prepare('INSERT INTO wp_usermeta(user_id, meta_key, meta_value) VALUES (:id, :capabilities, :role)');

        $request->bindParam(':id', $id, PDO::PARAM_INT);
        $request->bindParam(':capabilities', $capabilities, PDO::PARAM_STR);
        $request->bindParam(':role', $role, PDO::PARAM_STR);

        $request->execute();

        $level = "wp_user_level";

        $request = $this->getDatabase()->prepare('INSERT INTO wp_usermeta(user_id, meta_key, meta_value) VALUES (:id, :level, :value)');

        $request->bindParam(':id', $id, PDO::PARAM_INT);
        $request->bindParam(':level', $level, PDO::PARAM_STR);
        $request->bindParam(':value', $zero, PDO::PARAM_STR);

        $request->execute();
        */
        // To review
        if ($this->getRole() == 'television') {
            foreach ($this->getCodes() as $code) {

                $request = $this->getDatabase()->prepare('INSERT INTO ecran_code_user (user_id, code_ade_id) VALUES (:userId, :codeAdeId)');

                $request->bindParam(':userId', $id, PDO::PARAM_INT);
                $request->bindValue(':codeAdeId', $code->getId(), PDO::PARAM_INT);

                $request->execute();
            }
        } else if ($this->getRole() == 'enseignant' || $this->getRole() == 'directeuretude') {

            $codeAde = new CodeAde();

            $codeAde->setTitle($this->getLogin());
            $codeAde->setCode($this->getCodes());
            $codeAde->setType('teacher');

            $idCode = $codeAde->insert();

            $request = $this->getDatabase()->prepare('INSERT INTO ecran_code_user (user_id, code_ade_id) VALUES (:userId, :codeAdeId)');

            $request->bindParam(':userId', $id, PDO::PARAM_INT);
            $request->bindValue(':codeAdeId', $idCode, PDO::PARAM_INT);

            $request->execute();
        }
        return $id;
    }

    public function update() {
        $database = $this->getDatabase();
        $request = $database->prepare('UPDATE wp_users SET user_pass = :password WHERE ID = :id');

        $request->bindValue(':id', $this->getId(), PDO::PARAM_INT);
        $request->bindValue(':password', $this->getPassword(), PDO::PARAM_STR);

        $request->execute();

        if ($this->getRole() === 'enseignant' || $this->getRole() === 'directeuretude') {

            $this->getCodes()[0]->update();

        } else {
            $request = $database->prepare('DELETE FROM ecran_code_user WHERE user_id = :id');

            $request->bindValue(':id', $this->getId(), PDO::PARAM_INT);

            $request->execute();

            foreach ($this->getCodes() as $code) {
                if ($code instanceof CodeAde && !is_null($code->getId())) {
                    $request = $database->prepare('INSERT INTO ecran_code_user (user_id, code_ade_id) VALUES (:userId, :codeAdeId)');

                    $request->bindValue(':userId', $this->getId(), PDO::PARAM_INT);
                    $request->bindValue(':codeAdeId', $code->getId(), PDO::PARAM_INT);

                    $request->execute();
                }
            }
        }
        return $request->rowCount();
    }

    /**
     * Delete an user
     */
    public function delete() {
        $database = $this->getDatabase();
        $request = $database->prepare('DELETE FROM wp_users WHERE ID = :id');

        $request->bindValue(':id', $this->getId(), PDO::PARAM_INT);

        $request->execute();
        $count = $request->rowCount();

        $request = $database->prepare('DELETE FROM wp_usermeta WHERE user_id = :id');

        $request->bindValue(':id', $this->getId(), PDO::PARAM_INT);

        $request->execute();

        return $count;
    }

    /**
     * Get the user link to the id
     *
     * @param $id int
     *
     * @return User | false
     */
    public function get($id) {
        $request = $this->getDatabase()->prepare('SELECT ID, user_login, user_pass, user_email FROM wp_users WHERE ID = :id LIMIT 1');

        $request->bindParam(':id', $id, PDO::PARAM_INT);

        $request->execute();

        if ($request->rowCount() > 0) {
            return $this->setEntity($request->fetch());
        }
        return false;
        /*
        $author = get_user_by('id', $id);

        $this->setLogin($author->user_login);

        return $this;
        */
    }

    /**
     * @param int $begin
     * @param int $numberElement
     *
     * @return Information[]|void
     */
    public function getList($begin = 0, $numberElement = 25) {
        $request = $this->getDatabase()->prepare('SELECT ID, user_login, user_pass, user_email FROM wp_users user JOIN wp_usermeta meta ON user.ID = meta.user_id LIMIT :begin, :numberElement');

        $request->bindValue(':begin', (int)$begin, PDO::PARAM_INT);
        $request->bindValue(':numberElement', (int)$numberElement, PDO::PARAM_INT);

        $request->execute();

        if ($request->rowCount() > 0) {
            return $this->setEntityList($request->fetchAll());
        }
        return [];
    }

    /**
     *
     *
     * @param $role     string
     *
     * @return array
     */
    public function getUsersByRole($role) {
        $request = $this->getDatabase()->prepare('SELECT ID, user_login, user_pass, user_email  FROM wp_users user, wp_usermeta meta WHERE user.ID = meta.user_id AND meta.meta_value =:role ORDER BY user.user_login LIMIT 1000');

        $size = strlen($role);
        $role = 'a:1:{s:' . $size . ':"' . $role . '";b:1;}';

        $request->bindParam(':role', $role, PDO::PARAM_STR);

        $request->execute();

        return $this->setEntityList($request->fetchAll());
    }

    /**
     *
     */
    public function getMyCodes($users) {
        foreach ($users as $user) {
            $request = $this->getDatabase()->prepare('SELECT code.id, type, title, code FROM ecran_code_ade code, ecran_code_user user WHERE user.user_id = :id AND user.code_ade_id = code.id ORDER BY code.id LIMIT 100');

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
     * Check if an user got the same login or email
     *
     * @param $login
     * @param $email
     *
     * @return array|mixed
     */
    public function checkUser($login, $email) {
        $request = $this->getDatabase()->prepare('SELECT ID, user_login, user_pass, user_email FROM wp_users WHERE user_login = :login OR user_email = :email LIMIT 2');

        $request->bindParam(':login', $login, PDO::PARAM_STR);
        $request->bindParam(':email', $email, PDO::PARAM_STR);

        $request->execute();

        return $this->setEntityList($request->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * Give the link between the code ade and the user
     *
     * @return array
     */
    public function getUserLinkToCode() {
        $request = $this->getDatabase()->prepare('SELECT ID, user_login, user_pass, user_email FROM ecran_code_user JOIN wp_users ON ecran_code_user.user_id = wp_users.ID WHERE user_id = :userId LIMIT 300');

        $request->bindValue(':id_user', $this->getId(), PDO::PARAM_INT);

        $request->execute();

        return $this->setEntityList($request->fetchAll());
    }

    public function createCode($code) {
        $request = $this->getDatabase()->prepare('INSERT INTO ecran_code_delete_account (user_id, code) VALUES (:user_id, :code)');

        $request->bindValue(':user_id', $this->getId(), PDO::PARAM_INT);
        $request->bindParam(':code', $code, PDO::PARAM_STR);

        $request->execute();
    }

    public function updateCode($code) {
        $request = $this->getDatabase()->prepare('UPDATE ecran_code_delete_account SET code = :code WHERE user_id = :id');

        $request->bindValue(':id', $this->getId(), PDO::PARAM_INT);
        $request->bindParam(':code', $code, PDO::PARAM_STR);

        $request->execute();
    }

    public function deleteCode() {
        $request = $this->getDatabase()->prepare('DELETE FROM ecran_code_delete_account WHERE user_id = :id');

        $request->bindValue(':id', $this->getId(), PDO::PARAM_INT);

        $request->execute();

        return $request->rowCount();
    }

    public function getCodeDeleteAccount() {
        $request = $this->getDatabase()->prepare('SELECT code FROM ecran_code_delete_account WHERE user_id = :id LIMIT 1');

        $request->bindValue(':id', $this->getId(), PDO::PARAM_INT);

        $request->execute();

        $result = $request->fetch();

        return $result['code'];
    }

    /**
     * @inheritDoc
     */
    public function setEntity($data) {
        $entity = new User();

        $entity->setId($data['ID']);

        $entity->setLogin($data['user_login']);
        $entity->setPassword($data['user_pass']);
        $entity->setEmail($data['user_email']);
        $entity->setRole(get_user_by('ID', $data['ID'])->roles[0]);

        $request = $this->getDatabase()->prepare('SELECT id, title, code, type FROM ecran_code_ade JOIN ecran_code_user ON ecran_code_ade.id = ecran_code_user.code_ade_id WHERE ecran_code_user.user_id = :id');

        $request->bindValue(':id', $data['ID']);

        $request->execute();

        $codeAde = new CodeAde();

        $codes = $codeAde->setEntityList($request->fetchAll());

        $entity->setCodes($codes);

        if (function_exists('get_user_by')) {
            $user_info = get_user_by('id', $entity->getId());
            if (in_array('etudiant', $user_info->roles)) {
                $codesSort = ['', '', ''];
                foreach ($entity->getCodes() as $code) {
                    if ($code instanceof CodeAde) {
                        if ($code->getType() === 'year') {
                            $codesSort[0] = $code;
                        } else if ($code->getType() === 'group') {
                            $codesSort[1] = $code;
                        } else {
                            $codesSort[2] = $code;
                        }
                    }
                }
                $entity->setCodes($codesSort);
            }
        }
        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function setEntityList($dataList) {
        $listEntity = array();
        foreach ($dataList as $data) {
            $listEntity[] = $this->setEntity($data);
        }
        return $listEntity;
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getLogin() {
        return $this->login;
    }

    /**
     * @param $login
     */
    public function setLogin($login) {
        $this->login = $login;
    }

    /**
     * @return string
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     * @param $password
     */
    public function setPassword($password) {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * @param $email
     */
    public function setEmail($email) {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getRole() {
        return $this->role;
    }

    /**
     * @param $role
     */
    public function setRole($role) {
        $this->role = $role;
    }

    /**
     * @return CodeAde[]
     */
    public function getCodes() {
        return $this->codes;
    }

    /**
     * @param CodeAde[] $codes
     */
    public function setCodes($codes) {
        $this->codes = $codes;
    }

    public function jsonSerialize() {
        return array(
            'id' => $this->id,
            'name' => $this->login
        );
    }
}
