<?php
// TODO : Ajouter la doc du fichier
namespace models;

use JsonSerializable;
use PDO;

/**
 * TODO : Ajouter les tags @author, @category, @license et @link
 * Classe Department
 *
 * Représente un département dans l'application, permettant de gérer
 * ses informations, telles que son nom et son ID. Cette classe fournit
 * des méthodes pour insérer, mettre à jour, supprimer, et récupérer des
 * départements dans la base de données. Elle est également utilisée pour
 * récupérer les départements associés à un utilisateur.
 *
 * @package models
 */
class Department extends Model implements Entity, JsonSerializable
{

    // TODO : Ajouter une description
    /**
     * @var int
     */
    private int $id_department;

    // TODO : Ajouter une description
    /**
     * @var string
     */
    private string $name;

    /**
     * Insère un département dans la base de données selon les attributs actuels.
     *
     * Cette méthode prépare et exécute une requête SQL pour insérer un
     * nouveau département dans la table `ecran_departement` en utilisant
     * le nom du département. Le département est ajouté avec les données
     * actuellement définies dans l'objet.
     *
     * @return string L'ID du département inséré dans la base de données.
     *
     * @version 1.0
     * @date    2024-01-07
     */
    public function insert(): string
    {
        $database = $this->getDatabase();
        $request = $database->prepare(
            'INSERT INTO ecran_departement (dept_nom) 
VALUES (:name)'
        );
        $request->bindValue(':name', $this->getName());
        $request->execute();
        return $database->lastInsertId();
    }

    /**
     * Met à jour un département de la base de données selon les attributs actuels.
     *
     * Cette méthode prépare et exécute une requête SQL pour mettre à jour
     * les informations d'un département dans la table `ecran_departement`.
     * Elle met à jour le nom du département en fonction de l'ID spécifié.
     *
     * @return int Le nombre de lignes affectées par la requête de mise à jour.
     *
     * @version 1.0
     * @date    2024-01-07
     */
    public function update(): int
    {
        $database = $this->getDatabase();
        $request = $database->prepare(
            'UPDATE ecran_departement SET dept_nom = :name 
                         WHERE dept_id = :id'
        );
        $request->bindValue(':name', $this->getName());
        $request->bindValue(':id', $this->getIdDepartment());
        $request->execute();
        return $request->rowCount();
    }

    /**
     * Supprime un département de la base de données selon les attributs actuels.
     *
     * Cette méthode prépare et exécute une requête SQL pour supprimer un département
     * de la table `ecran_departement` en fonction de l'ID spécifié.
     *
     * @return int Le nombre de lignes affectées par la requête de suppression.
     *
     * @version 1.0
     * @date    2024-01-07
     */
    public function delete(): int
    {
        $request = $this->getDatabase()->prepare(
            'DELETE FROM ecran_departement 
       WHERE dept_id = :id'
        );
        $request->bindValue(':id', $this->getIdDepartment(), PDO::PARAM_INT);
        $request->execute();
        return $request->rowCount();
    }

    /**
     * Récupère le département en fonction de son ID.
     *
     * Cette méthode prépare et exécute une requête SQL pour récupérer les
     * informations d'un département spécifique à partir de son ID. Elle retourne un
     * objet `Department` si le département est trouvé, ou `false` si aucune donnée
     * n'est trouvée.
     *
     * @param int $id L'ID du département à récupérer.
     *
     * @return bool|Department Un objet `Department` si le département existe, sinon
     * `false`.
     *
     * @version 1.0
     * @date    2024-01-07
     */
    public function get( $id ): bool|Department
    {
        $request = $this->getDatabase()->prepare(
            'SELECT dept_id, dept_nom 
FROM ecran_departement WHERE dept_id = :id'
        );
        $request->bindValue(':id', $id, PDO::PARAM_INT);
        $request->execute();
        if ($request->rowCount() > 0) {
            return $this->setEntity($request->fetch(PDO::FETCH_ASSOC));
        }
        return false;
    }

