<?php

namespace RafflesArgentina\RestfulController;

class TestController extends RestfulController
{
    protected $repository = Repositories\TestRepository::class;

    protected $resourceName = 'test';
}
