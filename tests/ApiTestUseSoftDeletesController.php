<?php

namespace RafflesArgentina\RestfulController;

class ApiTestUseSoftDeletesController extends RestfulController
{
    protected $repository = Repositories\TestRepository::class;

    protected $resourceName = 'test4';

    protected $useSoftDeletes = true;
}
