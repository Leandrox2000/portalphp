<?php
namespace Entity;

/**
 * Description of BaseTest
 *
 * @author Sammy Guergachi <sguergachi at gmail.com>
 */
abstract class BaseTest extends \PHPUnit_Framework_TestCase  
{ 
    
    public function getEmMock() 
    {
        $emMock = $this->getMock("Doctrine\\ORM\\EntityManager", 
                        array(
                                "persist", 
                                "flush",
                                "find",
                                "getReference", 
                                "remove", 
                                "getRepository",
                                "createQueryBuilder",
                            ), 
                        array(), 
                        '', 
                        FALSE);

        $emMock->expects($this->any())
                ->method("persist")
                ->will($this->returnValue(NULL));

        $emMock->expects($this->any())
                ->method("flush")
                ->will($this->returnValue(NULL));
        
        return $emMock;
    }
    
    
    public function getQueryBuilderMock() 
    {
        $emQueryBuilder = $this->getMock("Doctrine\\ORM\\QueryBuilder", 
                array(
                        "update", 
                        "set", 
                        "andWhere", 
                        "expr", 
                        "in",
                        "getQuery",
                        "execute",
                        "delete",
                    ), 
                array(), 
                '', 
                FALSE);
        
        
        $emQueryBuilder->expects($this->any())
                ->method("select")
                ->will($this->returnSelf());
        $emQueryBuilder->expects($this->any())
                ->method("from")
                ->will($this->returnSelf());
        $emQueryBuilder->expects($this->any())
                ->method("insert")
                ->will($this->returnSelf());
        $emQueryBuilder->expects($this->any())
                ->method("update")
                ->will($this->returnSelf());
        $emQueryBuilder->expects($this->any())
                ->method("set")
                ->will($this->returnSelf());
        $emQueryBuilder->expects($this->any())
                ->method("andWhere")
                ->will($this->returnSelf());
        $emQueryBuilder->expects($this->any())
                ->method("expr")
                ->will($this->returnSelf());
        $emQueryBuilder->expects($this->any())
                ->method("in")
                ->will($this->returnSelf());
        $emQueryBuilder->expects($this->any())
                ->method("delete")
                ->will($this->returnSelf());
        $emQueryBuilder->expects($this->any())
                ->method("getQuery")
                ->will($this->returnSelf());
        $emQueryBuilder->expects($this->any())
                ->method("execute")
                ->will($this->returnSelf());

        return $emQueryBuilder;
        
    }
    public function getQueryBuilderWithExceptionMock() 
    {
        $emQueryBuilder = $this->getQueryBuilderMock();
        
        $emQueryBuilder->expects($this->any())
                ->method("execute")
                ->will($this->throwException(new \Exception("Teste")));
        $emQueryBuilder->expects($this->any())
                ->method("remove")
                ->will($this->throwException(new \Exception("Teste")));

        return $emQueryBuilder;
    }
    
    
    
    public function getLoggerMock()
    {
        $loggerMock = $this->getMock("\\Logger", array("info", "error"), array(), '', FALSE);

        $loggerMock->expects($this->any())
                ->method("info")
                ->will($this->returnValue(NULL));

        $loggerMock->expects($this->any())
                ->method("error")
                ->will($this->returnValue(NULL));

        return $loggerMock;
    }

}
