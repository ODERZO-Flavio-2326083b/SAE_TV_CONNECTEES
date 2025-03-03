<?php
/**
 * Fichier Information.php
 *
 * Ce fichier contient la classe 'Information', qui représente une entité
 * d'information dans l'application. Cette classe est utilisée pour gérer
 * les informations relatives à l'application, telles que la récupération,
 * l'insertion, la mise à jour et la suppression des données dans la base
 * de données.
 *
 * PHP version 8.3
 *
 * @category Entity
 * @package  Models
 * @author   BUT Informatique, AMU <iut-aix-scol@univ-amu.fr>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @version  GIT: abcd1234abcd5678efgh9012ijkl3456mnop6789
 * @link     https://www.example.com/docs/Information
 * Documentation de la classe
 * @since    2025-01-07
 */
namespace models;

use JsonSerializable;
use PDO;

/**
 * Class Information
 *
 * Représente une entité d'information dans l'application. Cette classe est
 * utilisée pour gérer les informations (récupérer, insérer, mettre à jour
 * et supprimer des données) dans la base de données.
 *
 * @category Entity
 * @package  Models
 * @author   BUT Informatique, AMU <iut-aix-scol@univ-amu.fr>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @version  Release: 2.0.0
 * @link     https://www.example.com/docs/Information Documentation de la classe
 * @since    2025-01-07
 */
class Information extends Model implements Entity, JsonSerializable
{

    /**
     * Identifiant unique de l'entité.
     *
     * @var ?int L'identifiant, ou null si non défini.
     */
    private ?int $_id = null;

    /**
     * Titre de l'entité.
     *
     * @var ?string Le titre de l'entité, ou null si non défini.
     */
    private ?string $_title;

    /**
     * Auteur de l'entité.
     *
     * @var ?User L'auteur de l'entité, ou null si non défini.
     * Il s'agit d'un objet de la classe 'User'.
     */
    private ?User $_author;

    /**
     * Date de création de l'entité.
     *
     * @var ?string La date de création sous forme de
     * chaîne (format "YYYY-MM-DD"), ou null si non définie.
     */
    private ?string $_creationDate;

    /**
     * Date d'expiration de l'entité.
     *
     * @var ?string La date d'expiration sous forme
     * de chaîne (format "YYYY-MM-DD"), ou null si non définie.
     */
    private ?string $_expirationDate;

    /**
     * Contenu de l'entité.
     *
     * @var ?string Le contenu de l'entité, ou null si non défini.
     */
    private ?string $_content;

    /**
     * Type de l'entité.
     *
     * @var ?string Le type de l'entité (par exemple "Text",
     * "Image", "PDF", "Event", "Video", "Short"), ou null si non défini.
     */
    private ?string $_type;

    /**
     * Identifiant de l'administrateur associé à l'entité.
     *
     * @var ?int L'identifiant de l'administrateur, ou null si non défini.
     */
    private ?int $_adminId;

    /**
     * Identifiant du département associé à l'entité.
     *
     * @var ?int L'identifiant du département, ou null si non défini.
     */
    private ?int $_idDepartment;

    /**
     * Durée associée à l'entité.
     *
     * @var ?int La durée de l'entité en heures, ou null si non définie.
     */
    private ?int $_duration;


