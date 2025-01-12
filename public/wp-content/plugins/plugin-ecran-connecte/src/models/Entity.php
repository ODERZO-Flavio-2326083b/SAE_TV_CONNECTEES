<?php
// TODO : Ajouter la doc du fichier

namespace models;

/**
 * TODO : Ajouter les tags @author, @category, @license et @link
 * Interface Entity
 *
 * Link the database tables to the PHP code
 *
 * @package models
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

    // TODO : Commenter le paramètre
    /**
     * Get an entity link to the id
     *
     * @param $id
     *
     * @return mixed
     */
    public function get($id);

    /**
     * Get all entity
     *
     * @return mixed
     */
    public function getList();

    // TODO : Commenter le paramètre
    /**
     * Build an entity
     *
     * @param $data
     *
     * @return mixed
     */
    public function setEntity($data);

    // TODO : Commenter le paramètre
    /**
     * Build a list of entity
     *
     * @param $dataList
     *
     * @return mixed
     */
    public function setEntityList($dataList);
}
