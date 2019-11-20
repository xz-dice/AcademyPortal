<?php

namespace Portal\Models;

use Portal\Entities\EventEntity;

class EventModel
{
    private $db;

    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Get all events from the database
     *
     * @return array An array of Events
     */
    public function getEvents(): array
    {
        $sql = 'SELECT `events`.`id`, `events`.`name`, `category`, 
        `event_categories`.`name` AS `category_name`, `location`, `date`, `start_time`, 
        `end_time`, `notes` 
        FROM `events`
        LEFT JOIN `event_categories` ON `events`.`category` = `event_categories`.`id`
        ORDER BY `date` DESC;';
        $query = $this->db->prepare($sql);
        $query->execute();
        return $query->fetchAll();
    }

    /**
     * Get all event categories from the database
     *
     * @return array An array of event categories
     */
    public function getEventCategories(): array
    {
        $sql = 'SELECT `id`, `name` FROM `event_categories`';
        $query = $this->db->prepare($sql);
        $query->execute();
        return $query->fetchAll();
    }

    /**
     * Add a new event to the database
     *
     * @param [type] $newEvent
     * @return boolean True if operation succeeded
     */
    public function addEvent(EventEntity $newEvent): bool
    {
        $query = $this->db->prepare("INSERT INTO `events` (
            `id`,
            `name`,
            `category`,
            `location`,
            `date`,
            `start_time`,
            `end_time`,
            `notes`
            ) 
            VALUES (
            :eventId, 
            :name, 
            :category, 
            :location,
            :date, 
            :startTime, 
            :endTime, 
            :notes);");
        $query->bindParam(':eventId', $newEvent->getEventId());
        $query->bindParam(':name', $newEvent->getName());
        $query->bindParam(':category', $newEvent->getCategory());
        $query->bindParam(':location', $newEvent->getLocation());
        $query->bindParam(':date', $newEvent->getDate());
        $query->bindParam(':startTime', $newEvent->getStartTime());
        $query->bindParam(':endTime', $newEvent->getEndTime());
        $query->bindParam(':notes', $newEvent->getNotes());
        return $query->execute();
    }


    /**
     *Adds event id, hiring partner id and people attending to database
     *
     * @param int $hiringPartner id of the hiring partner selected
     *
     * @param int $event the id of the event selected
     *
     * @param int $attendees number of people attending from that hiring partner
     *
     * @return bool True if operation succeeds
     */
    public function addHPToEvent(int $hiringPartner, int $event, $attendees = null): bool
    {
        $query = $this->db->prepare('INSERT INTO `events_hiring_partner_link_table` (
            `hiring_partner_id`, 
            `event_id`, 
            `people_attending`
            ) 
            VALUES (
            :hiringPartner, 
            :event, 
            :attendees);');
        $query->bindParam(':hiringPartner', $hiringPartner);
        $query->bindParam(':event', $event);
        $query->bindParam(':attendees', $attendees);
        return $query->execute();
    }

    /**
     * checks that the hiring partner has successfully been linked to the event in the database
     *
     * @param int $hiringPartner hiring partner id
     *
     * @param int $event event id
     *
     * @return bool
     */
    public function checkLinkHP(int $hiringPartner, int $event): bool
    {
        $query = $this->db->prepare('SELECT `id` FROM `events_hiring_partner_link_table`
        WHERE  `event_id` = :event AND
        `hiring_partner_id` = :hiringPartner;');
        $query->bindParam(':hiringPartner', $hiringPartner);
        $query->bindParam(':event', $event);
        $query->execute();
        $linkToHp = $query->fetchAll();
        if (count($linkToHp) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Pulls hiring partner ids from database where they link to a specific event id
     *
     * @param int $eventId the id of the event
     *
     * @return array the array of hiring partner ids
     */
    public function hpIdsByEventId(int $eventId): array
    {
        $query = $this->db->prepare('SELECT `hiring_partner_id` FROM `events_hiring_partner_link_table`
        WHERE  `event_id` = :eventId;');
        $query->bindParam(':eventId', $eventId);
        $success = $query->execute();
        $hpIds = $query->fetchAll();
        $queryTwo = $this->db->prepare('SELECT `people_attending` FROM `events_hiring_partner_link_table`
        WHERE  `event_id` = :eventId;');
        $queryTwo->bindParam(':eventId', $eventId);
        $successTwo = $queryTwo->execute();
        $attendees = $queryTwo->fetchAll();
        $returnData = ['hpIds' => $hpIds, 'attendees' => $attendees, 'success' => $success];
        return $returnData;
    }
}
