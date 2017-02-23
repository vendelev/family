<?php

// use Illuminate\Foundation\Testing\WithoutMiddleware;
// use Illuminate\Foundation\Testing\DatabaseMigrations;
// use Illuminate\Foundation\Testing\DatabaseTransactions;

use Family\Http\Controllers\FamilyTree;

class FamilyTreeTest extends TestCase
{

    protected function setUp()
    {
        $this->className= '\Family\Http\Controllers\FamilyTree';
        $this->object   = new $this->className();
    }
}