    /**
     * Insère un nouvel enregistrement d'information dans la base de données.
     *
     * Cette méthode prépare une requête SQL pour insérer un nouvel enregistrement
     * dans la table 'ecran_information'. Elle lie les valeurs des propriétés de
     * l'objet courant à la requête SQL, puis exécute la requête. Enfin, elle renvoie
     * l'ID du nouvel enregistrement inséré.
     *
     * @return int L'ID du nouvel enregistrement inséré dans la base de données.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function insert() : int
    {
        $database = $this->getDatabase();
        $request = $database->prepare(
            "
        INSERT INTO ecran_information 
            (title,
             content,
             creation_date,
             expiration_date,
             type,
             author,
             administration_id,
             department_id,
             duration)
        VALUES
            (:title,
             :content,
             :creationDate,
             :expirationDate,
             :type,
             :userId,
             :administration_id,
             :department_id,
             :duration) "
        );
        $request->bindValue(':title', $this->getTitle());
        $request->bindValue(':content', $this->getContent());
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

    /**
     * Met à jour un enregistrement d'information existant dans la base de données.
     *
     * Cette méthode prépare une requête SQL pour mettre à jour un enregistrement
     * dans la table 'ecran_information'. Elle lie les valeurs des propriétés de
     * l'objet courant à la requête SQL, puis exécute la requête. La méthode renvoie
     * le nombre de lignes affectées par l'opération.
     *
     * @return int Le nombre de lignes mises à jour dans la base de données.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function update() : int
    {
        $request = $this->getDatabase()->prepare(
            "
        UPDATE ecran_information 
        SET title = :title, 
            content = :content, 
            expiration_date = :expirationDate,
            department_id = :deptId,
            duration = :duration
        WHERE id = :id"
        );
        $request->bindValue(':title', $this->getTitle());
        $request->bindValue(':content', $this->getContent());
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

    /**
     * Supprime un enregistrement d'information de la base de données.
     *
     * Cette méthode prépare une requête SQL pour supprimer un enregistrement
     * dans la table 'ecran_information' en fonction de l'identifiant
     * spécifié. Elle lie l'identifiant de l'enregistrement à la requête SQL,
     * exécute la requête et renvoie le nombre de lignes affectées par
     * l'opération.
     *
     * @return int Le nombre de lignes supprimées dans la base de données.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function delete() : int
    {
        $request = $this->getDatabase()->prepare(
            'DELETE FROM ecran_information 
       WHERE id = :id'
        );
        $request->bindValue(':id', $this->getId(), PDO::PARAM_INT);
        $request->execute();
        return $request->rowCount();
    }

    /**
     * Récupère un enregistrement d'information à partir de son identifiant.
     *
     * Cette méthode prépare une requête SQL pour sélectionner un enregistrement
     * dans la table 'ecran_information' basé sur l'identifiant fourni. Elle lie
     * cet identifiant à la requête SQL, exécute la requête, et si un enregistrement
     * est trouvé, il est renvoyé sous forme d'entité. Si aucun enregistrement
     * n'est trouvé, la méthode retourne 'false'.
     *
     * @param int $id L'identifiant de l'enregistrement à récupérer.
     *
     * @return false|Information L'entité correspondant à l'enregistrement
     *                          ou 'false' si aucun enregistrement n'est trouvé.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function get($id) : false|Information
    {
        $request = $this->getDatabase()->prepare(
            "
        SELECT 
            id, 
            title, 
            content, 
            creation_date, 
            expiration_date, 
            author, 
            type, 
            administration_id, 
            department_id,
            duration
        FROM 
            ecran_information
        WHERE id = :id LIMIT 1"
        );
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
     * Cette méthode prépare une requête SQL pour sélectionner une liste
     * d'enregistrements dans la table 'ecran_information', avec pagination.
     * Elle lie les paramètres de début et le nombre d'éléments à récupérer, exécute
     * la requête, et si des enregistrements sont trouvés, ils sont renvoyés sous
     * forme de liste d'entités. Si aucun enregistrement n'est trouvé, un tableau
     * vide est retourné.
     *
     * @param int $begin         Le point de départ pour la récupération des
     *                           enregistrements.
     * @param int $numberElement Le nombre d'enregistrements à récupérer.
     *
     * @return array Un tableau d'entités correspondant aux enregistrements
     * récupérés.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function getList(int $begin = 0, int $numberElement = 25) : array
    {
        $request = $this->getDatabase()->prepare(
            "
        SELECT 
            id, 
            title,
            content,
            creation_date,
            expiration_date,
            author, 
            type,
            administration_id,
            department_id, 
            duration
        FROM ecran_information 
        ORDER BY id 
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

    /**
     * Récupère une liste d'enregistrements d'information d'un auteur spécifique.
     *
     * Cette méthode prépare une requête SQL pour sélectionner les enregistrements
     * dans la table 'ecran_information' où l'auteur correspond à celui spécifié.
     * La méthode utilise la pagination pour retourner un sous-ensemble des résultats
     * en fonction des paramètres de début et de nombre d'éléments. Les résultats
     * sont triés par date d'expiration.
     *
     * @param int $author        L'identifiant de l'auteur des informations.
     * @param int $begin         Le point de départ pour la récupération des
     *                           enregistrements.
     * @param int $numberElement Le nombre d'enregistrements à récupérer.
     *
     * @return array Un tableau d'entités correspondant aux enregistrements
     * récupérés.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function getAuthorListInformation( int $author, int $begin = 0,
        int $numberElement = 25
    ) : array {
        $request = $this->getDatabase()->prepare(
            '
        SELECT 
            id, 
            title,
            content,
            creation_date,
            expiration_date,
            author,
            type,
            administration_id,
            department_id, 
            duration
        FROM 
            ecran_information
        WHERE author = :author 
        ORDER BY expiration_date LIMIT :begin, :numberElement'
        );
        $request->bindParam(':author', $author, PDO::PARAM_INT);
        $request->bindValue(':begin', $begin, PDO::PARAM_INT);
        $request->bindValue(
            ':numberElement', $numberElement,
            PDO::PARAM_INT
        );
        $request->execute();
        return $this->setEntityList($request->fetchAll(PDO::FETCH_ASSOC));
    } //getAuthorListInformation()

    /**
     * Récupère une liste d'informations provenant d'un département spécifique.
     *
     * Cette méthode prépare une requête SQL pour sélectionner les enregistrements
     * dans la table 'ecran_information' où le département correspond à celui
     * spécifié.
     * La méthode utilise la pagination pour retourner un sous-ensemble des résultats
     * en fonction des paramètres de début et de nombre d'éléments. Les résultats
     * sont triés par date d'expiration.
     *
     * @param int $idDept        L'identifiant du
     *                           département
     * @param int $begin         Point de départ pour la récupération des
     *                           informations
     * @param int $numberElement Le nombre d'informations à récupérer
     *
     * @return array Une liste d'entités correspondant aux informations récupérées
     */
    public function getInformationsByDeptId(
        int $idDept, int $begin = 0, int $numberElement = 25
    ): array {
        $request = $this->getDatabase()->prepare(
            '
        SELECT 
            id, 
            title, 
            content, 
            creation_date, 
            expiration_date, 
            author, 
            type, 
            administration_id, 
            department_id, 
            duration
        FROM 
            ecran_information 
        WHERE 
            department_id = :id 
        ORDER BY 
            expiration_date 
        LIMIT :begin, :numberElement'
        );
        $request->bindParam(':id', $idDept, PDO::PARAM_INT);
        $request->bindValue(':begin', $begin, PDO::PARAM_INT);
        $request->bindValue(
            ':numberElement', $numberElement,
            PDO::PARAM_INT
        );
        $request->execute();
        return $this->setEntityList($request->fetchAll(PDO::FETCH_ASSOC));
    }

