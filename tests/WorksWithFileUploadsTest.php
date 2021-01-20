<?php

namespace RafflesArgentina\RestfulController;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

use Orchestra\Testbench\TestCase;

class WorksWithFileUploadsTest extends TestCase
{
    use BaseTest;

    /**
     * @coversNothing
     */
    function testPostNonMultipleFileUpload()
    {
        Storage::fake('uploads');

        $this->post(
            '/test', [
                'location' => UploadedFile::fake()->image('test.jpeg'),
                'name' => 'Mario',
                'email' => 'mario@raffles.com.ar',
                'password' => str_random()
            ]
        )->assertRedirect('/test')
            ->assertSessionHas('rafflesargentina.status.success');

        $user = \RafflesArgentina\RestfulController\Models\User::first();
        $this->assertTrue(!is_null($user->location));
    }

    /**
     * @coversNothing
     */
    function testPostHasOneFileUpload()
    {
        Storage::fake('uploads');

        $this->post(
            '/test', [
                'name' => 'Mario',
                'email' => 'mario@raffles.com.ar',
                'password' => str_random(),
                'hasOneFileUpload' => [
                    UploadedFile::fake()->image('test.jpeg')
                ],
            ]
        )->assertRedirect('/test')
            ->assertSessionHas('rafflesargentina.status.success');

        $user = \RafflesArgentina\RestfulController\Models\User::first();
        $this->assertTrue(!is_null($user->hasOneFileUpload));
    }

    /**
     * @coversNothing
     */
    function testPostMorphOneFileUpload()
    {
        Storage::fake('uploads');

        $this->post(
            '/test', [
            'name' => 'Mario',
            'email' => 'mario@raffles.com.ar',
            'password' => str_random(),
                'morphOneFileUpload' => [
                    UploadedFile::fake()->image('test.jpeg')
                ],
            ]
        )->assertRedirect('/test')
            ->assertSessionHas('rafflesargentina.status.success');

        $user = \RafflesArgentina\RestfulController\Models\User::first();
        $this->assertTrue(!is_null($user->morphOneFileUpload));
    }

    /**
     * @coversNothing
     */
    function testPostBelongsToFileUpload()
    {
        $user = \RafflesArgentina\RestfulController\Models\User::first();

        Storage::fake('uploads');

        $this->post(
            '/test', [
            'name' => 'Mario',
            'email' => 'mario@raffles.com.ar',
            'password' => str_random(),
                'belongsToFileUpload' => [
                    UploadedFile::fake()->image('test.jpeg')
                ],
            ]
        )->assertRedirect('/test')
            ->assertSessionHas('rafflesargentina.status.success');

        $user = \RafflesArgentina\RestfulController\Models\User::first();
        $this->assertTrue(!is_null($user->belongsToFileUpload));
    }

    /**
     * @coversNothing
     */
    function testPostHasManyFileUploads()
    {
        Storage::fake('uploads');

        $this->post(
            '/test', [
            'name' => 'Mario',
            'email' => 'mario@raffles.com.ar',
            'password' => str_random(),
            'hasManyFileUploads' => [
                UploadedFile::fake()->image('test.jpeg'),
                UploadedFile::fake()->create('document.pdf')
            ]
            ]
        )->assertRedirect('/test')
            ->assertSessionHas('rafflesargentina.status.success');

        $user = \RafflesArgentina\RestfulController\Models\User::first();
        $this->assertTrue($user->hasManyFileUploads->count() === 2);
    }

    /**
     * @coversNothing
     */
    function testPostMorphManyFileUploads()
    {
        $user = \RafflesArgentina\RestfulController\Models\User::first();

        Storage::fake('uploads');

        $this->post(
            '/test', [
            'name' => 'Mario',
            'email' => 'mario@raffles.com.ar',
            'password' => str_random(),
            'morphManyFileUploads' => [
                UploadedFile::fake()->image('test.jpeg'),
                UploadedFile::fake()->create('document.pdf')
            ],
            ]
        )->assertRedirect('/test')
            ->assertSessionHas('rafflesargentina.status.success');

        $user = \RafflesArgentina\RestfulController\Models\User::first();
        $this->assertTrue($user->morphManyFileUploads->count() === 2);
    }

    /**
     * @coversNothing
     */
    function testPostBelongsToManyFileUploads()
    {
        $user = \RafflesArgentina\RestfulController\Models\User::first();

        Storage::fake('uploads');

        $this->post(
            '/test', [
            'name' => 'Mario',
            'email' => 'mario@raffles.com.ar',
            'password' => str_random(),
            'belongsToManyFileUploads' => [
                UploadedFile::fake()->image('test.jpeg'),
                UploadedFile::fake()->create('document.pdf')
            ],
            ]
        )->assertRedirect('/test')
            ->assertSessionHas('rafflesargentina.status.success');

        $user = \RafflesArgentina\RestfulController\Models\User::first();
        $this->assertTrue($user->belongsToManyFileUploads->count() === 2);
    }

