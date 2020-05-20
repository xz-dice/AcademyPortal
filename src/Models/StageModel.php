<?php

namespace Portal\Models;

use phpDocumentor\Reflection\Types\Boolean;
use \Portal\Entities\StageEntity;

class StageModel
{
    private $db;

    /** Constructor assigns db PDO to this object
     *
     * @param \PDO $db
     */
    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    /** Queries the database and returns the highest current stage number as an integer
     *
     * @return int
     */
    public function getHighestOrderNo() : int
    {
        $query = $this->db->prepare("SELECT MAX(`order`) FROM `stages` WHERE `deleted` = 0;");
        $query->execute();
        $result = $query->fetch();
        return ($result['MAX(`order`)'] ?? 0);
    }

    /** Queries the db and returns an array with one result stored under 'stagesCount'
     *
     * @return array
     */
    public function stagesCount() : array
    {
        $query = $this->db->prepare(
            "SELECT COUNT(`id`) AS 'stagesCount' FROM `stages` WHERE `deleted` = 0;"
        );
        $query->setFetchMode(\PDO::FETCH_ASSOC);
        $query->execute();
        return $query->fetch();
    }

    /** Adds new stage to database and returns a boolean based on success or failure
     *
     * @param StageEntity $stageEntity
     * @return bool
     */
    public function createStage(StageEntity $stageEntity) : bool
    {
        $query = $this->db->prepare("INSERT INTO `stages` (`title`, `order`) VALUES (:title, :order); ");
        $query->bindParam(':title', $stageEntity->getStageTitle());
        $query->bindParam(':order', $stageEntity->getStageOrder());
        return $query->execute();
    }

    /**
     *  Gets all the stages that are not deleted from stages table sorted by order
     *
     * @return array of stage entities
     */
    public function getAllStages()
    {
        $query = $this->db->prepare(
            'SELECT `id`, `title`, `order`, `deleted` FROM `stages` WHERE `deleted` = 0 ORDER BY `order`;'
        );
        $query->setFetchMode(\PDO::FETCH_CLASS, 'Portal\Entities\StageEntity');
        $query->execute();
        $stages = $query->fetchAll();
        
        $query = $this->db->prepare(
            'SELECT `id`, `option`, `stageId` FROM `options` WHERE `deleted` = 0;'
        );
        $query->setFetchMode(\PDO::FETCH_CLASS, 'Portal\Entities\OptionsEntity');
        $query->execute();
        $options = $query->fetchAll();

        foreach ($stages as $stage) {
            $stageOptions = [];
            foreach ($options as $option) {
                if ($stage->getStageId() == $option->getStageId()) {
                    $stageOptions[] = $option;
                }
            }
            $stage->setOptions($stageOptions);
        }
        return $stages;
    }

    /** Sets the 'deleted' flag to '1' and 'order' value to '0' for a record with a given id.
     *
     * @param integer $id
     *
     * @return boolean for success or failure of the query
     */
    public function deleteStage(int $id) : bool
    {
        $query = $this->db->prepare("UPDATE `stages` SET `deleted` = '1', `order` = '0' WHERE `id` = :id");
        $query->bindParam(':id', $id);
        return $query->execute();
    }

    /**
     * Retrieves a stage with the specified id
     *
     * @param integer $id
     * @return StageEntity object
     *
     */
    public function getStageById(int $id) : StageEntity
    {
        $query = $this->db->prepare('SELECT `id`, `title`, `order`, `deleted` FROM `stages` WHERE `id`=:id');

        $query->setFetchMode(\PDO::FETCH_CLASS, 'Portal\Entities\StageEntity');
        $query->bindParam(':id', $id);
        $query->execute();
        return $query->fetch();
    }

    /**
     * Updates the 'title' value of a record with a given id.
     * @param int $id
     * @param string $newTitle
     * @return bool
     */
    public function updateStage(int $id, string $title, int $order) : bool
    {
        $query = $this->db->prepare("UPDATE `stages` SET `title` = :title, `order` = :newOrder WHERE `id` = :id");
        $query->bindParam(':id', $id);
        $query->bindParam(':title', $title);
        $query->bindParam(':newOrder', $order);

        return $query->execute();
    }

    public function updateAllStages(array $stages) : bool
    {
        try {
            $this->db->beginTransaction();
            foreach ($stages as $stage) {
                $this->updateStage($stage['id'], $stage['title'], $stage['order']);
            }
            $this->db->commit();
            return true;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            throw new \Exception('Cannot update stages.');
        }
    }

    /**
     * Creates a new option in the options table.
     * @param string $option
     * @param int $stageId
     * @return bool
     */
    public function createOption(string $option, int $stageId) : bool
    {
        $query = $this->db->prepare("INSERT INTO `options` (`option`, `stageId`) VALUES (:optionText, :stageId);");
        $query->bindParam(':optionText', $option);
        $query->bindParam(':stageId', $stageId);
        return $query->execute();
    }

    /**
     * Updates the 'option' value of an entry in the options table with a given id.
     * @param string $option
     * @param int $optionId
     * @return bool
     */
    public function updateOption(string $option, int $optionId) : bool
    {
        $query = $this->db->prepare("UPDATE `options` SET `option` = :optionText WHERE `id` = :id");
        $query->bindParam(':id', $optionId);
        $query->bindParam(':optionText', $option);
        return $query->execute();
    }

    /**
     * Deletes (soft delete) the 'option' value of an entry in the options table with a given id.
     * @param int $optionId
     * @return bool
     */
    public function deleteOption(int $optionId) : bool
    {
        $query = $this->db->prepare("UPDATE `options` SET `deleted` = '1' WHERE `id` = :optionId");
        $query->bindParam(':optionId', $optionId);
        return $query->execute();
    }

    public function getStageIdByOptionId(int $optionId) : int
    {
        $query = $this->db->prepare("SELECT `stageId` FROM `options` WHERE `id`=:optionId;");
        $query->bindParam(':optionId', $optionId);
        $query->execute();

        return $query->fetch();
    }

    public function getOptionsByStageId(int $stageId) : array
    {
        $query = $this->db->prepare("SELECT `id`, `option`, `stageId` FROM `options` 
                                        WHERE `stageId` = :stageId, `deleted` = '0';");
        $query->bindParam(':stageId', $stageId);
        $query->execute;

        return $query->fetchAll();
    }
    
    /**
     * Deletes (soft delete) all the 'options' of a stage with a given id.
     * @param int $stageId
     * @return bool
     */
    public function deleteAllOptions(int $stageId) : bool
    {
        $query = $this->db->prepare("UPDATE `options` SET `deleted` = '1' WHERE `stageId` = :stageId");
        $query->bindParam(':stageId', $stageId);
        return $query->execute();
    }


    public function getDB() : \PDO
    {
        return $this->db;
    }
}
