<?php

namespace Models;

use JsonSerializable;
use PDO;

/**
 * Class Information
 *
 * Information entity
 *
 * @package Models
 */
class Information extends Model implements Entity, JsonSerializable
{

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $title;

    /**
     * @var User
     */
    private $author;

    /**
     * @var string
     */
    private $creationDate;

    /**
     * @var string
     */
    private $expirationDate;

    /**
     * @var string
     */
    private $content;

    /**
     * @var string (Texte | Image | Vidéo | Excel | PDF | Événement)
     */
    private $type;

    /**
     * Insère un nouvel enregistrement d'information dans la base de données.
     *
     * Cette méthode prépare une requête SQL pour insérer un nouvel enregistrement dans
     * la table `ecran_information`. Elle lie les valeurs des propriétés de l'objet
     * courant à la requête SQL, puis exécute la requête. Enfin, elle renvoie l'ID
     * du nouvel enregistrement inséré.
     *
     * @return int L'ID du nouvel enregistrement inséré dans la base de données.
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function insert() {
        $database = $this->getDatabase();
        $request = $database->prepare("INSERT INTO ecran_information (title, content, creation_date, expiration_date, type, author, administration_id) VALUES (:title, :content, :creationDate, :expirationDate, :type, :userId, :administration_id) ");

        $request->bindValue(':title', $this->getTitle(), PDO::PARAM_STR);
        $request->bindValue(':content', $this->getContent(), PDO::PARAM_STR);
        $request->bindValue(':creationDate', $this->getCreationDate(), PDO::PARAM_STR);
        $request->bindValue(':expirationDate', $this->getExpirationDate(), PDO::PARAM_STR);
        $request->bindValue(':type', $this->getType(), PDO::PARAM_STR);
        $request->bindValue(':userId', $this->getAuthor(), PDO::PARAM_INT);
        $request->bindValue('administration_id', $this->getAdminId(), PDO::PARAM_INT);

        $request->execute();

        return $database->lastInsertId();
    }

    /**
     * Met à jour un enregistrement d'information existant dans la base de données.
     *
     * Cette méthode prépare une requête SQL pour mettre à jour un enregistrement
     * dans la table `ecran_information`. Elle lie les valeurs des propriétés de
     * l'objet courant à la requête SQL, puis exécute la requête. La méthode renvoie
     * le nombre de lignes affectées par l'opération.
     *
     * @return int Le nombre de lignes mises à jour dans la base de données.
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function update() {
        $request = $this->getDatabase()->prepare("UPDATE ecran_information SET title = :title, content = :content, expiration_date = :expirationDate WHERE id = :id");

        $request->bindValue(':title', $this->getTitle(), PDO::PARAM_STR);
        $request->bindValue(':content', $this->getContent(), PDO::PARAM_STR);
        $request->bindValue(':expirationDate', $this->getExpirationDate(), PDO::PARAM_STR);
        $request->bindValue(':id', $this->getId(), PDO::PARAM_INT);

        $request->execute();

        return $request->rowCount();
    }

    /**
     * Supprime un enregistrement d'information de la base de données.
     *
     * Cette méthode prépare une requête SQL pour supprimer un enregistrement
     * dans la table `ecran_information` en fonction de l'identifiant
     * spécifié. Elle lie l'identifiant de l'enregistrement à la requête SQL,
     * exécute la requête et renvoie le nombre de lignes affectées par
     * l'opération.
     *
     * @return int Le nombre de lignes supprimées dans la base de données.
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function delete() {
        $request = $this->getDatabase()->prepare('DELETE FROM ecran_information WHERE id = :id');

        $request->bindValue(':id', $this->getId(), PDO::PARAM_INT);

        $request->execute();

        return $request->rowCount();
    }

    /**
     * Récupère un enregistrement d'information à partir de son identifiant.
     *
     * Cette méthode prépare une requête SQL pour sélectionner un enregistrement
     * dans la table `ecran_information` basé sur l'identifiant fourni. Elle lie
     * cet identifiant à la requête SQL, exécute la requête, et si un enregistrement
     * est trouvé, il est renvoyé sous forme d'entité. Si aucun enregistrement
     * n'est trouvé, la méthode retourne `false`.
     *
     * @param int $id L'identifiant de l'enregistrement à récupérer.
     * @return mixed L'entité correspondant à l'enregistrement ou `false` si
     *               aucun enregistrement n'est trouvé.
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function get($id) {
        $request = $this->getDatabase()->prepare("SELECT id, title, content, creation_date, expiration_date, author, type, administration_id FROM ecran_information WHERE id = :id LIMIT 1");

        $request->bindParam(':id', $id, PDO::PARAM_INT);

        $request->execute();

        if ($request->rowCount() > 0) {
            return $this->setEntity($request->fetch(PDO::FETCH_ASSOC));
        }
        return false;
    }

    /**
     * Récupère une liste d'enregistrements d'information.
     *
     * Cette méthode prépare une requête SQL pour sélectionner une liste d'enregistrements
     * dans la table `ecran_information`, avec pagination. Elle lie les paramètres de début
     * et le nombre d'éléments à récupérer, exécute la requête, et si des enregistrements
     * sont trouvés, ils sont renvoyés sous forme de liste d'entités. Si aucun enregistrement
     * n'est trouvé, un tableau vide est retourné.
     *
     * @param int $begin Le point de départ pour la récupération des enregistrements.
     * @param int $numberElement Le nombre d'enregistrements à récupérer.
     * @return array Un tableau d'entités correspondant aux enregistrements récupérés.
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function getList($begin = 0, $numberElement = 25) {
        $request = $this->getDatabase()->prepare("SELECT id, title, content, creation_date, expiration_date, author, type, administration_id FROM ecran_information ORDER BY id ASC LIMIT :begin, :numberElement");

        $request->bindValue(':begin', (int)$begin, PDO::PARAM_INT);
        $request->bindValue(':numberElement', (int)$numberElement, PDO::PARAM_INT);

        $request->execute();

        if ($request->rowCount() > 0) {
            return $this->setEntityList($request->fetchAll());
        }
        return [];
    }

    /**
     * Récupère une liste d'enregistrements d'information d'un auteur spécifique.
     *
     * Cette méthode prépare une requête SQL pour sélectionner les enregistrements
     * dans la table `ecran_information` où l'auteur correspond à celui spécifié.
     * La méthode utilise la pagination pour retourner un sous-ensemble des résultats
     * en fonction des paramètres de début et de nombre d'éléments. Les résultats
     * sont triés par date d'expiration.
     *
     * @param int $author L'identifiant de l'auteur des informations.
     * @param int $begin Le point de départ pour la récupération des enregistrements.
     * @param int $numberElement Le nombre d'enregistrements à récupérer.
     * @return array Un tableau d'entités correspondant aux enregistrements récupérés.
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function getAuthorListInformation($author, $begin = 0, $numberElement = 25) {
        $request = $this->getDatabase()->prepare('SELECT id, title, content, creation_date, expiration_date, author, type, administration_id FROM ecran_information WHERE author = :author ORDER BY expiration_date LIMIT :begin, :numberElement');

        $request->bindParam(':author', $author, PDO::PARAM_INT);
        $request->bindValue(':begin', (int)$begin, PDO::PARAM_INT);
        $request->bindValue(':numberElement', (int)$numberElement, PDO::PARAM_INT);

        $request->execute();

        return $this->setEntityList($request->fetchAll(PDO::FETCH_ASSOC));
    } //getAuthorListInformation()

    /**
     * Compte le nombre total d'enregistrements dans la table `ecran_information`.
     *
     * Cette méthode exécute une requête SQL pour compter tous les enregistrements
     * présents dans la table. Elle retourne un entier représentant le nombre
     * total d'enregistrements.
     *
     * @return int Le nombre total d'enregistrements dans `ecran_information`.
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function countAll() {
        $request = $this->getDatabase()->prepare("SELECT COUNT(*) FROM ecran_information");

        $request->execute();

        return $request->fetch()[0];
    }

    /**
     * Récupère la liste des informations de type "event".
     *
     * Cette méthode exécute une requête SQL pour sélectionner toutes les informations
     * de type "event" dans la table `ecran_information`, triées par date d'expiration.
     *
     * @return array Un tableau d'entités représentant les informations de type "event".
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function getListInformationEvent() {
        $request = $this->getDatabase()->prepare('SELECT id, title, content, creation_date, expiration_date, author, type FROM ecran_information WHERE type = "event" ORDER BY expiration_date ASC');

        $request->execute();

        return $this->setEntityList($request->fetchAll(PDO::FETCH_ASSOC));
    }


    /**
     * Récupère les informations à partir du site d'administration.
     *
     * Cette méthode exécute une requête SQL pour sélectionner les informations
     * dans la table `ecran_information`, avec un maximum de 200 résultats.
     *
     * @return array Un tableau d'entités représentant les informations.
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function getFromAdminWebsite() {
        $request = $this->getDatabaseViewer()->prepare('SELECT id, title, content, type, author, expiration_date, creation_date FROM ecran_information LIMIT 200');

        $request->execute();

        return $this->setEntityList($request->fetchAll(), true);
    }

    /**
     * Récupère les informations administratives à partir du site d'administration.
     *
     * Cette méthode exécute une requête SQL pour sélectionner les informations
     * dans la table `ecran_information` où `administration_id` n'est pas nul,
     * avec un maximum de 500 résultats.
     *
     * @return array Un tableau d'entités représentant les informations administratives.
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function getAdminWebsiteInformation() {
        $request = $this->getDatabase()->prepare('SELECT id, title, content, creation_date, expiration_date, author, type, administration_id FROM ecran_information WHERE administration_id IS NOT NULL LIMIT 500');

        $request->execute();

        return $this->setEntityList($request->fetchAll());
    }

    /**
     * Récupère les informations d'un site d'administration par son ID.
     *
     * Cette méthode exécute une requête SQL pour sélectionner une information
     * spécifique dans la table `ecran_information` en fonction de son ID.
     * Elle retourne l'entité correspondante si trouvée, sinon elle retourne faux.
     *
     * @param int $id L'ID de l'information à récupérer.
     * @return mixed L'entité représentant l'information, ou faux si non trouvée.
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function getInformationFromAdminSite($id) {
        $request = $this->getDatabaseViewer()->prepare('SELECT id, title, content, type, author, expiration_date, creation_date FROM ecran_information WHERE id = :id LIMIT 1');

        $request->bindValue(':id', $id, PDO::PARAM_INT);

        $request->execute();

        if ($request->rowCount() > 0) {
            return $this->setEntity($request->fetch(), true);
        }
        return false;
    }

    /**
     * Crée une liste d'entités à partir d'une liste de données.
     *
     * Cette méthode prend une liste de données et crée une liste d'entités
     * correspondantes en appelant la méthode `setEntity` pour chaque élément.
     * Elle peut également prendre en compte un paramètre indiquant si
     * les entités sont pour un site d'administration.
     *
     * @param array $dataList La liste des données à convertir en entités.
     * @param bool $adminSite Indique si les entités sont pour un site d'administration (par défaut : faux).
     * @return array La liste d'entités créée à partir des données fournies.
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function setEntityList($dataList, $adminSite = false) {
        $listEntity = array();
        foreach ($dataList as $data) {
            $listEntity[] = $this->setEntity($data, $adminSite);
        }
        return $listEntity;
    }


    /**
     * Crée une instance d'entité `Information` à partir d'un tableau de données.
     *
     * Cette méthode initialise une nouvelle entité `Information` en utilisant les données
     * fournies dans le tableau `$data`. Elle gère également l'auteur de l'information
     * en fonction de la présence d'un identifiant d'administration.
     *
     * @param array $data Un tableau associatif contenant les données de l'entité, y compris :
     *                    - id (int): L'identifiant de l'information.
     *                    - title (string): Le titre de l'information.
     *                    - content (string): Le contenu de l'information.
     *                    - creation_date (string): La date de création au format 'Y-m-d'.
     *                    - expiration_date (string): La date d'expiration au format 'Y-m-d'.
     *                    - type (string): Le type de l'information.
     *                    - administration_id (int|null): L'identifiant de l'administration, s'il existe.
     *                    - author (int): L'identifiant de l'auteur.
     * @param bool $adminSite Indique si l'entité est pour un site d'administration (par défaut : faux).
     * @return Information L'instance d'entité `Information` créée et initialisée avec les données fournies.
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function setEntity($data, $adminSite = false) {
        $entity = new Information();
        $author = new User();

        $entity->setId($data['id']);
        $entity->setTitle($data['title']);
        $entity->setContent($data['content']);
        $entity->setCreationDate(date('Y-m-d', strtotime($data['creation_date'])));
        $entity->setExpirationDate(date('Y-m-d', strtotime($data['expiration_date'])));

        $entity->setType($data['type']);

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
    public function getTitle() {
        return $this->title;
    }

    /**
     * @param $title
     */
    public function setTitle($title) {
        $this->title = $title;
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
    public function getCreationDate() {
        return $this->creationDate;
    }

    /**
     * @param mixed $creationDate
     */
    public function setCreationDate($creationDate) {
        $this->creationDate = $creationDate;
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
    public function getType() {
        return $this->type;
    }

    /**
     * @param $type
     */
    public function setType($type) {
        $this->type = $type;
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