<?php
/**
 * Fichier Alert.php
 *
 * Ce fichier contient la classe `Alert`, qui représente une entité d'alerte
 * dans l'application. Cette classe permet de manipuler
 * les données liées aux alertes.
 *
 * PHP version 7.4 or later
 *
 * @category Model
 * @package  Models
 * @author   John Doe <johndoe@example.com>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @version  GIT: abcd1234abcd5678efgh9012ijkl3456mnop6789
 * @link     https://www.example.com/docs/Alert
 * Documentation de la classe
 * @since    2025-01-07
 */
namespace models;

use JsonSerializable;
use PDO;

/**
 * Class Alert
 *
 * Entité représentant une alerte.
 *
 * @category Model
 * @package  Models
 * @author   John Doe <johndoe@example.com>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @version  Release: 2.0.0
 * @link     https://www.example.com/docs/Alert Documentation de la classe
 * @since    2025-01-07
 */
class Alert extends Model implements Entity, JsonSerializable
{

    /**
     * L'identifiant unique de l'entité.
     *
     * @var int
     */
    private int $_id;

    /**
     *  L'auteur de l'entité, représenté par un objet de la classe `User`.
     *
     * @var User
     */
    private User $_author;

    /**
     *  Le contenu associé à l'entité, généralement un texte ou un fichier.
     *
     * @var string
     */
    private string $_content;

    /**
     *  La date de création de l'entité, au format 'Y-m-d H:i:s'.
     *
     * @var string
     */
    private string $_creation_date;

    /**
     *  La date d'expiration de l'entité, au format 'Y-m-d H:i:s'.
     *
     * @var string
     */
    private string $_expirationDate;

    /**
     *  Un tableau d'objets `CodeAde`, qui représente les codes associés à l'entité.
     *
     * @var CodeAde[]
     */
    private array $_codes;

    /**
     *  L'identifiant de l'administrateur associé à l'entité.
     *  Peut être `null` si l'entité n'a pas d'administrateur.
     *
     * @var int
     */
    private int $_adminId;



