<?php

namespace RafflesArgentina\RestfulController;

class ApiTestController extends RestfulController
{
    protected $repository = Repositories\TestRepository::class;

    protected $resourceName = 'test3';
}
