<?php


namespace Models;

/**
 * Interface Entity
 *
 * Link the database tables to the PHP code
 *
 * @package Models
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

    /**
     * Build an entity
     *
     * @param $data
     *
     * @return mixed
     */
    public function setEntity($data);

    /**
     * Build a list of entity
     *
     * @param $dataList
     *
     * @return mixed
     */
    public function setEntityList($dataList);
}