    /**
     * Insère une alerte dans la base de données et assigne des codes spécifiques si
     * nécessaire.
     *
     * Cette fonction insère une alerte avec des détails tels que l'auteur, le
     * contenu, la date de création, la date d'expiration, et si l'alerte est visible
     * par tout le monde.
     * Après avoir inséré l'alerte, elle récupère l'ID généré et associe les codes
     * spécifiques à cette alerte.
     *
     * @return int Retourne l'ID de l'alerte nouvellement insérée.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function insert() : int
    {
        $database = $this->getDatabase();
        $request = $database->prepare(
            '
            INSERT INTO ecran_alert (author, content, creation_date, expiration_date)
                   VALUES (:author, :content, :creation_date, :expirationDate)'
        );
        $request->bindValue(':author', $this->getAuthor(), PDO::PARAM_INT);
        $request->bindValue(':content', $this->getContent(), PDO::PARAM_STR);
        $request->bindValue(
            ':creation_date', $this->getCreationDate(),
            PDO::PARAM_STR
        );
        $request->bindValue(
            ':expirationDate', $this->getExpirationDate(),
            PDO::PARAM_STR
        );

        $request->execute();

        $id = $database->lastInsertId();

        foreach ( $this->getCodes() as $code ) {
            if ($code->getCode() != 'all' && $code->getCode() != 0 ) {
                $request = $database->prepare(
                    'INSERT INTO ecran_code_alert (alert_id, code_ade_id) 
                            VALUES (:idAlert, :idCodeAde)'
                );
                $request->bindParam(':idAlert', $id, PDO::PARAM_INT);
                $request->bindValue(':idCodeAde', $code->getId(), PDO::PARAM_INT);

                $request->execute();
            }
        }

        return $id;
    }

    /**
     * Met à jour une alerte existante dans la base de données.
     *
     * Cette fonction met à jour les informations d'une alerte spécifique, y compris
     * le contenu, la date d'expiration et la visibilité pour tout le monde.
     * Elle supprime d'abord tous les codes associés à l'alerte avant d'ajouter les
     * nouveaux codes associés, s'il y en a.
     *
     * @return int Retourne le nombre de lignes affectées par la requête de mise à
     * jour.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function update() : int
    {
        $database = $this->getDatabase();
        $request = $database->prepare(
            'UPDATE ecran_alert 
             SET content = :content, expiration_date = :expirationDate 
             WHERE id = :id'
        );
        $request->bindValue(':id', $this->getId(), PDO::PARAM_INT);
        $request->bindValue(':content', $this->getContent(), PDO::PARAM_STR);
        $request->bindValue(
            ':expirationDate', $this->getExpirationDate(),
            PDO::PARAM_STR
        );

        $request->execute();

        $count = $request->rowCount();

        $request = $database->prepare(
            'DELETE FROM ecran_code_alert 
                    WHERE alert_id = :alertId'
        );

        $request->bindValue(':alertId', $this->getId(), PDO::PARAM_INT);

        $request->execute();

        foreach ( $this->getCodes() as $code ) {
            if ($code->getCode() != 'all' && $code->getCode() != 0 ) {
                $request = $database->prepare(
                    'INSERT INTO ecran_code_alert (alert_id, code_ade_id) 
                     VALUES (:alertId, :codeAdeId)'
                );
                $request->bindValue(':alertId', $this->getId(), PDO::PARAM_INT);
                $request->bindValue(':codeAdeId', $code->getId(), PDO::PARAM_INT);

                $request->execute();

                $count += $request->rowCount();
            }
        }

        return $count;
    }

    /**
     * Supprime une alerte de la base de données.
     *
     * Cette fonction supprime l'alerte correspondant à l'ID spécifié de la base de
     * données.
     * Elle utilise une requête préparée pour éviter les injections SQL et retourne
     * le nombre de lignes affectées par la requête de suppression.
     *
     * @return int Retourne le nombre de lignes supprimées.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function delete() : int
    {
        $request = $this->getDatabase()->prepare(
            'DELETE FROM ecran_alert 
                    WHERE id = :id'
        );

        $request->bindValue(':id', $this->getId(), PDO::PARAM_INT);

        $request->execute();

        return $request->rowCount();
    }

    /**
     * Récupère une alerte de la base de données par son ID.
     *
     * Cette fonction exécute une requête préparée pour sélectionner une alerte
     * correspondant à l'ID fourni. Elle retourne l'entité alerte en utilisant
     * la méthode 'setEntity' pour initialiser ses attributs avec les données
     * récupérées.
     *
     * @param int $id L'ID de l'alerte à récupérer.
     *
     * @return mixed Retourne l'entité alerte ou null si aucune alerte n'est trouvée.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function get($id) : mixed
    {
        $request = $this->getDatabase()->prepare(
            'SELECT id, content, creation_date, expiration_date, author, 
       administration_id 
             FROM ecran_alert 
             WHERE id = :id LIMIT 1'
        );
        $request->bindParam(':id', $id, PDO::PARAM_INT);

        $request->execute();

        return $this->setEntity($request->fetch(PDO::FETCH_ASSOC));
    }

    /**
     * Récupère une liste d'alertes à partir de la base de données.
     *
     * Cette fonction exécute une requête préparée pour sélectionner un
     * ensemble d'alertes en fonction des paramètres fournis pour
     * la pagination (offset et limite). Elle retourne une liste d'entités
     * alertes en utilisant la méthode 'setEntityList' pour initialiser
     * les attributs avec les données récupérées.
     *
     * @param int $begin         Position de départ pour la récupération des alertes
     *                           (par défaut 0).
     * @param int $numberElement Nombre d'alertes à récupérer (par défaut 25).
     *
     * @return array Retourne un tableau d'entités alertes ou un tableau vide si
     *               aucune alerte n'est trouvée.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function getList($begin = 0, $numberElement = 25) : array
    {
        $request = $this->getDatabase()->prepare(
            "SELECT id, content, creation_date, expiration_date, author, 
       administration_id 
             FROM ecran_alert 
             ORDER BY id LIMIT :begin, :numberElement"
        );
        $request->bindValue(':begin', (int)$begin, PDO::PARAM_INT);
        $request->bindValue(':numberElement', (int)$numberElement, PDO::PARAM_INT);

        $request->execute();

        if ($request->rowCount() > 0) {
            return $this->setEntityList($request->fetchAll());
        }
        return [];
    }

    /**
     * Récupère une liste d'alertes créées par un auteur spécifique.
     *
     * Cette fonction exécute une requête préparée pour sélectionner les alertes
     * associées à un auteur donné, en fonction des paramètres fournis pour la
     * pagination (offset et limite). Elle retourne une liste d'entités alertes
     * en utilisant la méthode 'setEntityList' pour initialiser les attributs
     * avec les données récupérées.
     *
     * @param int $author        Identifiant de l'auteur dont on souhaite récupérer
     *                           les alertes.
     * @param int $begin         Position de départ pour la récupération des alertes
     *                           (par défaut  0).
     * @param int $numberElement Nombre d'alertes à récupérer (par défaut 25).
     *
     * @return array Retourne un tableau d'entités alertes ou un tableau vide si
     *               aucune alerte n'est trouvée.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function getAuthorListAlert(
        $author, $begin = 0, $numberElement = 25
    ) : array {
        $request = $this
            ->getDatabase()->prepare(
                "SELECT id, content, creation_date, expiration_date, author, 
       administration_id 
                 FROM ecran_alert  
                 WHERE author = :author 
                 ORDER BY id LIMIT :begin, :numberElement"
            );
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
     * Récupère une liste d'alertes depuis l'interface administrateur.
     *
     * Cette fonction exécute une requête préparée pour sélectionner jusqu'à
     * 200 alertes de la table 'ecran_alert', récupérant des informations
     * telles que l'identifiant, le contenu, l'auteur, la date d'expiration
     * et la date de création. Elle utilise ensuite la méthode 'setEntityList'
     * pour initialiser les attributs des alertes récupérées.
     *
     * @return array Retourne un tableau d'entités alertes ou un tableau vide si
     *               aucune alerte n'est trouvée.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function getFromAdminWebsite() : array
    {
        $request = $this->getDatabaseViewer()->prepare(
            'SELECT id, content, author, expiration_date, 
       creation_date 
             FROM ecran_alert LIMIT 200'
        );
        $request->execute();

        return $this->setEntityList($request->fetchAll(), true);
    }

    /**
     * Récupère les alertes pour un utilisateur spécifique.
     *
     * Cette méthode exécute une requête préparée pour sélectionner les alertes
     * associées à un utilisateur donné, en joignant plusieurs tables :
     * 'ecran_alert', 'ecran_code_alert', 'ecran_code_ade', et 'ecran_code_user'.
     * Elle filtre les résultats en fonction de l'identifiant de l'utilisateur passé
     * en paramètre.
     * Les alertes sont triées par date d'expiration (du plus ancien au plus récent).
     *
     * @param int $id Identifiant de l'utilisateur pour lequel les alertes sont
     *                récupérées.
     *
     * @return array Retourne un tableau d'entités alertes ou un tableau vide si
     * aucune alerte n'est trouvée.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function getForUser($id) : array
    {
        $request = $this->getDatabase()->prepare(
            'SELECT ecran_alert.id, content, creation_date, expiration_date, author, 
       administration_id
             FROM ecran_alert
                 JOIN ecran_code_alert ON ecran_alert.id = ecran_code_alert
                     .alert_id
                 JOIN ecran_code_ade ON ecran_code_alert
                     .code_ade_id = ecran_code_ade.id
                 JOIN ecran_code_user ON ecran_code_ade.id = ecran_code_user
                     .code_ade_id
             WHERE ecran_code_user.user_id = :id 
             ORDER BY expiration_date'
        );

        $request->bindParam(':id', $id, PDO::PARAM_INT);

        $request->execute();

        return $this->setEntityList($request->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * Récupère les alertes liées à un code spécifique.
     *
     * Cette méthode exécute une requête préparée pour sélectionner les alertes
     * associées à un identifiant de code d'alerte ('alert_id'). Elle joint
     * les tables 'ecran_code_alert' et 'ecran_alert' pour obtenir les détails
     * des alertes. Les résultats sont limités à 50 alertes.
     *
     * @return array Retourne un tableau d'entités alertes ou un tableau vide si
     *               aucune alerte n'est trouvée.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function getAlertLinkToCode() : array
    {
        $request = $this->getDatabase()->prepare(
            'SELECT ecran_alert.id, content, creation_date, expiration_date, author 
             FROM ecran_code_alert 
                 JOIN ecran_alert ON ecran_code_alert.alert_id = ecran_alert.id 
             WHERE alert_id = :alertId LIMIT 50'
        );
        $request->bindValue(':alertId', $this->getId(), PDO::PARAM_INT);

        $request->execute();

        return $this->setEntityList($request->fetchAll());
    }
    /**
     * Récupère les alertes administratives visibles sur le site.
     *
     * Cette méthode exécute une requête préparée pour sélectionner les alertes
     * dont l'identifiant d'administration n'est pas nul. Cela permet de
     * récupérer les alertes qui sont spécifiques à un groupe d'administration
     * et qui sont destinées à être affichées sur le site. Les résultats sont
     * limités à 500 alertes.
     *
     * @return array Retourne un tableau d'entités alertes ou un tableau vide si
     * aucune alerte n'est trouvée.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function getAdminWebsiteAlert() : array
    {
        $request = $this->getDatabase()->prepare(
            'SELECT id, content, author, expiration_date, creation_date 
             FROM ecran_alert 
             WHERE administration_id IS NOT NULL LIMIT 500'
        );
        $request->execute();

        return $this->setEntityList($request->fetchAll());
    }

    /**
     * Compte le nombre total d'alertes dans la base de données.
     *
     * Cette méthode exécute une requête préparée pour compter le nombre total
     * d'enregistrements dans la table 'ecran_alert'. Elle renvoie le total
     * des alertes présentes.
     *
     * @return int Retourne le nombre total d'alertes.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function countAll() : int
    {
        $request = $this->getDatabase()->prepare(
            "SELECT COUNT(*) 
                                                  FROM ecran_alert"
        );

        $request->execute();

        return $request->fetch()[0];
    }

    /**
     * Récupère une alerte spécifique depuis le site admin.
     *
     * Cette méthode exécute une requête préparée pour obtenir les détails
     * d'une alerte particulière à partir de son identifiant. Elle renvoie
     * les données de l'alerte sous forme d'entité si l'alerte existe,
     * sinon elle retourne false.
     *
     * @param int $id L'identifiant de l'alerte à récupérer.
     *
     * @return mixed Retourne l'entité de l'alerte si trouvée, sinon false.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function getAlertFromAdminSite($id) : mixed
    {
        $request = $this->getDatabaseViewer()->prepare(
            'SELECT id, content, author, expiration_date, creation_date 
             FROM ecran_alert 
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
     * Définit une liste d'entités à partir des données fournies.
     *
     * Cette méthode prend un tableau de données, crée une entité pour chaque
     * élément du tableau en utilisant la méthode 'setEntity', et retourne la
     * liste des entités créées. Si le paramètre '$adminSite' est vrai,
     * cela indique que les données proviennent du site admin, ce qui peut
     * influencer la façon dont les entités sont créées.
     *
     * @param array $dataList  La liste des données à convertir en
     *                         entités.
     * @param bool  $adminSite Indique si les données proviennent du site
     *                         admin.
     *
     * @return array La liste des entités créées.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function setEntityList($dataList, $adminSite = false) : array
    {
        $listEntity = array();
        foreach ($dataList as $data) {
            $listEntity[] = $this->setEntity($data, $adminSite);
        }
        return $listEntity;
    }

    /**
     * Crée et définit une entité Alert à partir des données fournies.
     *
     * Cette méthode initialise une nouvelle instance de l'entité 'Alert'
     * en remplissant ses attributs avec les données fournies. Elle gère
     * également l'attribution de l'auteur et des codes associés à l'alerte
     * en fonction de la provenance des données (site admin ou non).
     *
     * @param array $data      Les données de l'alerte à utiliser pour créer
     *                         l'entité.
     * @param bool  $adminSite Indique si les données proviennent du site
     *                         admin.
     *
     * @return Alert L'entité Alert créée et configurée.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function setEntity($data, $adminSite = false) : Alert
    {
        $entity = new Alert();
        $author = new User();
        $codeAde = new CodeAde();

        $entity->setId($data['id']);
        $entity->setContent($data['content']);
        $entity->setCreationDate(date('Y-m-d', strtotime($data['creation_date'])));
        $entity->setExpirationDate(
            date('Y-m-d', strtotime($data['expiration_date']))
        );

        if ($data['administration_id'] != null) {
            $author->setLogin('Administration');
            $entity->setAuthor($author);
        } else {
            $entity->setAuthor($author->get($data['author']));
        }

        $codes = array();
        foreach ( $codeAde->getByAlert($data['id']) as $code ) {
            $codes[] = $code;
        }
        $entity->setCodes($codes);

        return $entity;
    }

    /**
     * Récupère l'identifiant de l'entité.
     *
     * Cette méthode permet d'obtenir l'identifiant unique de l'entité,
     * représenté par un nombre entier.
     *
     * @return int L'identifiant unique de l'entité.
     *
     * @version 1.0.0
     * @date    2025-01-13
     */
    public function getId() : int
    {
        return $this->_id;
    }


