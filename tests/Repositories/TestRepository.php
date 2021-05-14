<?php

namespace RafflesArgentina\RestfulController\Repositories;

use Caffeinated\Repository\Repositories\EloquentRepository;

use RafflesArgentina\RestfulController\Models\User;

class TestRepository extends EloquentRepository
{
    public $model = User::class;

    protected $tag = ['user'];
}
