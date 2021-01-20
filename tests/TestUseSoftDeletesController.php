<?php

namespace RafflesArgentina\RestfulController;

class TestUseSoftDeletesController extends RestfulController
{
    protected $repository = Repositories\TestRepository::class;

    protected $resourceName = 'test';

    protected $useSoftDeletes = true;
}
