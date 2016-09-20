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
    namespace DMF\Data\Specifier;
    
    use DMF\Data\DataModel;

    /**
     * Class Where
     * @author Marwijnn de Kuijper <ikbenmarwijnn@gmail.com>
     * @package DMF\Data\Specifier
     */
    class Where extends SpecifierObject{
        private $whereBlocks;

        /**
         * Constructor Where Specifier
         * @param DataModel $model Model to apply where checks on
         * @param null|array[]|WhereCheck $whereBlock
         * @throws \Exception
         */
        public function __construct(DataModel $model, $whereBlock = null){
                parent::__construct($model);

                $this->whereBlocks = [];
            if ($whereBlock != null){
                    $this->orWhere($whereBlock);
                return;
            }
            throw new \Exception("Invalid WhereChecks");
        }

        /**
         * method to add a new whereBlock
         * @param $whereBlock
         * @return $this
         * @throws \Exception if invalid where check or column doesn't exist
         */
        public function orWhere($whereBlock){
                if ($whereBlock instanceof WhereCheck){
                    $whereBlock = [$whereBlock];  
                }
            foreach($whereBlock as $whereCheck){
                /** @var WhereCheck $whereCheck */
                if($whereCheck instanceof WhereCheck == false){
                    throw new \Exception("Invalid Wherecheck");
                }

                if (!array_key_exists($whereCheck->getColumn(), $this->getModel()->getColumns())) {
                    throw new \Exception("Column '{$whereCheck->getColumn()}' doesn't exist");
                }
            }
            $this->whereBlocks[] = $whereBlock;
            return $this;
        }

        /**
         * method to return all whereBlocks
         * @return array
         */
        public function getWhereBlocks(){
            return $this->whereBlocks;
        }
    }