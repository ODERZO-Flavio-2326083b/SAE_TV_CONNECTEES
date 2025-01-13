<?php
/**
 * Fichier Entity.php
 *
 * Ce fichier contient l'interface `Entity`, qui établit le lien entre
 * les tables de la base de données et le code PHP. Cette interface définit
 * les méthodes nécessaires pour que les entités (modèles) interagissent avec
 * la base de données, en permettant l'insertion, la mise à jour, la suppression
 * et la récupération des données.
 *
 * PHP version 7.4 or later
 *
 * @category Interface
 * @package  Models
 * @author   John Doe <johndoe@example.com>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @version  GIT: abcd1234abcd5678efgh9012ijkl3456mnop6789
 * @link     https://www.example.com/docs/Entity
 * Documentation de l'interface
 * @since    2025-01-07
 */
namespace models;

/**
 * Interface Entity
 *
 * Lien entre les tables de la base de données et le code PHP. Cette interface
 * définit les méthodes nécessaires pour interagir avec la base de données et
 * gérer les entités de manière uniforme à travers les différentes classes.
 *
 * @category Interface
 * @package  Models
 * @author   John Doe <johndoe@example.com>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @version  Release: 2.0.0
 * @link     https://www.example.com/docs/Entity Documentation de l'interface
 * @since    2025-01-07
 */
interface Entity
{

    /**
     * Create an entity
     *
     * @return int  id of the new entity
     */
    public function insert();

    /**
     * Update an entity
     *
     * @return mixed
     */
    public function update();

    /**
     * Delete an entity
     *
     * @return mixed
     */
    public function delete();

    /**
     * Get an entity linked to the given ID.
     *
     * Cette méthode récupère une entité à partir de son identifiant unique.
     *
     * @param int $id L'identifiant unique de l'entité à récupérer.
     *
     * @return mixed L'entité associée à l'identifiant fourni, ou une valeur
     *               indiquant qu'aucune entité n'a été trouvée.
     *
     * @version 1.0.0
     * @date    2025-01-13
     */
    public function get($id);

    /**
     * Get all entity
     *
     * @return mixed
     */
    public function getList();


    /**
     * Build an entity from the provided data.
     *
     * Cette méthode crée une entité à
     * partir des données fournies. Les données doivent
     * être sous forme de tableau associatif,
     * et cette fonction va configurer l'entité
     * en fonction des valeurs fournies.
     *
     * @param array $data Les données nécessaires à la création de l'entité.
     *                    Cela inclut généralement des
     *                    informations comme des identifiants,
     *                    des noms et d'autres attributs.
     *
     * @return mixed L'entité créée et configurée,
     * généralement un objet d'une classe spécifique.
     *
     * @version 1.0.0
     * @date    2025-01-13
     */
    public function setEntity($data);

    /**
     * Build a list of entities from the provided data list.
     *
     * Cette méthode crée une liste d'entités à partir des données
     * fournies. Elle itère
     * sur un tableau de données et utilise la méthode `setEntity`
     * pour créer une entité
     * pour chaque élément du tableau. Elle retourne un tableau
     * contenant toutes les entités créées.
     *
     * @param array $dataList Un tableau contenant des données
     *                        nécessaires pour créer plusieurs entités.
     *                        Chaque élément doit être
     *                        structuré de manière à correspondre à la
     *                        création d'une entité individuelle.
     *
     * @return mixed Un tableau d'entités créées, généralement un tableau d'objets.
     *
     * @version 1.0.0
     * @date    2025-01-13
     */
    public function setEntityList($dataList);
}