    /**
     * Récupère une liste de départements à partir de la base de données, avec une
     * pagination.
     *
     * Cette méthode prépare et exécute une requête SQL pour récupérer une liste de
     * départements depuis la base de données, avec un nombre limité de résultats en
     * fonction des paramètres de pagination fournis. Elle retourne un tableau
     * d'objets `Department` correspondant aux départements dans la plage spécifiée.
     *
     * @param int $begin         Le point de départ (index) pour la récupération des
     *                           départements (par défaut 0).
     * @param int $numberElement Le nombre de départements à récupérer (par défaut
     *                           25).
     *
     * @return array Un tableau d'objets `Department` correspondant aux départements
     * récupérés.
     *
     * @version 1.0
     * @date    2024-01-07
     */
    public function getList( int $begin = 0, int $numberElement = 25 ): array
    {
        $request = $this->getDatabase()->prepare(
            'SELECT dept_id, dept_nom 
FROM ecran_departement ORDER BY dept_id LIMIT :begin, :numberElement'
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
     * Récupère un département par son nom dans la base de données.
     *
     * Cette méthode prépare et exécute une requête SQL pour rechercher un
     * département en fonction de son nom dans la base de données. Elle retourne un
     * tableau contenant les résultats correspondant au nom donné.
     *
     * @param string $name Le nom du département à rechercher.
     *
     * @return array|Department Un tableau d'objets `Department` correspondant aux
     *                          départements trouvés, ou un seul objet `Department`
     *                          si un seul résultat est trouvé.
     *
     * @version 1.0
     * @date    2024-01-07
     */
    public function getDepartmentByName($name) : array|Department
    {
        $request = $this->getDatabase()->prepare(
            'SELECT dept_id, dept_nom 
FROM ecran_departement WHERE dept_nom = :name LIMIT 1'
        );
        $request->bindValue(':name', $name);
        $request->execute();
        return $this->setEntityList($request->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * @inheritDoc
     *
     * Crée et retourne un objet `Department` à partir des données fournies.
     *
     * Cette méthode prend un tableau associatif de données et crée un nouvel objet
     * `Department` en initialisant ses propriétés à partir des valeurs du tableau.
     * Elle retourne ensuite l'entité `Department` ainsi créée.
     *
     * @param array $data Tableau associatif contenant les données du département à
     *                    utiliser pour l'entité.
     *
     * @return Department L'entité `Department` créée et initialisée avec les données
     * fournies.
     *
     * @version 1.0
     * @date    2024-01-07
     */
    public function setEntity($data): Department
    {
        $entity = new Department();
        $entity->setIdDepartment($data['dept_id']);
        $entity->setName($data['dept_nom']);
        return $entity;
    }

    /**
     * @inheritDoc
     *
     * Crée et retourne une liste d'objets `Department` à partir des données
     * fournies.
     *
     * Cette méthode prend un tableau de données et pour chaque entrée dans le
     * tableau, elle crée un objet `Department` en utilisant la méthode `setEntity`,
     * puis ajoute l'objet créé à une liste. La liste complète d'objets `Department`
     * est ensuite retournée.
     *
     * @param array $dataList  Tableau contenant les données des départements à
     *                         utiliser pour créer les entités.
     * @param bool  $adminSite (Optionnel) Indicateur de l'état du site
     *                         administrateur, non utilisé dans cette méthode.
     *
     * @return Department[] Tableau d'objets `Department` créés à partir des données
     *                      fournies.
     *
     * @version 1.0
     * @date    2024-01-07
     */
    public function setEntityList($dataList, $adminSite = false)
    {
        $listEntity = array();
        foreach ($dataList as $data) {
            $listEntity[] = $this->setEntity($data);
        }
        return $listEntity;
    }

    /**
     * Renvoie tous les départements stockés dans la base de données.
     *
     * Cette méthode interroge la base de données pour récupérer tous les
     * départements enregistrés, puis utilise la méthode `setEntityList` pour
     * convertir les résultats en une liste d'objets `Department`.
     * La liste complète des départements est ensuite retournée.
     *
     * @return Department[] Tableau d'objets `Department` représentant tous les
     *                      départements stockés dans la base de données.
     *
     * @version 1.0
     * @date    2024-01-07
     */
    public function getAllDepts(): array
    {
        $request = $this->getDatabase()->prepare(
            'SELECT dept_id, dept_nom 
FROM ecran_departement ORDER BY dept_id'
        );
        $request->execute();
        return $this->setEntityList($request->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * Renvoie tous les départements stockés dans la base de données
     * qui n'ont aucun sous administrateur.
     *
     * @return Department[]
     */
    public function getAllDeptsWithoutSubadmin(): array
    {
        $request = $this->getDatabase()->prepare(
            '
		SELECT d.dept_id AS dept_id, d.dept_nom AS dept_nom
		FROM ecran_departement d
		WHERE NOT EXISTS (
		    SELECT 1
		    FROM ecran_user_departement u
		    JOIN wp_usermeta um 
		      ON u.user_id = um.user_id
		     AND um.meta_key = \'wp_capabilities\'
		     AND um.meta_value LIKE \'%subadmin%\'
		    WHERE u.dept_id = d.dept_id);
		'
        );

        $request->execute();

        return $this->setEntityList($request->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * Renvoie le département associé à un utilisateur donné par son ID.
     *
     * Cette méthode effectue une jointure entre la table des départements et la
     * table des relations utilisateur-département pour récupérer le département de
     * l'utilisateur spécifié par son ID.
     * Le département correspondant est ensuite renvoyé sous forme d'un objet
     * `Department`.
     *
     * @param int $userId L'ID de l'utilisateur dont on souhaite récupérer le
     *                    département.
     *
     * @return Department L'objet `Department` représentant le département associé à
     * l'utilisateur.
     *
     * @version 1.0
     * @date    2024-01-07
     */
    public function getUserDepartment(int $userId): Department
    {
        $request = $this->getDatabase()->prepare(
            'SELECT d.dept_id as dept_id, d.dept_nom as dept_nom 
                                                        FROM ecran_departement d
                                                         JOIN 
                                                            ecran_user_departement u 
                                                                ON d.dept_id = u
                                                                    .dept_id
                                                         WHERE u.user_id = :id'
        );
        $request->bindValue(':id', $userId);
        $request->execute();
        return $this->setEntity($request->fetch(PDO::FETCH_ASSOC));
    }

    /**
     * Récupère l'ID du département.
     *
     * Cette méthode retourne l'ID du département associé à l'objet courant.
     *
     * @return int L'ID du département.
     *
     * @version 1.0
     * @date    2024-01-07
     */
    public function getIdDepartment(): int
    {
        return $this->id_department;
    }

    /**
     * Définit l'ID du département.
     *
     * Cette méthode permet de définir l'ID du département associé à l'objet courant.
     *
     * @param int $id_department L'ID du département à définir.
     *
     * @return void
     *
     * @version 1.0
     * @date    2024-01-07
     */
    public function setIdDepartment( int $id_department ): void
    {
        $this->id_department = $id_department;
    }

    /**
     * Récupère le nom du département.
     *
     * Cette méthode permet d'obtenir le nom du département associé à l'objet
     * courant.
     *
     * @return string Le nom du département.
     *
     * @version 1.0
     * @date    2024-01-07
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Définit le nom du département.
     *
     * Cette méthode permet d'assigner un nom au département en cours.
     *
     * @param string $name Le nom du département à attribuer.
     *
     * @return void
     *
     * @version 1.0
     * @date    2024-01-07
     */
    public function setName( string $name ): void
    {
        $this->name = $name;
    }

    /**
     * Sérialise l'objet en tableau associatif pour JSON.
     *
     * Cette méthode transforme l'objet actuel en un tableau associatif
     * représentant ses propriétés, ce qui permet de le convertir en JSON
     * facilement.
     *
     * @return array Un tableau associatif des propriétés de l'objet.
     *
     * @version 1.0
     * @date    2024-01-07
     */
    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}
