<?php
Namespace Adianti\Database;

use Adianti\Database\TExpression;
//use Adianti\Database\TSqlStatement;

/**
 * Provides an interface to define filters to be used inside a criteria
 *
 * @version    1.0
 * @package    database
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TFilter1 extends TExpression {

    private $variable;

    //  private $operator;
//    private $value;
    //private $value2;

    /**
     * Class Constructor
     * 
     * @param  $value = variable
     * @param  $operator = operator (>, <, =, BETWEEN)
     * @param  $value    = value to be compared
     * @param  $value2   = second value to be compared (between)
     */
    public function __construct($value) {
        // store the properties
        $this->variable = $value;
    }

    /**
     * Return the filter as a string expression
     * @return  A string containing the filter
     */
    public function dump() {
//        if ($this->value2)
//        {
//            // concatenated the expression
//            return "{$this->variable} {$this->operator} {$this->value} AND {$this->value2}";
//        }
//        else
//        {
//            // concatenated the expression
        return "{$this->variable}";
//        }
    }

}
