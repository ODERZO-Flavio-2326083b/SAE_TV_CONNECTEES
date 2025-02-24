<?php

namespace models;

use JsonSerializable;
use PDO;

class Scrapping extends Model implements Entity, JsonSerializable {

    private ?int $_id = null;

    private ?string $_title;

    private ?User $_author;

    private ?string $_creationDate;

    private ?string $_expirationDate;

    private ?string $_tag;

    private ?string $_num;

    private ?string $_type;

    private ?int $_adminId;

    private ?int $_idDepartment;

    private ?int $_duration;

    public function insert() {
        $database = $this->getDatabase();
        $request = $database->prepare(
                "
                INSERT INTO ecran_scrapping
                    (title,
                     content,
                     tag,
                     num,
                     creation_date,
                     expiration_date,
                     type,
                     author,
                     administration_id,
                     department_id,
                     duration)
                VALUES 
                    (:title,
                     :tag,
                     :num,
                     :creation_date,
                     :expiration_date,
                     :type, 
                     :author, 
                     :administration_id, 
                     :department_id, 
                     :duration) "
        );
        $request->bindValue(':title', $this->getTitle());
        $request->bindValue(':tag', $this->getTag());
        $request->bindValue(':num', $this->getNum());
        $request->bindValue(
            ':creationDate', $this->getCreationDate()
        );
        $request->bindValue(
            ':expirationDate', $this->getExpirationDate()
        );
        $request->bindValue(':type', $this->getType());
        $request->bindValue(
            ':userId', $this->getAuthor()->getId(),
            PDO::PARAM_INT
        );
        $request->bindValue(
            ':administration_id', $this->getAdminId(),
            PDO::PARAM_INT
        );
        $request->bindValue(
            ':department_id', $this->getIdDepartment(),
            PDO::PARAM_INT
        );
        $request->bindValue(
            ':duration', $this->getDuration(),
            PDO::PARAM_INT
        );
        $request->execute();
        return $database->lastInsertId();
    }

    public function update() : int
    {
        $request = $this->getDatabase()->prepare(
            "
        UPDATE ecran_scrapping 
        SET title = :title, 
            tag = :tag, 
            num = :num,
            expiration_date = :expirationDate,
            department_id = :deptId,
            duration = :duration
        WHERE scrapping_id = :id"
        );
        $request->bindValue(':title', $this->getTitle());
        $request->bindValue(':tag', $this->getTag());
        $request->bindValue(':num', $this->getNum());
        $request->bindValue(':expirationDate', $this->getExpirationDate());
        $request->bindValue(':id', $this->getId(), PDO::PARAM_INT);
        $request->bindValue(
            ':deptId', $this->getIdDepartment(),
            PDO::PARAM_INT
        );
        $request->bindValue(
            ':duration', $this->getDuration(),
            PDO::PARAM_INT
        );
        $request->execute();
        return $request->rowCount();
    }

    public function delete() : int
    {
        $request = $this->getDatabase()->prepare(
            'DELETE FROM ecran_scrapping WHERE scrapping_id = :id'
        );
        $request->bindValue(':id', $this->getId(), PDO::PARAM_INT);
        $request->execute();
        return $request->rowCount();
    }

    public function get($id) : false|Scrapping
    {
        $request = $this->getDatabase()->prepare(
            "
        SELECT 
            id, 
            title, 
            tag,
            num,
            creation_date, 
            expiration_date, 
            author, 
            type, 
            administration_id, 
            department_id,
            duration
        FROM 
            ecran_scrapping
        WHERE scrapping_id = :id LIMIT 1"
        );
        $request->bindParam(':id', $id, PDO::PARAM_INT);
        $request->execute();
        if ($request->rowCount() > 0) {
            return $this->setEntity($request->fetch(PDO::FETCH_ASSOC));
        }
        return false;
    }

    public function getList(int $begin = 0, int $numberElement = 25) : array
    {
        $request = $this->getDatabase()->prepare(
            "
        SELECT 
            id, 
            title,
            tag,
            num,
            creation_date,
            expiration_date,
            author, 
            type,
            administration_id,
            department_id, 
            duration
        FROM ecran_scrapping 
        ORDER BY scrapping_id 
        LIMIT 
            :begin,
            :numberElement"
        );
        $request->bindValue(':begin', $begin, PDO::PARAM_INT);
        $request->bindValue(
            ':numberElement', $numberElement,
            PDO::PARAM_INT
        );
        $request->execute();
        if ($request->rowCount() > 0) {
            return $this->setEntityList($request->fetchAll());
        }
        return [];
    }

    public function setEntityList($dataList, bool $adminSite = false) : array
    {
        $listEntity = array();
        foreach ($dataList as $data) {
            $listEntity[] = $this->setEntity($data, $adminSite);
        }
        return $listEntity;
    }

    public function setEntity($data, bool $adminSite = false) : Scrapping
    {
        $entity = new Scrapping();
        $author = new User();
        $entity->setId($data['id']);
        $entity->setTitle($data['title']);
        $entity->setTag($data['tag']);
        $entity->setNum($data['num']);
        $entity->setCreationDate(
            date(
                'Y-m-d',
                strtotime($data['creation_date'])
            )
        );
        $entity->setExpirationDate(
            date('Y-m-d', strtotime($data['expiration_date']))
        );
        $entity->setIdDepartment($data['department_id']);
        $entity->setType($data['type']);
        $entity->setDuration($data['duration']);
        if ($data['administration_id'] != null) {
            $author->setLogin('Administration');
            $entity->setAuthor($author);
        } else {
            $entity->setAuthor($author->get($data['author']));
        }
        if ($adminSite) {
            $entity->setAdminId($data['id']);
        } else {
            $entity->setAdminId($data['administration_id']);
        }
        return $entity;
    }

    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }

    public function setId(?int $id): void
    {
        $this->_id = $id;
    }

    public function setTitle(?string $title): void
    {
        $this->_title = $title;
    }

    public function setCreationDate(?string $creationDate): void
    {
        $this->_creationDate = $creationDate;
    }

    public function setNum(?string $num): void
    {
        $this->_num = $num;
    }

    public function setIdDepartment(?int $idDepartment): void
    {
        $this->_idDepartment = $idDepartment;
    }

    public function setDuration(?int $duration): void
    {
        $this->_duration = $duration;
    }

    public function setAdminId(?int $adminId): void
    {
        $this->_adminId = $adminId;
    }

    public function setType(?string $type): void
    {
        $this->_type = $type;
    }

    public function setTag(?string $tag): void
    {
        $this->_tag = $tag;
    }

    public function setExpirationDate(?string $expirationDate): void
    {
        $this->_expirationDate = $expirationDate;
    }

    public function setAuthor(?User $author): void
    {
        $this->_author = $author;
    }

    public function getId() : ?int
    {
        return $this->_id;
    }

    public function getTitle() : ?string
    {
        return $this->_title;
    }

    public function getTag() : ?string
    {
        return $this->_tag;
    }

    public function getNum() : ?int
    {
        return $this->_num;
    }

    public function getCreationDate() : ?string {
        return $this->_creationDate;
    }

    public function getExpirationDate() : ?string {
        return $this->_expirationDate;
    }

    public function getType() : ?string {
        return $this->_type;
    }

    public function getAdminId() : ?int {
        return $this->_adminId;
    }

    public function getIdDepartment() : ?int {
        return $this->_idDepartment;
    }

    public function getDuration() : ?int {
        return $this->_duration;
    }

    public function getAuthor() : ?User {
        return $this->_author;
    }
}