    /**
     * Définit l'identifiant de l'entité.
     *
     * Cette méthode permet de définir l'identifiant de l'entité
     * en attribuant une nouvelle valeur
     * à la propriété `$id`. L'identifiant doit être un entier.
     *
     * @param int $_id L'identifiant à attribuer à l'entité.
     *
     * @return void
     *
     * @version 1.0.0
     * @date    2025-01-13
     */
    public function setId($_id) : void
    {
        $this->_id = $_id;
    }

    /**
     * Récupère l'auteur de l'entité.
     *
     * Cette méthode retourne l'objet `User` représentant l'auteur de l'entité.
     * L'auteur peut être un utilisateur, un administrateur,
     * ou toute autre personne ayant créé l'entité.
     *
     * @return User L'objet `User` représentant l'auteur de l'entité.
     *
     * @version 1.0.0
     * @date    2025-01-13
     */
    public function getAuthor() : User
    {
        return $this->_author;
    }

    /**
     * Définit l'auteur de l'entité.
     *
     * Cette méthode permet d'attribuer un auteur
     * à l'entité en affectant un objet `User`
     * à la propriété `$author`. Cela permet de
     * spécifier qui a créé ou est responsable
     * de l'entité.
     *
     * @param User $_author L'objet `User` représentant
     *                      l'auteur à attribuer à l'entité.
     *
     * @return void
     *
     * @version 1.0.0
     * @date    2025-01-13
     */
    public function setAuthor($_author) : void
    {
        $this->_author = $_author;
    }

