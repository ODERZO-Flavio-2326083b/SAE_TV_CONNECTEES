<?php
/**
 * Fichier CodeAde.php
 *
 * Ce fichier contient la classe 'CodeAde', qui représente une entité de code ADE
 * dans l'application. Cette classe permet de
 * manipuler les données liées aux
 * codes ADE.
 *
 * PHP version 8.3
 *
 * @category Model
 * @package  Models
 * @author   BUT Informatique, AMU <iut-aix-scol@univ-amu.fr>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @version  GIT: abcd1234abcd5678efgh9012ijkl3456mnop6789
 * @link     https://www.example.com/docs/CodeAde
 * Documentation de la classe
 * @since    2025-01-07
 */
namespace models;

use JsonSerializable;
use PDO;

/**
 * Class CodeAde
 *
 * Entité représentant un code ADE.
 *
 * @category Model
 * @package  Models
 * @author   BUT Informatique, AMU <iut-aix-scol@univ-amu.fr>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @version  Release: 2.0.0
 * @link     https://www.example.com/docs/CodeAde Documentation de la classe
 * @since    2025-01-07
 */
class CodeAde extends Model implements Entity, JsonSerializable
{


    /**
     * Identifiant unique de l'entité.
     *
     * Cette propriété contient l'identifiant
     * unique de l'entité. Elle est utilisée pour
     * identifier de manière unique chaque instance de l'objet.
     *
     * @var int L'identifiant unique de l'entité.
     *
     * @version 1.0.0
     * @date    2025-01-13
     */
    private int $_id;

    /**
     * Type de l'entité.
     *
     * Cette propriété décrit le type de l'entité.
     * Elle peut avoir les valeurs suivantes :
     * - "year" : Représente une entité de type année.
     * - "group" : Représente une entité de type groupe.
     * - "halfGroup" : Représente une entité de type demi-groupe.
     *
     * @var string Le type de l'entité (peut être "year", "group" ou "halfGroup").
     *
     * @version 1.0.0
     * @date    2025-01-13
     */
    private string $_type;

    /**
     * Titre de l'entité.
     *
     * Cette propriété contient le titre de l'entité. Elle est utilisée pour fournir
     * un nom ou un titre à l'entité.
     *
     * @var string Le titre de l'entité.
     *
     * @version 1.0.0
     * @date    2025-01-13
     */
    private string $_title;

    /**
     * Code de l'entité.
     *
     * Cette propriété contient un code qui peut être soit une chaîne de caractères,
     * soit un entier, en fonction du contexte de l'entité. Ce code peut être utilisé
     * pour des références, des identifications
     * supplémentaires ou pour des traitements.
     *
     * @var string | int Le code associé à l'entité, qui
     * peut être une chaîne de caractères ou un entier.
     *
     * @version 1.0.0
     * @date    2025-01-13
     */
    private $_code;

    /**
     * Identifiant du département auquel l'entité appartient.
     *
     * Cette propriété contient l'identifiant
     * du département auquel l'entité est associée.
     * Il permet de relier l'entité à un département spécifique.
     *
     * @var int L'identifiant du département.
     *
     * @version 1.0.0
     * @date    2025-01-13
     */
    private int $_deptId;

