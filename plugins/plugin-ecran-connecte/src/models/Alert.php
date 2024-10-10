<?php

namespace Models;

use JsonSerializable;
use PDO;

/**
 * Class Alert
 *
 * Alert entity
 *
 * @package Models
 */
class Alert extends Model implements Entity, JsonSerializable
{

    /**
     * @var int
     */
    private $id;

    /**
     * @var User
     */
    private $author;

    /**
     * @var string
     */
    private $content;

    /**
     * @var string
     */
    private $creation_date;

    /**
     * @var string
     */
    private $expirationDate;

    /**
     * @var CodeAde[]
     */
    private $codes;

    /**
     * @var int
     */
    private $forEveryone;

    /**
     * @var int
     */
    private $adminId;


    /**
     * Add an alert in the database with today date and current user.
     */
    public function insert() {
        $database = $this->getDatabase();
        $request = $database->prepare('INSERT INTO ecran_alert (author, content, creation_date, expiration_date, for_everyone, administration_id) VALUES (:author, :content, :creation_date, :expirationDate, :for_everyone, :administrationId)');

        $request->bindValue(':author', $this->getAuthor(), PDO::PARAM_INT);
        $request->bindValue(':content', $this->getContent(), PDO::PARAM_STR);
        $request->bindValue(':creation_date', $this->getCreationDate(), PDO::PARAM_STR);
        $request->bindValue(':expirationDate', $this->getExpirationDate(), PDO::PARAM_STR);
        $request->bindValue(':for_everyone', $this->isForEveryone(), PDO::PARAM_INT);
        $request->bindValue(':administrationId', $this->getAdminId(), PDO::PARAM_INT);

        $request->execute();

        $id = $database->lastInsertId();

        foreach ($this->getCodes() as $code) {

            if ($code !== 'all' || $code !== 0) {
                $request = $database->prepare('INSERT INTO ecran_code_alert (alert_id, code_ade_id) VALUES (:idAlert, :idCodeAde)');

                $request->bindParam(':idAlert', $id, PDO::PARAM_INT);
                $request->bindValue(':idCodeAde', $code->getId(), PDO::PARAM_INT);

                $request->execute();
            }
        }

        return $id;
    }

    /**
     * Modify the information in database
     */
    public function update() {
        $database = $this->getDatabase();
        $request = $database->prepare('UPDATE ecran_alert SET content = :content, expiration_date = :expirationDate, for_everyone = :for_everyone WHERE id = :id');

        $request->bindValue(':id', $this->getId(), PDO::PARAM_INT);
        $request->bindValue(':content', $this->getContent(), PDO::PARAM_STR);
        $request->bindValue(':expirationDate', $this->getExpirationDate(), PDO::PARAM_STR);
        $request->bindValue(':for_everyone', $this->isForEveryone(), PDO::PARAM_INT);

        $request->execute();

        $count = $request->rowCount();

        $request = $database->prepare('DELETE FROM ecran_code_alert WHERE alert_id = :alertId');

        $request->bindValue(':alertId', $this->getId(), PDO::PARAM_INT);

        $request->execute();

        foreach ($this->getCodes() as $code) {

            if ($code->getCode() !== 'all' || $code->getCode() !== 0) {
                $request = $database->prepare('INSERT INTO ecran_code_alert (alert_id, code_ade_id) VALUES (:alertId, :codeAdeId)');

                $request->bindValue(':alertId', $this->getId(), PDO::PARAM_INT);
                $request->bindValue(':codeAdeId', $code->getId(), PDO::PARAM_INT);

                $request->execute();
            }
        }

        return $count;
    }

    /**
     * Delete an alert in the database
     */
    public function delete() {
        $request = $this->getDatabase()->prepare('DELETE FROM ecran_alert WHERE id = :id');

        $request->bindValue(':id', $this->getId(), PDO::PARAM_INT);

        $request->execute();

        return $request->rowCount();
    }