    /**
     * Récupère le contenu de l'entité.
     *
     * Cette méthode retourne le contenu de
     * l'entité sous forme de chaîne de caractères.
     * Le contenu peut être un texte, un lien, ou tout autre type de donnée associée.
     *
     * @return string Le contenu de l'entité.
     *
     * @version 1.0.0
     * @date    2025-01-13
     */
    public function getContent() : string
    {
        return $this->_content;
    }

    /**
     * Définit le contenu de l'entité.
     *
     * Cette méthode permet d'attribuer un contenu
     * à l'entité en affectant une chaîne de caractères.
     *
     * @param string $_content Le contenu à attribuer à l'entité.
     *
     * @return void
     *
     * @version 1.0.0
     * @date    2025-01-13
     */
    public function setContent(string $_content) : void
    {
        $this->_content = $_content;
    }

    /**
     * Récupère la date de création de l'entité.
     *
     * Cette méthode retourne la date de création
     * de l'entité sous forme de chaîne de caractères.
     * La date est généralement au format `Y-m-d` (année-mois-jour).
     *
     * @return string La date de création de l'entité.
     *
     * @version 1.0.0
     * @date    2025-01-13
     */
    public function getCreationDate() : string
    {
        return $this->_creation_date;
    }

    /**
     * Définit la date de création de l'entité.
     *
     * Cette méthode permet d'attribuer une date de création
     * à l'entité en affectant une chaîne de caractères.
     *
     * @param string $_creation_date La date de création
     *                               à attribuer à l'entité (au format `Y-m-d`).
     *
     * @return void
     *
     * @version 1.0.0
     * @date    2025-01-13
     */
    public function setCreationDate(string $_creation_date) : void
    {
        $this->_creation_date = $_creation_date;
    }

