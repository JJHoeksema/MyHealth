<?php

namespace DMF\_Static;


class Time {

    static function getTimestamp($timeZone){
        $datetime = new \DateTime();
        $datetime->setTimezone($timeZone);
        return $datetime->format("Y-m-d H:i:s");
    }
}