<?php

abstract class TestCase extends Illuminate\Foundation\Testing\TestCase
{
    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://localhost';

    /**
     * @var object Инстанс тестируемого класса для вызова private метода
     */
    protected $object = null;

    /**
     * @var stgring Название тестируемого класса для вызова private метода
     */
    protected $className = '';

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }

    /**
     * Получение методов private и protected
     *
     * @param   string $className
     * @param   string $methodName
     * @return  ReflectionMethod
     */
    protected function getPrivateMethod($className, $methodName)
    {
        $reflector = new \ReflectionClass($className);
        $method    = $reflector->getMethod($methodName);
        $method->setAccessible(true);
 
        return $method;
    }

    /**
     * Вызов private метода
     * 
     * @param string $className
     * @param string $method
     * @param type|array $args
     * @return mixed
     */
    protected function callPrivateMethod($method, $args=array())
    {
        if ($this->object && $this->className) {
            $method = $this->getPrivateMethod($this->className, $method);
            $result = $method->invokeArgs($this->object, $args);
        } else {
            $this->markTestIncomplete('Не задан объект');
        }

        return $result;
    }
}
