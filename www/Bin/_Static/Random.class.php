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
    namespace DMF\_Static;

    /**
     * Class Random
     * @author Marwijnn de Kuijper <ikbenmarwijnn@gmail.com>
     * @package DMF\_Static
     */
    class Random{

        /**
         * Method to generates a random string
         * @param int $length the length of the string,
         * @param bool $numbers include numbers?
         * @param bool $lowercase include lowercase?
         * @param bool $uppercase include uppercase?
         * @return null|string the random string
         */
        static function string($length, $numbers = true, $lowercase = true, $uppercase = true){
            $array = [
                'numbers' => ['1', '2', '3', '4', '5' ,'6','7', '8', '9','0'],
                'lower' => ['a','b','c','d','e','f','g','h','i',
                    'j','k','l','m','n','o','p','q','r',
                    's','t','u','v','w','x','y','z'],
                'upper' => ['A','B','C','D','E','F','G','H','I',
                    'J','K','L','M','N','O','P','Q','R',
                    'S','T','U','V','W','X','Y','Z']
            ];
            $chars = [];
            if($numbers)    $chars = array_merge($chars, $array['numbers']);
            if($lowercase)  $chars = array_merge($chars, $array['lower']);
            if($uppercase)  $chars = array_merge($chars, $array['upper']);
            if(count($chars) == 0) return null;
            $string = '';
			$maxValue = count($chars) - 1 ;
            for ($i = 0; $i < $length; $i++){
                $string .= $chars[rand(0, $maxValue)];
            }
            return $string;
        }

        /**
         * Method to generate a random number
         * @param float $min minimum value
         * @param float $max maximum value
         * @param int $e amount of decimals
         * @return float an random number
         */
        static function number($min, $max, $e = 0){
            return rand($min * ($e + 1),$max * ($e + 1)) / ($e + 1);
        }
    
    }