    /**
     * Récupère la date d'expiration de l'entité.
     *
     * Cette méthode retourne la date d'expiration de l'entité sous
     * forme de chaîne de caractères.
     * La date est généralement au format `Y-m-d` (année-mois-jour).
     *
     * @return string La date d'expiration de l'entité.
     *
     * @version 1.0.0
     * @date    2025-01-13
     */
    public function getExpirationDate() : string
    {
        return $this->_expirationDate;
    }

    /**
     * Définit la date d'expiration de l'entité.
     *
     * Cette méthode permet d'attribuer une date d'expiration à l'entité
     * en affectant une chaîne de caractères.
     *
     * @param string $_expirationDate La date d'expiration
     *                                à attribuer à l'entité (au format `Y-m-d`).
     *
     * @return void
     *
     * @version 1.0.0
     * @date    2025-01-13
     */
    public function setExpirationDate(string $_expirationDate) : void
    {
        $this->_expirationDate = $_expirationDate;
    }


    /**
     * Récupère les codes associés à l'entité.
     *
     * Cette méthode retourne un tableau d'objets `CodeAde` représentant les codes
     * associés à l'entité. Ces codes peuvent être utilisés pour diverses
     * fonctionnalités.
     *
     * @return CodeAde[] Un tableau d'objets `CodeAde`
     * représentant les codes associés.
     *
     * @version 1.0.0
     * @date    2025-01-13
     */
    public function getCodes()
    {
        return $this->_codes;
    }

