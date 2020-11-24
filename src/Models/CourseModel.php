<?php

namespace Portal\Models;

use PDO;
use Portal\Entities\CourseEntity;

class CourseModel
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Get all courses from the database
     *
     * @return array An array of Courses
     */
    public function getAllCourses(): array
    {
        $sql = 'SELECT * FROM `courses`;';
        $query = $this->db->prepare($sql);
        $query->setFetchMode(\PDO::FETCH_CLASS, CourseEntity::class);
        $query->execute();
        return $query->fetchAll();
    }
}
