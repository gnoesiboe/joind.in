<?php

class EventModel extends ApiModel {
    public static function getDefaultFields() {
        $fields = array(
            'event_id' => 'ID',
            'name' => 'event_name',
            'start_date' => 'event_start',
            'end_date' => 'event_end',
            'description' => 'event_desc',
            'href' => 'event_href',
            'icon' => 'event_icon'
            );
        return $fields;
    }

    public static function getVerboseFields() {
        $fields = array(
            'event_id' => 'ID',
            'name' => 'event_name',
            'start_date' => 'event_start',
            'end_date' => 'event_end',
            'description' => 'event_desc',
            'href' => 'event_href',
            'icon' => 'event_icon',
            'latitude' => 'event_lat',
            'longitude' => 'event_long',
            'tz_continent' => 'event_tz_cont',
            'tz_place' => 'event_tz_place',
            'location' => 'event_loc',
            'cfp_start_date' => 'event_cfp_start',
            'cfp_end_date' => 'event_cfp_end'
            );
        return $fields;
    }

    public static function getEventById($db, $event_id, $verbose = false) {
        $sql = 'select * from events '
            . 'where active = 1 and '
            . '(pending = 0 or pending is NULL) and '
            . 'ID = :event_id';
        $stmt = $db->prepare($sql);
        $response = $stmt->execute(array(
            ':event_id' => $event_id
            ));
        if($response) {
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $retval = static::transformResults($results, $verbose);
            return $retval;
        }
        return false;

    }

    public static function getEventList($db, $resultsperpage, $page, $verbose = false) {
        $sql = 'select * from events '
            . 'where active = 1 and '
            . 'pending = "0" and '
            . 'private <> "y" '
            . 'order by event_start desc';
        $sql .= static::buildLimit($resultsperpage, $page);

        $stmt = $db->prepare($sql);
        $response = $stmt->execute();
        if($response) {
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $retval = static::transformResults($results, $verbose);
            return $retval;
        }
        return false;
    }

    public static function addHyperMedia($list, $host) {
        // loop again and add links specific to this item
        if(count($list)) {
            foreach($list as $key => $row) {
                $list[$key]['uri'] = 'http://' . $host . '/v2/events/' . $row['event_id'];
                $list[$key]['verbose_uri'] = 'http://' . $host . '/v2/events/' . $row['event_id'] . '?verbose=yes';
                $list[$key]['comments_link'] = 'http://' . $host . '/v2/events/' . $row['event_id'] . '/comments';
                $list[$key]['talks_link'] = 'http://' . $host . '/v2/events/' . $row['event_id'] . '/talks';
            }
        }
        return $list;
    }
}