    /**
     * Définit les codes associés à l'entité.
     *
     * Cette méthode permet d'attribuer un tableau d'objets `CodeAde` à l'entité.
     * Les codes peuvent être utilisés pour divers traitements ou fonctionnalités.
     *
     * @param CodeAde[] $_codes Un tableau d'objets `CodeAde`
     *                          représentant les codes à associer.
     *
     * @return void
     *
     * @version 1.0.0
     * @date    2025-01-13
     */
    public function setCodes(array $_codes) : void
    {
        $this->_codes = $_codes;
    }

    /**
     * Récupère l'identifiant de l'administrateur associé à l'entité.
     *
     * Cette méthode retourne l'identifiant de
     * l'administrateur qui est lié à l'entité.
     * Elle retourne `null` si aucun administrateur n'est associé.
     *
     * @return int|null L'identifiant de l'administrateur ou
     * `null` si aucun administrateur n'est défini.
     *
     * @version 1.0.0
     * @date    2025-01-13
     */
    public function getAdminId() : ?int
    {
        return $this->_adminId;
    }

    /**
     * Définit l'identifiant de l'administrateur associé à l'entité.
     *
     * Cette méthode permet d'attribuer un identifiant d'administrateur à l'entité.
     *
     * @param int $_adminId L'identifiant de l'administrateur à associer à l'entité.
     *
     * @return void
     *
     * @version 1.0.0
     * @date    2025-01-13
     */
    public function setAdminId(int $_adminId) : void
    {
        $this->_adminId = $_adminId;
    }


    /**
     * Sérialise l'objet en un tableau associatif pour le format JSON.
     *
     * Implémente l'interface `JsonSerializable` afin de permettre la conversion
     * de l'objet en une structure de données JSON. Cette méthode utilise
     * `get_object_vars` pour récupérer les propriétés accessibles de l'objet
     * sous forme de tableau associatif.
     *
     * @return array Tableau associatif représentant les propriétés de l'objet.
     *
     * @version 1.0
     * @date    2024-10-16
     */
    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}
