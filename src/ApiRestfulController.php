<?php

namespace RafflesArgentina\RestfulController;

use DB;

use Illuminate\Http\Request;

use RafflesArgentina\RestfulController\Traits\WorksWithRelations;
use RafflesArgentina\RestfulController\Traits\WorksWithFileUploads;
use RafflesArgentina\RestfulController\Traits\FormatsResponseMessages;
use RafflesArgentina\RestfulController\Exceptions\RestfulControllerException;

class ApiRestfulController extends AbstractRestfulController
{
    use WorksWithRelations, WorksWithFileUploads, FormatsResponseMessages;

    /**
     * Display a listing of the resource.
     *
     * @param Request $request The request object.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $this->getFormRequestInstance();

        $items = $this->getItemsCollection();

        return $this->validSuccessJsonResponse('Success', $items);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request The request object.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        return $this->validNotFoundJsonResponse();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request The request object.
     *
     * @throws RestfulControllerException
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $this->getFormRequestInstance();

        DB::beginTransaction();
        try {
            $instance = $this->repository->create($request->all());
            $model = $instance[1];
            $number = $model->{$model->getRouteKeyName()};
            $mergedRequest = $this->uploadFiles($request, $model);
            $this->updateOrCreateRelations($mergedRequest, $model);
        } catch (\Exception $e) {
            DB::rollback();

            $message = $this->storeFailedMessage($e->getMessage());
            throw new RestfulControllerException($message);
        }

        DB::commit();

        $message = $this->storeSuccessfulMessage($number);
        $data = [$model];

        return $this->validSuccessJsonResponse($message, $data);
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request The request object.
     * @param string  $key     The model key.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $key)
    {
        $model = $this->findFirstByKey($key);

        if (!$model) {
            return $this->validNotFoundJsonResponse();
        }

        return response()->json($model, 200, [], JSON_PRETTY_PRINT);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Request $request The request object.
     * @param string  $key     The model key.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(Request $request, $key)
    {
        return $this->validNotFoundJsonResponse();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request The request object.
     * @param string  $key     The model key.
     *
     * @throws RestfulControllerException
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $key)
    {
        $this->getFormRequestInstance();

        $model = $this->findFirstByKey($key);

        if (!$model) {
            return $this->validNotFoundJsonResponse();
        }

        DB::beginTransaction();
       
        try { 
            $instance = $this->repository->update($model, $request->all());
            $model = $instance[1];
            $mergedRequest = $this->uploadFiles($request, $model);
            $this->updateOrCreateRelations($mergedRequest, $model);
        } catch (\Exception $e) {
            DB::rollback();

            $message = $this->updateFailedMessage($key, $e->getMessage());
            throw new RestfulControllerException($message);
        }

        DB::commit();

        $message = $this->updateSuccessfulMessage($key);
        $data = [$model];

        return $this->validSuccessJsonResponse($message, $data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request The request object.
     * @param string  $key     The model key.
     *
     * @throws RestfulControllerException
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $key)
    {
        $this->getFormRequestInstance();

        $model = $this->findFirstByKey($key);

        if (!$model) {
            return $this->validNotFoundJsonResponse();
        }

        DB::beginTransaction();

        try {
            $this->repository->delete($model);
        } catch (\Exception $e) {
            DB::rollback();

            $message = $this->destroyFailedMessage($key, $e->getMessage());
            throw new RestfulControllerException($message);
        }

        DB::commit();

        $message = $this->destroySuccessfulMessage($key);
        $data = [$model];

        return $this->validSuccessJsonResponse($message, $data);
    }
}
