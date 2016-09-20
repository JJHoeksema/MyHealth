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
     * Class Event
     * @author Marwijnn de Kuijper <ikbenmarwijnn@gmail.com>
     * @package DMF\Events
     */
    abstract class Event {
        /**
         * @var array
         */
        protected $params;
        /**
         * @var array
         */
        protected $trace;

        public function __construct($params = []){
            $this->params = $params;
            $this->trace = debug_backtrace();
        }

        /**
         * Method to get the name of Event
         * @return string name of Event
         */
        public abstract function getName();

        /**
         * Method to get the trace of event call
         * @return array
         */
        public function getTrace(){
            return $this->trace;
        }

    }