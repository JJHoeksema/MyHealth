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
namespace DMF\Data;
/**
 * Class FileModel
 * @author Marwijnn de Kuijper <ikbenmarwijnn@gmail.com>
 * @package DMF\Data
 */
class FileModel extends DataModel{
    /**
     * @var array
     */
    private $dataReferences;
    /**
     * @var array
     */
    private $columns;
    /**
     * @var string|null
     */
    private $database;
    /**
     * @var string
     */
    private $table;

    /**
     * Constructor file Model
     * @param string $name model name
     * @param bool $public if not public, looks in module where it is called from
     * @throws \Exception
     */
    public function __construct($name, $public = true){
        $DIR = dirname(dirname(dirname(__FILE__)));
        if(!$public){
            $DIR .= DIRECTORY_SEPARATOR . "Modules" . DIRECTORY_SEPARATOR
                .  explode('\\', debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]["class"])[0];
        }
        $DIR .= DIRECTORY_SEPARATOR . "Models";

        $path = $DIR . DIRECTORY_SEPARATOR . $name . '.model';
        if (!file_exists($path)) throw new \Exception("Model '$name' could not be located");
        $file = json_decode(file_get_contents($path), true);
        if (!isset($file["Table"])) throw new \Exception("Invalid Model '$name' missing 'Table'");
        if (!isset($file["Columns"])) throw new \Exception("Invalid Model '$name' missing 'Columns'");

        $this->database         = (isset($file["Database"]))    ? $file["Database"]     : null;
        $this->dataReferences   = (isset($file["References"]))  ? $file["References"]   : [];
        $this->table            = $file["Table"];
        $this->columns          = $file["Columns"];

    }

    /**
     * Method to export an array to a Model
     * @param array $array
     * @param string $name name of Model
     * @param null|string $module name of the module the model belongs to
     */
    public static function Export($array, $name, $module = null){
        $DIR = dirname(dirname(dirname(__FILE__)));
        if($module != null){
            $DIR .= DIRECTORY_SEPARATOR . "Modules";
            if(!is_dir($DIR)){
                // module not found
                return;
            }
            $DIR .= DIRECTORY_SEPARATOR . $module;
            if(!is_dir($DIR)) mkdir($DIR);
        }
        $DIR .= DIRECTORY_SEPARATOR . "Models";
        if(!is_dir($DIR)) mkdir($DIR);

        $path = $DIR . DIRECTORY_SEPARATOR . $name . '.model';
        $file = fopen($path, "w");
        fwrite($file, json_encode($array));
        fclose($file);
    }

    /**
     * Method to get the references
     * @return array
     */
    public function getDataReferences(){
        return $this->dataReferences;
    }

    /**
     * Method to get the columns
     * @return array
     */
    public function getColumns(){
        return $this->columns;
    }

    /**
     * Method to get the name of the database
     * @return null|string
     */
    public function getDatabase(){
        return $this->database;
    }

    /**
     * Method to get the name of the Table
     * @return string
     */
    public function getTable(){
        return $this->table;
    }
}