    /**
     * Return the alert corresponding to an ID
     *
     * @param $id
     *
     * @return Alert | null
     */
    public function get($id) {
        $request = $this->getDatabase()->prepare('SELECT id, content, creation_date, expiration_date, author, administration_id FROM ecran_alert WHERE id = :id LIMIT 1');

        $request->bindParam(':id', $id, PDO::PARAM_INT);

        $request->execute();

        return $this->setEntity($request->fetch(PDO::FETCH_ASSOC));
    }

    /**
     * @param int $begin
     * @param int $numberElement
     *
     * @return array|Alert[]
     */
    public function getList($begin = 0, $numberElement = 25) {
        $request = $this->getDatabase()->prepare("SELECT id, content, creation_date, expiration_date, author, administration_id FROM ecran_alert ORDER BY id ASC LIMIT :begin, :numberElement");

        $request->bindValue(':begin', (int)$begin, PDO::PARAM_INT);
        $request->bindValue(':numberElement', (int)$numberElement, PDO::PARAM_INT);

        $request->execute();

        if ($request->rowCount() > 0) {
            return $this->setEntityList($request->fetchAll());
        }
        return [];
    }

    /**
     * @param int $begin
     * @param int $numberElement
     *
     * @return array|Alert[]
     */
    public function getAuthorListAlert($author, $begin = 0, $numberElement = 25) {
        $request = $this->getDatabase()->prepare("SELECT id, content, creation_date, expiration_date, author, administration_id FROM ecran_alert  WHERE author = :author ORDER BY id ASC LIMIT :begin, :numberElement");

        $request->bindValue(':begin', (int)$begin, PDO::PARAM_INT);
        $request->bindValue(':numberElement', (int)$numberElement, PDO::PARAM_INT);
        $request->bindParam(':author', $author, PDO::PARAM_INT);

        $request->execute();

        if ($request->rowCount() > 0) {
            return $this->setEntityList($request->fetchAll());
        }
        return [];
    }

    /**
     * @return Information[]
     */
    public function getFromAdminWebsite() {
        $request = $this->getDatabaseViewer()->prepare('SELECT id, content, author, expiration_date, creation_date FROM ecran_alert LIMIT 200');

        $request->execute();

        return $this->setEntityList($request->fetchAll(), true);
    }

