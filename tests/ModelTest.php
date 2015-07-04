<?php

use Stubs\Model;

/**
 * @covers Mobileka\ScopeApplicator\Laravel\Model
 */
class ModelTest extends TestCase
{
    /**
     * @test
     * @covers Mobileka\ScopeApplicator\Laravel\Model::getInputManager
     */
    public function returns_input_manager_instance()
    {
        $model = new Model;
        assertInstanceOf('Mobileka\ScopeApplicator\Contracts\InputManagerInterface', $model->getInputManager());
    }

    /**
     * @test
     * @covers Mobileka\ScopeApplicator\Laravel\Repository::getLogger
     */
    public function returns_logger_instance()
    {
        $model = new Model;
        assertInstanceOf('Mobileka\ScopeApplicator\Contracts\LoggerInterface', $model->getLogger());
    }

    /**
     * @test
     * @covers Mobileka\ScopeApplicator\Laravel\Model::__callStatic
     */
    public function handles_scopes()
    {
        assertInstanceOf('Stubs\Model', Model::handleScopes(['scope']));
    }
}