    /**
     Insère un nouvel enregistrement dans la table 'ecran_code_ade'.
     *
     * Cette méthode utilise les propriétés de l'objet courant pour insérer
     * un enregistrement dans la base de données. Les champs 'type', 'title' et
     * 'code' doivent être préalablement définis via les méthodes 'setType',
     * 'setTitle' et 'setCode'.
     *
     * @return false|string L'ID de l'enregistrement inséré, tel que retourné par la
     * base de données.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function insert() : string
    {
        $database = $this->getDatabase();
        $request = $database->prepare(
            'INSERT INTO ecran_code_ade (type, title, code, dept_id) 
             VALUES (:type, :title, :code, :dept_id)'
        );

        $request->bindValue(':title', $this->getTitle(), PDO::PARAM_STR);
        $request->bindValue(':code', $this->getCode(), PDO::PARAM_STR);
        $request->bindValue(':type', $this->getType(), PDO::PARAM_STR);
        $request->bindValue(':dept_id', $this->getDeptId(), PDO::PARAM_INT);

        $request->execute();

        return $database->lastInsertId();
    }

    /**
     * Met à jour un enregistrement existant dans la table 'ecran_code_ade'.
     *
     * Cette méthode prépare et exécute une requête de mise à jour pour modifier
     * les colonnes 'title', 'code' et 'type' d'un enregistrement spécifique
     * identifié par son 'id'. La méthode retourne le nombre de lignes affectées
     * par l'opération.
     *
     * @return int Le nombre de lignes mises à jour.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function update() : int
    {
        $request = $this->getDatabase()->prepare(
            'UPDATE ecran_code_ade 
             SET title = :title, code = :code, type = :type, dept_id = :dept_id 
             WHERE id = :id'
        );
        $request->bindValue(':id', $this->getId(), PDO::PARAM_INT);
        $request->bindValue(':title', $this->getTitle(), PDO::PARAM_STR);
        $request->bindValue(':code', $this->getCode(), PDO::PARAM_STR);
        $request->bindValue(':type', $this->getType(), PDO::PARAM_STR);
        $request->bindValue(':dept_id', $this->getDeptId(), PDO::PARAM_INT);

        $request->execute();

        return $request->rowCount();
    }

    /**
     * Supprime un enregistrement dans la table 'ecran_code_ade'.
     *
     * Cette méthode prépare et exécute une requête pour supprimer
     * un enregistrement identifié par son 'id'. La méthode retourne le
     * nombre de lignes affectées par l'opération, ce qui indique
     * si la suppression a été effectuée avec succès.
     *
     * @return int Le nombre de lignes supprimées.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function delete() : int
    {
        $request = $this->getDatabase()->prepare(
            'DELETE FROM ecran_code_ade 
             WHERE id = :id'
        );

        $request->bindValue(':id', $this->getId(), PDO::PARAM_INT);

        $request->execute();

        return $request->rowCount();
    }

    /**
     * Récupère un enregistrement de la table 'ecran_code_ade' en fonction de son
     * identifiant.
     *
     * Cette méthode prépare et exécute une requête pour récupérer les informations
     * d'un code identifié par son 'id'.
     * Si l'enregistrement existe, il est renvoyé sous forme d'entité, sinon la
     * méthode retourne 'false'.
     *
     * @param int $id L'identifiant du code à récupérer.
     *
     * @return mixed L'entité correspondante si trouvée, sinon 'false'.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function get($id) : mixed
    {
        $request = $this->getDatabase()->prepare(
            'SELECT id, title, code, type, dept_id 
             FROM ecran_code_ade 
             WHERE id = :id LIMIT 1'
        );

        $request->bindParam(':id', $id, PDO::PARAM_INT);

        $request->execute();

        if ($request->rowCount() > 0) {
            return $this->setEntity($request->fetch(PDO::FETCH_ASSOC));
        }
        return false;
    }

    /**
     * Récupère une liste d'enregistrements de la table 'ecran_code_ade'.
     *
     * Cette méthode prépare et exécute une requête pour obtenir jusqu'à
     * 1000 enregistrements de codes, triés par identifiant dans l'ordre décroissant.
     * Les résultats sont retournés sous forme d'une liste d'entités.
     *
     * @return array Une liste d'entités représentant les codes.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function getList() : array
    {
        $request = $this->getDatabase()->prepare(
            'SELECT id, title, code, type, dept_id
             FROM ecran_code_ade 
             ORDER BY id DESC LIMIT 1000'
        );

        $request->execute();

        return $this->setEntityList($request->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * Vérifie si un code ou un titre existe déjà dans la table 'ecran_code_ade'.
     *
     * Cette méthode prépare et exécute une requête pour sélectionner les
     * enregistrements dont le titre ou le code correspondent aux valeurs fournies.
     * Elle limite les résultats à deux enregistrements. Cela permet de s'assurer que
     * les titres et les codes sont uniques.
     *
     * @param string $title Le titre à vérifier.
     * @param string $code  Le code à
     *                      vérifier.
     *
     * @return array Une liste d'entités représentant les codes correspondants.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function checkCode($title, $code) : array
    {
        $request = $this->getDatabase()->prepare(
            'SELECT id, title, code, type, dept_id 
             FROM ecran_code_ade 
             WHERE title = :title OR code = :code LIMIT 2'
        );
        $request->bindParam(':title', $title, PDO::PARAM_STR);
        $request->bindParam(':code', $code, PDO::PARAM_STR);
        $request->execute();
        return $this->setEntityList($request->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * Récupère tous les enregistrements de la table 'ecran_code_ade' correspondant à
     * un type donné.
     *
     * Cette méthode prépare et exécute une requête pour sélectionner tous les
     * enregistrements dont le type correspond à celui fourni. Les résultats sont
     * triés par identifiant dans l'ordre décroissant et limités à 500
     * enregistrements.
     *
     * @param string $type Le type des enregistrements à récupérer.
     *
     * @return array Une liste d'entités représentant les codes du type spécifié.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function getAllFromType($type) : array
    {
        $request = $this->getDatabase()->prepare(
            'SELECT id, title, code, type, dept_id
             FROM ecran_code_ade 
             WHERE type = :type 
             ORDER BY id DESC LIMIT 500'
        );
        $request->bindParam(':type', $type, PDO::PARAM_STR);
        $request->execute();
        return $this->setEntityList($request->fetchAll(PDO::FETCH_ASSOC));
    }

    public function getAllFromTypeAndDept($id_dept, $type) : array
    {
        $request = $this->getDatabase()->prepare(
            'SELECT id, title, code, type, dept_id
             FROM ecran_code_ade 
             WHERE type = :type 
             AND dept_id = :id_dept
             ORDER BY id DESC LIMIT 500'
        );
        $request->bindParam(':type', $type, PDO::PARAM_STR);
        $request->bindParam(':id_dept', $id_dept, PDO::PARAM_INT);
        $request->execute();
        return $this->setEntityList($request->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * Récupère un enregistrement de la table 'ecran_code_ade' correspondant à un
     * code spécifique.
     *
     * Cette méthode prépare et exécute une requête pour sélectionner un
     * enregistrement dont le code correspond à celui fourni. Si un enregistrement
     * est trouvé, il est renvoyé sous forme d'entité.
     *
     * @param string $code Le code de l'enregistrement à récupérer.
     *
     * @return mixed L'entité correspondant au code spécifié, ou false si aucun
     * enregistrement n'est trouvé.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function getByCode($code) : mixed
    {
        $request = $this->getDatabase()->prepare(
            'SELECT id, title, code, type, dept_id
             FROM ecran_code_ade 
             WHERE code = :code LIMIT 1'
        );
        $request->bindParam(':code', $code, PDO::PARAM_STR);
        $request->execute();
        return $this->setEntity($request->fetch(PDO::FETCH_ASSOC));
    }

    /**
     * Récupère une liste d'enregistrements de la table 'ecran_code_ade' associés à
     * un identifiant d'alerte.
     *
     * Cette méthode prépare et exécute une requête pour sélectionner les
     * enregistrements de la table 'ecran_code_ade' qui sont liés à une alerte
     * spécifique, identifiée par son ID. La requête utilise une jointure avec la
     * table 'ecran_code_alert' pour récupérer les codes associés.
     *
     * @param int $id L'identifiant de l'alerte pour laquelle les codes doivent être
     *                récupérés.
     *
     * @return array Une liste d'entités correspondant aux codes associés à l'alerte.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function getByAlert($id) : array
    {
        $request = $this->getDatabase()->prepare(
            'SELECT id, title, code, type, dept_id
             FROM ecran_code_ade 
                 JOIN ecran_code_alert 
                     ON ecran_code_ade.id = ecran_code_alert.code_ade_id 
             WHERE alert_id = :id 
             LIMIT 100'
        );
        $request->bindParam(':id', $id, PDO::PARAM_INT);
        $request->execute();
        return $this->setEntityList($request->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * Crée une instance de 'CodeAde' à partir des données fournies.
     *
     * Cette méthode initialise un nouvel objet 'CodeAde' en utilisant les données
     * passées sous forme de tableau associatif. Les propriétés de l'objet sont
     * définies en fonction des valeurs correspondantes dans le tableau.
     *
     * @param array $data Un tableau associatif contenant les données nécessaires
     *                    pour initialiser l'objet.
     *                    Doit contenir les clés 'id', 'title', 'code' et 'type'.
     *
     * @return CodeAde L'objet 'CodeAde' créé et initialisé avec les données
     * fournies.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function setEntity($data) : CodeAde
    {
        $entity = new CodeAde();

        $entity->setId($data['id']);
        $entity->setTitle($data['title']);
        $entity->setCode($data['code']);
        $entity->setType($data['type']);
        $entity->setDeptId($data['dept_id']);

        return $entity;
    }

    /**
     * Crée une liste d'instances de 'CodeAde' à partir d'un tableau de données.
     *
     * Cette méthode parcourt un tableau de données et utilise la méthode 'setEntity'
     * pour créer une instance de 'CodeAde' pour chaque entrée. Elle renvoie ensuite
     * un tableau contenant toutes les instances créées.
     *
     * @param array $dataList Un tableau contenant des données associatives pour
     *                        chaque 'CodeAde'.
     *                        Chaque élément du tableau doit être un tableau
     *                        contenant les clés nécessaires pour initialiser un
     *                        objet 'CodeAde'.
     *
     * @return array Un tableau d'instances 'CodeAde' créées à partir des données
     * fournies.
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
     * Récupère le code de l'entité.
     *
     * Cette méthode retourne le code
     * associé à l'entité. Le code peut être un identifiant
     * unique ou une autre référence utilisée pour l'entité.
     *
     * @return int Le code de l'entité.
     *
     * @version 1.0.0
     * @date    2025-01-13
     */
    public function getCode() : int
    {
        return $this->_code;
    }

