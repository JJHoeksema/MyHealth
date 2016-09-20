<?php
/*
 *  DMF: Data Modeler Framework
 *  Copyright (C) 2015  Marwijnn de Kuijper
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
    namespace DMF\Events;

    /**
     * Class StatusChangeEvent
     * @author Marwijnn de Kuijper <ikbenmarwijnn@gmail.com>
     * @package DMF\Events
     */
    class StatusChangeEvent extends Event {

        /**
         * @param int $statusCode
         * @param null $msg optional message
         */
        public function __construct($statusCode, $msg = null){
            parent::__construct(["status" => $statusCode, "msg" => $msg]);
            http_response_code($statusCode);
        }

        /**
         * Method to get Name of the event
         * @return string name of Event
         */
        public function getName(){
            return "StatusChangeEvent";
        }

        /**
         * Method to get StatusCode of the Event
         * @return int status code
         */
        public function getStatusCode(){
            return $this->params["status"];
        }

        /**
         * Method to get the message of Event
         * @return string message
         */
        public function getMessage(){
            return $this->params["msg"];
        }

    }