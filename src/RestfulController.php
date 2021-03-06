<?php

namespace RafflesArgentina\RestfulController;

use DB;

use Illuminate\Http\Request;

use RafflesArgentina\RestfulController\Traits\WorksWithRelations;
use RafflesArgentina\RestfulController\Traits\WorksWithFileUploads;
use RafflesArgentina\RestfulController\Traits\FormatsResponseMessages;
use RafflesArgentina\RestfulController\Exceptions\RestfulControllerException;

class RestfulController extends AbstractRestfulController
{
    use WorksWithRelations, WorksWithFileUploads, FormatsResponseMessages;

    /**
     * Display a listing of the resource.
     *
     * @param Request $request The request object.
     *
     * @return mixed
     */
    public function index(Request $request)
    {
        $this->getFormRequestInstance();

        $items = $this->getItemsCollection();

        if ($request->wantsJson()) {
            return $this->validSuccessJsonResponse('Success', $items);
        }

        $view = $this->getViewLocation(__FUNCTION__);
        $this->checkViewExists($view);

        return response()->view($view, compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request The request object.
     *
     * @return mixed
     */
    public function create(Request $request)
    {
        if ($request->wantsJson()) {
            return $this->validNotFoundJsonResponse();
        }

        $model = new $this->repository->model;

        $view = $this->getViewLocation(__FUNCTION__);
        $this->checkViewExists($view);

        return response()->view($view, compact('model'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request The request object.
     *
     * @throws RestfulControllerException
     *
     * @return mixed
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

        if ($request->wantsJson()) {
            $data = [
                $model
            ];

            return $this->validSuccessJsonResponse($message, $data);
        }

        return redirect()->route($this->getRedirectionRoute())
            ->with($this->successFlashMessageKey, $message);
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request The request object.
     * @param string  $key     The model key.
     *
     * @return mixed
     */
    public function show(Request $request, $key)
    {
        $model = $this->findFirstByKey($key);

        if (!$model) {
            if ($request->wantsJson()) {
                return $this->validNotFoundJsonResponse();
            }
            abort(404);
        }

        if ($request->wantsJson()) {
            return response()->json($model, 200, [], JSON_PRETTY_PRINT);
        }

        $view = $this->getViewLocation(__FUNCTION__);
        $this->checkViewExists($view);

        return response()->view($view, compact('model'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Request $request The request object.
     * @param string  $key     The model key.
     *
     * @return mixed
     */
    public function edit(Request $request, $key)
    {
        if ($request->wantsJson()) {
            return $this->validNotFoundJsonResponse();
        }

        $model = $this->findFirstByKey($key);

        if (!$model) {
            abort(404);
        }

        $view = $this->getViewLocation(__FUNCTION__);
        $this->checkViewExists($view);

        return response()->view($view, compact('model'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request The request object.
     * @param string  $key     The model key.
     *
     * @throws RestfulControllerException
     *
     * @return mixed
     */
    public function update(Request $request, $key)
    {
        $this->getFormRequestInstance();

        $model = $this->findFirstByKey($key);

        if (!$model) {
            if ($request->wantsJson()) {
                return $this->validNotFoundJsonResponse();
            }
            abort(404);
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

        if ($request->wantsJson()) {
            $data = [
                $model
            ];

            return $this->validSuccessJsonResponse($message, $data);
        }

        return redirect()->route($this->getRedirectionRoute())
            ->with($this->successFlashMessageKey, $message);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request The request object.
     * @param string  $key     The model key.
     *
     * @throws RestfulControllerException
     *
     * @return mixed
     */
    public function destroy(Request $request, $key)
    {
        $this->getFormRequestInstance();

        $model = $this->findFirstByKey($key);

        if (!$model) {
            if ($request->wantsJson()) {
                return $this->validNotFoundJsonResponse();
            }
            abort(404);
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

        if ($request->wantsJson()) {
            $data = [
                $model
            ];

            return $this->validSuccessJsonResponse($message, $data);
        }

        return redirect()->route($this->getRedirectionRoute())
            ->with($this->infoFlashMessageKey, $message);
    }
}