    /**
     * Définit le code de l'entité.
     *
     * Cette méthode permet de définir le code associé à l'entité. Le code peut être
     * un identifiant ou une autre référence pour l'entité.
     *
     * @param $_code Le code de l'entité à définir.
     *
     * @return void
     *
     * @version 1.0.0
     * @date    2025-01-13
     */
    public function setCode($_code): void
    {
        $this->_code = $_code;
    }

    /**
     * Récupère l'identifiant de l'entité.
     *
     * Cette méthode retourne l'identifiant unique de l'entité.
     *
     * @return int L'identifiant de l'entité.
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
     * Cette méthode permet de définir l'identifiant unique de l'entité.
     *
     * @param $_id L'identifiant de l'entité à définir.
     *
     * @return void
     *
     * @version 1.0.0
     * @date    2025-01-13
     */
    public function setId($_id): void
    {
        $this->_id = $_id;
    }

    /**
     * Récupère le type de l'entité.
     *
     * Cette méthode retourne le type de l'entité. Le type peut être une valeur
     * comme "year", "group" ou "halfGroup" pour définir le type de l'entité.
     *
     * @return string Le type de l'entité.
     *
     * @version 1.0.0
     * @date    2025-01-13
     */
    public function getType() : string
    {
        return $this->_type;
    }

