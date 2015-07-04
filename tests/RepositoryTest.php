<?php

/**
 * @covers Mobileka\ScopeApplicator\Laravel\Repository
 */
class RepositoryTest extends TestCase
{
    /**
     * @test
     * @covers Mobileka\ScopeApplicator\Laravel\Repository::getInputManager
     */
    public function returns_input_manager_instance()
    {
        $repository = new Stubs\Repository;
        assertInstanceOf('Mobileka\ScopeApplicator\Contracts\InputManagerInterface', $repository->getInputManager());
    }

    /**
     * @test
     * @covers Mobileka\ScopeApplicator\Laravel\Repository::getLogger
     */
    public function returns_logger_instance()
    {
        $repository = new Stubs\Repository;
        assertInstanceOf('Mobileka\ScopeApplicator\Contracts\LoggerInterface', $repository->getLogger());
    }
}