    public function insertScrappingTags(array $tags, array $contents) {

        $database = $this->getDatabase();
        for ($i = 0; $i < count($tags); $i++) {
            $request = $database->prepare(
                'INSERT INTO ecran_scrapping_tags
                  (id_info,
                   content,
                   tag
                  )
                   VALUES
                  (:id_info,
                   :content,
                   :tag)
        ');

            $request->bindValue(':id_info', $this->getId(), PDO::PARAM_INT);
            $request->bindValue(':content', $contents[$i], PDO::PARAM_STR);
            $request->bindValue(':tag', $tags[$i], PDO::PARAM_STR);
            $request->execute();
        }
        return $database->lastInsertId();
    }

    public function deleteScrappingTags() : int
    {
        $request = $this->getDatabase()->prepare(
            'DELETE FROM ecran_information 
       WHERE id_info = :id'
        );
        $request->bindValue(':id', $this->getId(), PDO::PARAM_INT);
        $request->execute();
        return $request->rowCount();
    }

    public function getScrappingTags(int $id) : array {
        $database = $this->getDatabase();

        $request = $database->prepare(
            'SELECT content
                   FROM ecran_information
                   WHERE id = :id'
        );
        $request->bindValue(':id', $id, PDO::PARAM_STR);
        $request->execute();

        $url = $request->fetch()['content'];

        $request = $database->prepare(
            'SELECT sc.content, sc.tag
                   FROM ecran_information i
                   JOIN ecran_scrapping_tags sc 
                   ON i.id = sc.id_info
                   WHERE i.id = :id'
        );
        $request->bindValue(':id', $id, PDO::PARAM_INT);
        $request->execute();

        $balises = array();
        $tags = array();

        foreach($request->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $balises[] = $row['content'];
            $tags[] = $row['tag'];
        }

        return array($url, $balises, $tags);
    }

    public function getContentByArticle($id) {
        $database = $this->getDatabase();

        $request = $database->prepare(
            'SELECT content
                   FROM ecran_scrapping_tags
                   WHERE tag = \'article\' 
                   AND id = :id'
        );
        $request->bindValue(':id', $id, PDO::PARAM_INT);
        $request->execute();

        return $request->fetch(PDO::FETCH_ASSOC)['content'];
    }

    /**
     * Compte le nombre total d'enregistrements dans la table 'ecran_information'.
     *
     * Cette méthode exécute une requête SQL pour compter tous les enregistrements
     * présents dans la table. Elle retourne un entier représentant le nombre
     * total d'enregistrements.
     *
     * @return int Le nombre total d'enregistrements dans 'ecran_information'.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function countAll() : int
    {
        $request = $this->getDatabase()->prepare(
            "SELECT COUNT(*) FROM 
                    ecran_information"
        );
        $request->execute();
        return $request->fetch()[0];
    }

    /**
     * Récupère la liste des informations de type "event".
     *
     * Cette méthode exécute une requête SQL pour sélectionner toutes les
     * informations de type "event" dans la table 'ecran_information', triées par
     * date d'expiration.
     *
     * @return array Un tableau d'entités représentant les informations de type
     * "event".
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function getListInformationEvent() : array
    {
        $request = $this->getDatabase()->prepare(
            'SELECT id, title, content, 
       creation_date, expiration_date, author, type FROM ecran_information 
                                                    WHERE type = \'event\' 
                                                    ORDER BY expiration_date'
        );
        $request->execute();
        return $this->setEntityList($request->fetchAll(PDO::FETCH_ASSOC));
    }


    /**
     * Récupère les informations à partir du site d'administration.
     *
     * Cette méthode exécute une requête SQL pour sélectionner les informations
     * dans la table 'ecran_information', avec un maximum de 200 résultats.
     *
     * @return array Un tableau d'entités représentant les informations.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function getFromAdminWebsite() : array
    {
        $request = $this->getDatabaseViewer()->prepare(
            'SELECT id, title, content, 
       type, author, expiration_date, creation_date 
FROM ecran_information LIMIT 200'
        );
        $request->execute();
        return $this->setEntityList($request->fetchAll(), true);
    }

    /**
     * Récupère les informations administratives à partir du site d'administration.
     *
     * Cette méthode exécute une requête SQL pour sélectionner les informations
     * dans la table 'ecran_information' où 'administration_id' n'est pas nul,
     * avec un maximum de 500 résultats.
     *
     * @return array Un tableau d'entités représentant les informations
     * administratives.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function getAdminWebsiteInformation() : array
    {
        $request = $this->getDatabase()->prepare(
            'SELECT id, title, content, 
       creation_date, expiration_date, author, type, administration_id 
FROM ecran_information WHERE administration_id IS NOT NULL LIMIT 500'
        );
        $request->execute();
        return $this->setEntityList($request->fetchAll());
    }

    /**
     * Récupère les informations d'un site d'administration par son ID.
     *
     * Cette méthode exécute une requête SQL pour sélectionner une information
     * spécifique dans la table 'ecran_information' en fonction de son ID.
     * Elle retourne l'entité correspondante si trouvée, sinon elle retourne faux.
     *
     * @param int $id L'ID de l'information à récupérer.
     *
     * @return false|Information L'entité représentant l'information,
     *                           ou faux si non trouvée.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function getInformationFromAdminSite( int $id ) : false|Information
    {
        $request = $this->getDatabaseViewer()->prepare(
            '
        SELECT 
            id, 
            title, 
            content, 
            type,
            author,
            expiration_date,
            creation_date,
            duration 
        FROM ecran_information 
        WHERE id = :id LIMIT 1'
        );

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
     * correspondantes en appelant la méthode 'setEntity' pour chaque élément.
     * Elle peut également prendre en compte un paramètre indiquant si
     * les entités sont pour un site d'administration.
     *
     * @param array $dataList  La liste des données à convertir en
     *                         entités.
     * @param bool  $adminSite Indique si les entités sont pour un
     *                         site d'administration (par défaut :
     *                         faux).
     *
     * @return array La liste d'entités créée à partir des données fournies.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function setEntityList($dataList, bool $adminSite = false) : array
    {
        $listEntity = array();
        foreach ($dataList as $data) {
            $listEntity[] = $this->setEntity($data, $adminSite);
        }
        return $listEntity;
    }


    /**
     * Crée une instance d'entité 'Information' à partir d'un tableau de données.
     *
     * Cette méthode initialise une nouvelle entité 'Information' en utilisant les
     * données fournies dans le tableau '$data'. Elle gère également l'auteur de
     * l'information en fonction de la présence d'un identifiant d'administration.
     *
     * @param array $data      Un tableau associatif contenant les données de
     *                         l'entité, y compris :
     *                         - id (int): L'identifiant de l'information.
     *                         - title (string): Le titre de l'information.
     *                         - content (string): Le contenu de l'information.
     *                         - creation_date (string): La date de création au
     *                         format 'Y-m-d'.
     *                         - expiration_date (string): La date d'expiration au
     *                         format 'Y-m-d'.
     *                         - type (string): Le type de l'information.
     *                         - administration_id (int|null): L'identifiant de
     *                         l'administration, s'il existe.
     *                         - author (int): L'identifiant de l'auteur.
     * @param bool  $adminSite Indique si l'entité est pour un site
     *                         d'administration (par défaut : faux).
     *
     * @return Information L'instance d'entité 'Information' créée et initialisée
     *                     avec les données fournies.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function setEntity($data, bool $adminSite = false) : Information
    {
        $entity = new Information();
        $author = new User();
        $entity->setId($data['id']);
        $entity->setTitle($data['title']);
        $entity->setContent($data['content']);
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


    /**
     * Retourne l'identifiant de l'entité.
     *
     * @return int|null L'identifiant de l'entité, ou null si non défini.
     */
    public function getId() : ?int
    {
        return $this->_id;
    }

    /**
     * Définit l'identifiant de l'entité.
     *
     * @param int|null $_id L'identifiant à définir pour l'entité.
     *
     * @return void
     */
    public function setId(?int $_id): void
    {
        $this->_id = $_id;
    }

    /**
     * Retourne le titre de l'entité.
     *
     * @return string|null Le titre de l'entité, ou null si non défini.
     */
    public function getTitle() : ?string
    {
        return $this->_title;
    }

    /**
     * Définit le titre de l'entité.
     *
     * @param string|null $_title Le titre à définir pour l'entité.
     *
     * @return void
     */
    public function setTitle(?string $_title): void
    {
        $this->_title = $_title;
    }

    /**
     * Retourne l'auteur de l'entité.
     *
     * @return User|null L'auteur de l'entité, ou null si non défini.
     */
    public function getAuthor() : ?User
    {
        return $this->_author;
    }

    /**
     * Définit l'auteur de l'entité.
     *
     * @param User|null $_author L'auteur à définir pour l'entité.
     *
     * @return void
     */
    public function setAuthor(?User $_author): void
    {
        $this->_author = $_author;
    }

    /**
     * Retourne la date de création de l'entité.
     *
     * @return string La date de création sous forme de chaîne.
     */
    public function getCreationDate() : string
    {
        return $this->_creationDate;
    }

    /**
     * Définit la date de création de l'entité.
     *
     * @param null|string $_creationDate La date de création à définir,
     *                                   ou null si non défini.
     *
     * @return void
     */
    public function setCreationDate( ?string $_creationDate ): void
    {
        $this->_creationDate = $_creationDate;
    }

    /**
     * Retourne la date d'expiration de l'entité.
     *
     * @return string|null La date d'expiration sous forme de chaîne,
     * ou null si non définie.
     */
    public function getExpirationDate() : ?string
    {
        return $this->_expirationDate;
    }

    /**
     * Définit la date d'expiration de l'entité.
     *
     * @param string|null $_expirationDate La date d'expiration à définir,
     *                                     ou null si non défini.
     *
     * @return void
     */
    public function setExpirationDate(?string $_expirationDate): void
    {
        $this->_expirationDate = $_expirationDate;
    }

    /**
     * Retourne le contenu de l'entité.
     *
     * @return string|null Le contenu de l'entité, ou null si non défini.
     */
    public function getContent() : ?string
    {
        return $this->_content;
    }

    /**
     * Définit le contenu de l'entité.
     *
     * @param string|null $_content Le contenu à définir pour l'entité.
     *
     * @return void
     */
    public function setContent(?string $_content): void
    {
        $this->_content = $_content;
    }

    /**
     * Retourne le type de l'entité.
     *
     * @return string Le type de l'entité (ex : 'video', 'article', etc.).
     */
    public function getType() : string
    {
        return $this->_type;
    }

    /**
     * Définit le type de l'entité.
     *
     * @param string|null $_type Le type à définir pour l'entité.
     *
     * @return void
     */
    public function setType(?string $_type): void
    {
        $this->_type = $_type;
    }

    /**
     * Retourne l'identifiant de l'administrateur associé à l'entité.
     *
     * @return ?int L'identifiant de l'administrateur, ou null si non défini.
     */
    public function getAdminId() : null|int
    {
        return $this->_adminId;
    }

    /**
     * Définit l'identifiant de l'administrateur associé à l'entité.
     *
     * @param int|null $_adminId L'identifiant de l'administrateur à définir,
     *                           ou null si non défini.
     *
     * @return void
     */
    public function setAdminId( ?int $_adminId ): void
    {
        $this->_adminId = $_adminId;
    }

    /**
     * Retourne l'identifiant du département associé à l'entité.
     *
     * @return int|null L'identifiant du département, ou null si non défini.
     */
    public function getIdDepartment(): ?int
    {
        return $this->_idDepartment;
    }

    /**
     * Définit l'identifiant du département associé à l'entité.
     *
     * @param int|null $_idDepartment L'identifiant du département à définir,
     *                                ou null si non défini.
     *
     * @return void
     */
    public function setIdDepartment(?int $_idDepartment): void
    {
        $this->_idDepartment = $_idDepartment;
    }

    /**
     * Retourne la durée associée à l'entité.
     *
     * @return int|null La durée de l'entité, ou null si non définie.
     */
    public function getDuration(): ?int
    {
        return $this->_duration;
    }

    /**
     * Définit la durée associée à l'entité.
     *
     * @param int|null $_duration La durée à définir pour l'entité,
     *                            ou null si non défini.
     *
     * @return void
     */
    public function setDuration(?int $_duration): void
    {
        $this->_duration = $_duration;
    }


    /**
     * Sérialise l'objet en tableau associatif pour JSON.
     *
     * Cette méthode convertit l'objet actuel en un tableau associatif
     * contenant ses propriétés publiques et protégées. Cela permet une
     * sérialisation facile de l'objet en JSON, facilitant son export ou
     * son stockage.
     *
     * @return array Un tableau associatif contenant les propriétés de l'objet.
     *
     * @version 1.0
     * @date    2024-01-07
     */
    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}
