<?php

namespace DoctrineExtensions\DQLFunctions;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

/**
 * Ignora acentos nas consultas
 *
 * @author Henry
 */
class Unaccent extends FunctionNode
{
    public $string = null;

    public function getSql(SqlWalker $sqlWalker)
    {
        return "unaccent({$this->string->dispatch($sqlWalker)})";
    }

    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        
        $this->string = $parser->StringExpression();
        
        $parser->match(Lexer::T_CLOSE_PARENTHESIS); 
    }

}