    /**
     * Get all alerts for the user
     *
     * @param $id
     *
     * @return Alert[]
     */
    public function getForUser($id) {
        $request = $this->getDatabase()->prepare('SELECT ecran_alert.id, content, creation_date, expiration_date, author, administration_id
															FROM ecran_alert
															JOIN ecran_code_alert ON ecran_alert.id = ecran_code_alert.alert_id
															JOIN ecran_code_ade ON ecran_code_alert.code_ade_id = ecran_code_ade.id
															JOIN ecran_code_user ON ecran_code_ade.id = ecran_code_user.code_ade_id
															WHERE ecran_code_user.user_id = :id ORDER BY expiration_date ASC');

        $request->bindParam(':id', $id, PDO::PARAM_INT);

        $request->execute();

        return $this->setEntityList($request->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * Get all alerts for everyone
     */
    public function getForEveryone() {
        $request = $this->getDatabase()->prepare('SELECT ecran_alert.id, content, creation_date, expiration_date, author, administration_id FROM ecran_alert WHERE for_everyone = 1 ORDER BY expiration_date ASC LIMIT 50');

        $request->bindParam(':id', $id, PDO::PARAM_INT);

        $request->execute();

        return $this->setEntityList($request->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * Get all link between the alert and the codes ADE
     *
     * @return array|Alert
     */
    public function getAlertLinkToCode() {
        $request = $this->getDatabase()->prepare('SELECT ecran_alert.id, content, creation_date, expiration_date, author FROM ecran_code_alert JOIN ecran_alert ON ecran_code_alert.alert_id = ecran_alert.id WHERE alert_id = :alertId LIMIT 50');

        $request->bindValue(':alertId', $this->getId(), PDO::PARAM_INT);

        $request->execute();

        return $this->setEntityList($request->fetchAll());
    }

    public function getAdminWebsiteAlert() {
        $request = $this->getDatabase()->prepare('SELECT id, content, author, expiration_date, creation_date, for_everyone FROM ecran_alert WHERE administration_id IS NOT NULL LIMIT 500');

        $request->execute();

        return $this->setEntityList($request->fetchAll());
    }

    /**
     * @return int
     */
    public function countAll() {
        $request = $this->getDatabase()->prepare("SELECT COUNT(*) FROM ecran_alert");

        $request->execute();

        return $request->fetch()[0];
    }

    /**
     * @param $id
     * @return $this|bool|Information
     */
    public function getAlertFromAdminSite($id) {
        $request = $this->getDatabaseViewer()->prepare('SELECT id, content, author, expiration_date, creation_date FROM ecran_alert WHERE id = :id LIMIT 1');

        $request->bindValue(':id', $id, PDO::PARAM_INT);

        $request->execute();

        if ($request->rowCount() > 0) {
            return $this->setEntity($request->fetch(), true);
        }
        return false;
    }

    /**
     * Build a list of alerts
     *
     * @param $dataList
     *
     * @return array | Alert
     */
    public function setEntityList($dataList, $adminSite = false) {
        $listEntity = array();
        foreach ($dataList as $data) {
            $listEntity[] = $this->setEntity($data, $adminSite);
        }
        return $listEntity;
    }

    /**
     * Create an alert
     *
     * @param $data
     * @param bool $adminSite
     *
     * @return Alert
     */
    public function setEntity($data, $adminSite = false) {
        $entity = new Alert();
        $author = new User();
        $codeAde = new CodeAde();

        $entity->setId($data['id']);
        $entity->setContent($data['content']);
        $entity->setCreationDate(date('Y-m-d', strtotime($data['creation_date'])));
        $entity->setExpirationDate(date('Y-m-d', strtotime($data['expiration_date'])));

        if ($data['administration_id'] != null) {
            $author->setLogin('Administration');
            $entity->setAuthor($author);
        } else {
            $entity->setAuthor($author->get($data['author']));
        }


        if ($adminSite) {
            $entity->setAdminId($data['id']);
            $entity->setForEveryone(1);
        } else {
            $entity->setAdminId($data['administration_id']);

            $codes = array();

            if (sizeof($codes) <= 0) {
                if ($entity->isForEveryone()) {
                    $codeAde->setTitle('Tous');
                    $codeAde->setCode('all');
                    $codes[] = $codeAde;
                } else {
                    $codeAde->setTitle('Aucun');
                    $codeAde->setCode('0');
                    $codes[] = $codeAde;
                }
            }

            foreach ($codeAde->getByAlert($data['id']) as $code) {
                $codes[] = $code;
            }
            $entity->setCodes($codes);
        }

        return $entity;
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
     * @return User
     */
    public function getAuthor() {
        return $this->author;
    }

    /**
     * @param $author
     */
    public function setAuthor($author) {
        $this->author = $author;
    }

    /**
     * @return string
     */
    public function getContent() {
        return $this->content;
    }

    /**
     * @param $content
     */
    public function setContent($content) {
        $this->content = $content;
    }

    /**
     * @return string
     */
    public function getCreationDate() {
        return $this->creation_date;
    }

    /**
     * @param $creation_date
     */
    public function setCreationDate($creation_date) {
        $this->creation_date = $creation_date;
    }

    /**
     * @return string
     */
    public function getExpirationDate() {
        return $this->expirationDate;
    }

    /**
     * @param $expirationDate
     */
    public function setExpirationDate($expirationDate) {
        $this->expirationDate = $expirationDate;
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

    /**
     * @return int
     */
    public function isForEveryone() {
        return $this->forEveryone;
    }

    /**
     * @param int $forEveryone
     */
    public function setForEveryone($forEveryone) {
        $this->forEveryone = $forEveryone;
    }

    /**
     * @return int
     */
    public function getAdminId() {
        return $this->adminId;
    }

    /**
     * @param int $adminId
     */
    public function setAdminId($adminId) {
        $this->adminId = $adminId;
    }

    public function jsonSerialize() {
        return get_object_vars($this);
    }
}