    /**
     * @coversNothing
     */
    function testPostMorphToManyFileUploads()
    {
        $user = \RafflesArgentina\RestfulController\Models\User::first();

        Storage::fake('uploads');

        $this->post(
            '/test', [
            'name' => 'Mario',
            'email' => 'mario@raffles.com.ar',
            'password' => str_random(),
            'morphToManyFileUploads' => [
                UploadedFile::fake()->image('test.jpeg'),
                UploadedFile::fake()->create('document.pdf')
            ],
            ]
        )->assertRedirect('/test')
            ->assertSessionHas('rafflesargentina.status.success');

        $user = \RafflesArgentina\RestfulController\Models\User::first();
        $this->assertTrue($user->morphToManyFileUploads->count() === 2);
    }

    /**
     * @coversNothing
     */
    function testPutNonMultipleFileUpload()
    {
        $user = factory(\RafflesArgentina\RestfulController\Models\User::class)->create();

        Storage::fake('uploads');

        $this->put(
            '/test/1', [
                'location' => UploadedFile::fake()->image('test.jpeg'),
            ]
        )->assertRedirect('/test')
            ->assertSessionHas('rafflesargentina.status.success');

        $user->refresh();
        $this->assertTrue(!is_null($user->location));
    }

    /**
     * @coversNothing
     */
    function testPutHasOneFileUpload()
    {
        $user = factory(\RafflesArgentina\RestfulController\Models\User::class)->create();

        Storage::fake('uploads');

        $this->put(
            '/test/1', [
                'hasOneFileUpload' => [
                    UploadedFile::fake()->image('test.jpeg')
                ],
            ]
        )->assertRedirect('/test')
            ->assertSessionHas('rafflesargentina.status.success');

        $this->assertTrue(!is_null($user->hasOneFileUpload));
    }

    /**
     * @coversNothing
     */
    function testPutMorphOneFileUpload()
    {
        $user = factory(\RafflesArgentina\RestfulController\Models\User::class)->create();

        Storage::fake('uploads');

        $this->put(
            '/test/1', [
                'morphOneFileUpload' => [
                    UploadedFile::fake()->image('test.jpeg')
                ],
            ]
        )->assertRedirect('/test')
            ->assertSessionHas('rafflesargentina.status.success');

        $this->assertTrue(!is_null($user->morphOneFileUpload));
    }

    /**
     * @coversNothing
     */
    function testPutBelongsToFileUpload()
    {
        $user = factory(\RafflesArgentina\RestfulController\Models\User::class)->create();

        Storage::fake('uploads');

        $this->put(
            '/test/1', [
                'belongsToFileUpload' => [
                    UploadedFile::fake()->image('test.jpeg')
                ],
            ]
        )->assertRedirect('/test')
            ->assertSessionHas('rafflesargentina.status.success');

        $user->refresh();
        $this->assertTrue(!is_null($user->belongsToFileUpload));
    }

    /**
     * @coversNothing
     */
    function testPutHasManyFileUploads()
    {
        $user = factory(\RafflesArgentina\RestfulController\Models\User::class)->create();

        Storage::fake('uploads');

        $this->put(
            '/test/1', [
            'hasManyFileUploads' => [
                UploadedFile::fake()->image('test.jpeg'),
                UploadedFile::fake()->create('document.pdf')
            ]
            ]
        )->assertRedirect('/test')
            ->assertSessionHas('rafflesargentina.status.success');

        $this->assertTrue($user->hasManyFileUploads->count() === 2);
    }

    /**
     * @coversNothing
     */
    function testPutMorphManyFileUploads()
    {
        $user = factory(\RafflesArgentina\RestfulController\Models\User::class)->create();

        Storage::fake('uploads');

        $this->put(
            '/test/1', [
            'morphManyFileUploads' => [
                UploadedFile::fake()->image('test.jpeg'),
                UploadedFile::fake()->create('document.pdf')
            ],
            ]
        )->assertRedirect('/test')
            ->assertSessionHas('rafflesargentina.status.success');

        $this->assertTrue($user->morphManyFileUploads->count() === 2);
    }

    /**
     * @coversNothing
     */
    function testPutBelongsToManyFileUploads()
    {
        $user = factory(\RafflesArgentina\RestfulController\Models\User::class)->create();

        Storage::fake('uploads');

        $this->put(
            '/test/1', [
            'belongsToManyFileUploads' => [
                UploadedFile::fake()->image('test.jpeg'),
                UploadedFile::fake()->create('document.pdf')
            ],
            ]
        )->assertRedirect('/test')
            ->assertSessionHas('rafflesargentina.status.success');

        $this->assertTrue($user->belongsToManyFileUploads->count() === 2);
    }

    /**
     * @coversNothing
     */
    function testPutMorphToManyFileUploads()
    {
        $user = factory(\RafflesArgentina\RestfulController\Models\User::class)->create();

        Storage::fake('uploads');

        $this->put(
            '/test/1', [
            'morphToManyFileUploads' => [
                UploadedFile::fake()->image('test.jpeg'),
                UploadedFile::fake()->create('document.pdf')
            ],
            ]
        )->assertRedirect('/test')
            ->assertSessionHas('rafflesargentina.status.success');

        $this->assertTrue($user->morphToManyFileUploads->count() === 2);
    }
}