    /**
     * Définit le type de l'entité.
     *
     * Cette méthode permet de définir le type de l'entité. Le type peut être une
     * valeur comme "year", "group" ou "halfGroup".
     *
     * @param $_type Le type de l'entité à définir.
     *
     * @return void
     *
     * @version 1.0.0
     * @date    2025-01-13
     */
    public function setType($_type): void
    {
        $this->_type = $_type;
    }

    /**
     * Récupère le titre de l'entité.
     *
     * Cette méthode retourne le titre de l'entité.
     *
     * @return string Le titre de l'entité.
     *
     * @version 1.0.0
     * @date    2025-01-13
     */
    public function getTitle() : string
    {
        return $this->_title;
    }

    /**
     * Définit le titre de l'entité.
     *
     * Cette méthode permet de définir le titre de l'entité.
     *
     * @param $_title Le titre de l'entité à définir.
     *
     * @return void
     *
     * @version 1.0.0
     * @date    2025-01-13
     */
    public function setTitle($_title): void
    {
        $this->_title = $_title;
    }

    /**
     * Récupère l'identifiant du département auquel l'entité appartient.
     *
     * Cette méthode retourne l'identifiant du
     * département auquel l'entité est associée.
     *
     * @return int L'identifiant du département.
     *
     * @version 1.0.0
     * @date    2025-01-13
     */
    public function getDeptId(): int
    {
        return $this->_deptId;
    }

    /**
     * Définit l'identifiant du département auquel l'entité appartient.
     *
     * Cette méthode permet de définir l'identifiant du département auquel l'entité
     * doit être associée.
     *
     * @param int $_deptId L'identifiant du département à définir.
     *
     * @return void
     *
     * @version 1.0.0
     * @date    2025-01-13
     */
    public function setDeptId(int $_deptId): void
    {
        $this->_deptId = $_deptId;
    }


    /**
     * Sérialise l'objet en un tableau associatif compatible JSON.
     *
     * Implémente l'interface 'JsonSerializable', permettant la conversion
     * de l'objet en une structure JSON. Cette méthode retourne un tableau
     * associatif contenant toutes les propriétés accessibles de l'objet.
     *
     * @return array Tableau associatif des propriétés de l'objet.
     *
     * @version 1.0
     * @date    2024-10-16
     */
    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}
