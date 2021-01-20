<?php

namespace RafflesArgentina\RestfulController;

use Orchestra\Testbench\TestCase;

class ApiRestfulControllerTest extends TestCase
{
    use BaseTest;

    /**
     * @coversNothing
     */
    function testIndexRoute()
    {
        factory(\RafflesArgentina\RestfulController\Models\User::class, 3)->create();

        $this->json('GET', '/test3')
            ->assertStatus(200);
             //->assertJsonCount(3, 'data');
    }

    /**
     * @coversNothing
     */
    function testIndexRouteWithUseSoftDeletes()
    {
        $users = factory(\RafflesArgentina\RestfulController\Models\User::class, 3)->create();
        foreach ($users as $user) {
            $user->delete();
        }

        $this->json('GET', '/test4')
            ->assertStatus(200);
             //->assertJsonCount(3, 'data');
    }

    /**
     * @coversNothing
     */
    function testCreateRoute()
    {
        $this->json('GET', '/test3/create')
            ->assertStatus(404);
    }

    /**
     * @coversNothing
     */
    function testStoreRoute()
    {
        $this->json('POST', '/test3', ['name' => 'Paula', 'email' => 'paula@raffles.com.ar', 'password' => bcrypt(str_random())])
            ->assertStatus(200);
    }

    /**
     * @coversNothing
     */
    function testShowRoute()
    {
        $user = factory(\RafflesArgentina\RestfulController\Models\User::class)->create();

        $this->json('GET', '/test3/'.$user->id)
            ->assertStatus(200);
    }

    /**
     * @coversNothing
     */
    function testShowRouteWithUseSoftDeletes()
    {
        $user = factory(\RafflesArgentina\RestfulController\Models\User::class)->create();
        $user->delete();

        $this->json('GET', '/test4/'.$user->id)
            ->assertStatus(200);
    }

    /**
     * @coversNothing
     */
    function testShowRouteWithInexistentModel()
    {
        $this->json('GET', '/test3/7')->assertStatus(404);
    }

    /**
     * @coversNothing
     */
    function testEditRoute()
    {
        $user = factory(\RafflesArgentina\RestfulController\Models\User::class)->create();

        $this->json('GET', '/test3/'.$user->id.'/edit')
            ->assertStatus(404);
    }

    /**
     * @coversNothing
     */
    function testEditRouteWithUseSoftDeletes()
    {
        $user = factory(\RafflesArgentina\RestfulController\Models\User::class)->create();
        $user->delete();

        $this->json('GET', '/test4/'.$user->id.'/edit')
            ->assertStatus(404);
    }

    /**
     * @coversNothing
     */
    function testEditRouteWithInexistentModel()
    {
        $this->json('GET', '/test3/7/edit')->assertStatus(404);
    }

    /**
     * @coversNothing
     */
    function testUpdateRoute()
    {
        $user = factory(\RafflesArgentina\RestfulController\Models\User::class)->create();

        $this->json('PUT', '/test3/'.$user->id, ['name' => 'Mario', 'email' => 'mario@raffles.com.ar', 'password' => bcrypt(str_random())])
            ->assertStatus(200);
    }

    /**
     * @coversNothing
     */
    function testUpdateRouteWithUseSoftDeletes()
    {
        $user = factory(\RafflesArgentina\RestfulController\Models\User::class)->create();
        $user->delete();

        $this->json('PUT', '/test4/'.$user->id, ['name' => 'Mario', 'email' => 'mario@raffles.com.ar', 'password' => bcrypt(str_random())])
            ->assertStatus(200);
    }

    /**
     * @coversNothing
     */
    function testUpdateRouteWithInexistentModel()
    {
        $this->json('PUT', '/test3/7')->assertStatus(404);
    }

    /**
     * @coversNothing
     */
    function testDestroyRoute()
    {
        $user = factory(\RafflesArgentina\RestfulController\Models\User::class)->create();

        $this->json('DELETE', '/test3/'.$user->id)
            ->assertStatus(200);
    }

    /**
     * @coversNothing
     */
    function testDestroyRouteWithUseSoftDeletes()
    {
        $user = factory(\RafflesArgentina\RestfulController\Models\User::class)->create();
        $user->delete();

        $this->json('DELETE', '/test4/'.$user->id)
            ->assertStatus(200);
    }

    /**
     * @coversNothing
     */
    function testDestroyRouteWithInexistentModel()
    {
        $this->json('DELETE', '/test3/7')->assertStatus(404);
    }
}
