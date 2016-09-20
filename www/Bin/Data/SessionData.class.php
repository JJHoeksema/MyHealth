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

    use DMF\Data\Specifier\SpecifierObject;
    use DMF\Data\Specifier\Where;
    use DMF\Data\Specifier\WhereCheck;
    /**
     * Class SessionData
     * @author Marwijnn de Kuijper <ikbenmarwijnn@gmail.com>
     * @package DMF\Data
     */
    class SessionData extends DataObject{

        /**
         * constructor SessionData
         */
        public function __construct(){
            if(!is_writable(session_save_path())){
                throw new \Exception("cannot write session data to '".session_save_path()."'");
            }
            if (!isset($_SESSION)) session_start();
        }

        /**
         * destructor SessionData
         */
        public function __destruct(){
            if(isset($_SESSION) && count($_SESSION) == 0) session_destroy();
            else session_write_close();
        }
        
        
        private function compare($compare, $check, $value){
            switch($check){
                case DSWC_NOTEQUAL:                 return ($compare != $value);
                case DSWC_EQUAL:                    return ($compare == $value);
                case DSWC_GREATER:                  return ($compare >  $value);
                case (DSWC_GREATER | DSWC_EQUAL):   return ($compare >= $value);
                case DSWC_SMALLER:                  return ($compare <  $value);
                case (DSWC_SMALLER | DSWC_EQUAL):   return ($compare <= $value);
                default:                            return false;
            }
        }

        private function isSelectedWhereSpecifier($row, Where $whereSpecifier){
            foreach($whereSpecifier->getWhereBlocks() as $whereBlock){
                
                $result = true;
                foreach($whereBlock as $whereCheck){
                    /** @var WhereCheck $whereCheck */
                    $column = $whereCheck->getcolumn();
                    if(!$this->compare($row[$column], $whereCheck->getCheck(), $whereCheck->getValue())){
                       $result = false;
                    } 
                }
                
                if ($result) return true;
            }
            return false;
        }

        /**
         * Method to select data from a Session
         * Only supports where specifier currently
         * @param DataModel $model
         * @param array $columns
         * @param SpecifierObject[]|SpecifierObject $specifierObjects
         * @return array
         */
        public function select(DataModel $model, $columns = null, $specifierObjects = null){
            if(!(is_array($specifierObjects) || $specifierObjects == null)) $specifierObjects = [$specifierObjects];
            
            $tbl = $model->getTable();
            $selected = [];
            if(!(isset($_SESSION[$tbl]) && is_array($_SESSION[$tbl])) ) return $selected;
            
            foreach($_SESSION[$tbl] as $row){
                if($specifierObjects == null){
                    $selected[] = $row;
                    continue;
                }
                foreach($specifierObjects as $specifierObject){
                    /** @var SpecifierObject $specifierObject */
                    if ($specifierObject->getModel() != $model) continue;
                    if (!($specifierObject instanceof Specifier\Where)) continue; //only handle wherespecifier
                    /** @var Where $specifierObject */
                    if($this->isSelectedWhereSpecifier($row, $specifierObject)) $selected[] = $row;
                }
            }
            
            if($columns == null) return $selected;
            $tmp = [];
            foreach($selected as $k => $row){
                $tmp[$k] = [];
                foreach($columns as $v){
                    if(!isset($row[$v])) $tmp[$k][$v] = null;
                    else $tmp[$k][$v] = $row[$v];
                }
            }
            return $tmp;
        }

        /**
         * Method to insert data into a Session
         * @param DataModel $model
         * @param array $data
         * @return bool
         * @throws \Exception
         */
        public function insert(DataModel $model, $data){
            $tbl = $model->getTable();
            foreach($data as $k => $v){
                if (!$model->hasColumn($k)) throw new \Exception("column '$k' does not exist");
            }
            if(!(isset($_SESSION[$tbl]) && is_array($_SESSION[$tbl])) ) $_SESSION[$tbl] = [];
            
            $tmp = [];
            foreach($model->getColumns() as $column => $type){
                if(!isset($data[$column])) $tmp[$column] = null;
                $tmp[$column] = $this->validateType($type, $data[$column]);
            }
            
            $_SESSION[$tbl][] = $tmp;
            end($_SESSION[$tbl]);
            
            return key($_SESSION[$tbl]);
        }

        /**
         * Method to update data of a Session
         * Only supports where specifier currently
         * @param DataModel $model
         * @param array $data
         * @param SpecifierObject[]|SpecifierObject $specifierObjects
         * @return bool
         * @throws \Exception
         */
        public function update(DataModel $model, $data, $specifierObjects){
            if(!is_array($specifierObjects)) $specifierObjects = [$specifierObjects];
            
            $tbl = $model->getTable();
            
            if(!(isset($_SESSION[$tbl]) && is_array($_SESSION[$tbl])) ) return false;
            
            foreach($_SESSION[$tbl] as $key => $row){
                foreach($specifierObjects as $specifierObject){
                    /** @var SpecifierObject $specifierObject */
                    if ($specifierObject->getModel() != $model) continue;
                    if (!($specifierObject instanceof Specifier\Where)) continue; //only handle wherespecifier
                    /** @var Where $specifierObject */
                    if($this->isSelectedWhereSpecifier($row, $specifierObject)){
                           foreach($data as $column => $value){
                                if (!$model->hasColumn($column)) throw new \Exception("colomn '$column' does not exist");
                                $_SESSION[$tbl][$key][$column] = $this->validateType($model->getColumns()[$column],$value);
                            }
                    }
                }
            }
            return true;
        }

        /**
         * Method to delete data out of a Session
         * @param DataModel $model
         * @param $specifierObjects
         * @return bool
         */
        public function delete(DataModel $model, $specifierObjects){
            if(!is_array($specifierObjects)) $specifierObjects = [$specifierObjects];
            $tbl = $model->getTable();
            
            if(!(isset($_SESSION[$tbl]) && is_array($_SESSION[$tbl])) ) return false;
            
            foreach($_SESSION[$tbl] as $key => $row){
                foreach($specifierObjects as $specifierObject){
                    /** @var SpecifierObject $specifierObject */
                    if ($specifierObject->getModel() != $model) continue;
                    if (!($specifierObject instanceof Specifier\Where)) continue; //only handle wherespecifier
                    /** @var Where $specifierObject */
                    if($this->isSelectedWhereSpecifier($row, $specifierObject)) unset($_SESSION[$tbl][$key]);
                }
            }
            if(count($_SESSION[$tbl]) == 0) unset($_SESSION[$tbl]);
            return true;
        }

        /**
         * @param DataModel $model
         */
        public function clear($model){
            $tbl = $model->getTable();
            if(isset($_SESSION[$tbl])) unset($_SESSION[$tbl]);
        }
        
        
    }