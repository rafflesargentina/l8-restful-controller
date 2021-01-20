<?php

namespace RafflesArgentina\ResourceController;

use Lang;
use Validator;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\View;
use Illuminate\Foundation\Http\FormRequest;

use RafflesArgentina\ResourceController\Contracts\ResourceControllerInterface;
use RafflesArgentina\ResourceController\Exceptions\ResourceControllerException;
use RafflesArgentina\ResourceController\Traits\FormatsValidJsonResponses;

abstract class AbstractResourceController extends BaseController
                                       implements ResourceControllerInterface
{
    use FormatsValidJsonResponses;

    /**
     * The alias for named routes.
     *
     * @var string|null
     */
    protected $alias;

    /**
     * The location for themed views.
     *
     * @var string|null
     */
    protected $theme;

    /**
     * The vendor views prefix.
     *
     * @var string|null
     */
    protected $module;

    /**
     * The prefix for named routes.
     *
     * @var string|null
     */
    protected $prefix;

    /**
     * The Repository class to instantiate.
     *
     * @var string
     */
    protected $repository;

    /**
     * The FormRequest class to instantiate.
     *
     * @var mixed|null
     */
    protected $formRequest;

    /**
     * The name of the resource.
     *
     * @var string
     */
    protected $resourceName;

    /**
     * Define if model uses SoftDeletes.
     *
     * @var boolean
     */
    protected $useSoftDeletes;

    /**
     * The info flash message key.
     *
     * @var string|null
     */
    protected $infoFlashMessageKey = 'rafflesargentina.status.info';

    /**
     * The error flash message key.
     *
     * @var string|null
     */
    protected $errorFlashMessageKey = 'rafflesargentina.status.error';

    /**
     * The success flash message key.
     *
     * @var string|null
     */
    protected $successFlashMessageKey = 'rafflesargentina.status.success';

    /**
     * The warning flash message key.
     *
     * @var string|null
     */
    protected $warningFlashMessageKey = 'rafflesargentina.status.warning';

    /**
     * Create a new AbstractResourceController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->_checkRepositoryProperty();
        $this->_checkResourceNameProperty();
        $this->_formatRouteNameAndViewPathModifiers();

        $this->repository = app()->make($this->repository);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request The request object.
     *
     * @return mixed
     */
    public abstract function index(Request $request);

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request The request object.
     *
     * @return mixed
     */
    public abstract function create(Request $request);

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request The request object.
     *
     * @throws ResourceControllerException
     *
     * @return mixed
     */
    public abstract function store(Request $request);

    /**
     * Display the specified resource.
     *
     * @param Request $request The request object.
     * @param string  $key     The model key.
     *
     * @return mixed
     */
    public abstract function show(Request $request, $key);

    /**
     * Show the form for editing the specified resource.
     *
     * @param Request $request The request object.
     * @param string  $key     The model key.
     *
     * @return mixed
     */
    public abstract function edit(Request $request, $key);

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request The request object.
     * @param string  $key     The model key.
     *
     * @throws ResourceControllerException
     *
     * @return mixed
     */
    public abstract function update(Request $request, $key);

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request The request object.
     * @param string  $key     The model key.
     *
     * @throws ResourceControllerException
     *
     * @return mixed
     */
    public abstract function destroy(Request $request, $key);

    /**
     * Get named route for the specified action.
     *
     * @param string $action The action.
     *
     * @return string
     */
    public function getRouteName($action)
    {
        return $this->alias.$this->resourceName.$action;
    }

    /**
     * Validate rules from a FormRequest instance.
     *
     * @return \Illuminate\Validation\Validator
     */
    public function validateRules()
    {
        $input = request()->all();
        $rules = [];
        $messages = [];

        if ($this->formRequest) {
            $this->formRequest = new $this->formRequest;
            $rules = $this->formRequest->rules();
            $messages = $this->formRequest->messages();
        }

        return Validator::make($input, $rules, $messages);
    }

    /**
     * Throw an exception if the view doesn't exist.
     *
     * @param string $view The view.
     *
     * @throws ResourceControllerException
     *
     * @return void
     */
    public function checkViewExists($view)
    {
        if (!View::exists($view)) {
            if (Lang::has('resource-controller.viewnotfound')) {
                $message = trans('resource-controller.viewnotfound', ['view' => '$view']);
            } else {
                $message = 'Requested page couldn\'t be loaded because the view file is missing: '.$view;
            }

            throw new ResourceControllerException($message);
        }
    }

    /**
     * Find first by key.
     *
     * @param string $key The model key.
     *
     * @return Model|null
     */
    public function findFirstByKey($key)
    {
        if ($this->useSoftDeletes) {
            return $this->repository
                ->withTrashed()
                ->where($this->repository->getRouteKeyName(), $key)
                ->first();
        }

        return $this->repository
            ->findBy($this->repository->getRouteKeyName(), $key);
    }

    /**
     * Get the FormRequest instance.
     *
     * @return mixed
     */
    public function getFormRequestInstance()
    {
        if (!$this->formRequest) {
            return new FormRequest;
        }
        
        return app()->make($this->formRequest);
    }


    /**
     * Get items collection.
     *
     * @param string $orderBy The order key.
     * @param string $order   The order direction.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getItemsCollection($orderBy = 'updated_at', $order = 'desc')
    {
        if ($this->useSoftDeletes) {
            return $this->repository->withTrashed()->orderBy($orderBy, $order)->get();
        }

        return $this->repository->orderBy($orderBy, $order)->get();
    }

    /**
     * Get Paginator instance.
     *
     * @param string $orderBy The order key.
     * @param string $order   The order direction.
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getPaginatorInstance($orderBy = 'updated_at', $order = 'desc')
    {
        if ($this->useSoftDeletes) {
            return $this->repository->withTrashed()->orderBy($orderBy, $order)->paginate();
        }

        return $this->repository->orderBy($orderBy, $order)->paginate();
    }

    /**
     * Get redirection route.
     *
     * @return string
     */
    public function getRedirectionRoute()
    {
        return $this->getRouteName('index');
    }

    /**
     * Get view location for the specified action.
     *
     * @param string $action The action.
     *
     * @return string
     */
    public function getViewLocation($action)
    {
        if (request()->ajax()) {
            return $this->module.$this->theme.$this->resourceName.'ajax.'.$action;
        }

        return $this->module.$this->theme.$this->resourceName.$action;
    }

    /**
     * Redirect back with errors.
     *
     * @param \Illuminate\Validation\Validator $validator The validator instance.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectBackWithErrors($validator)
    {
        if (request()->wantsJson()) {
            return $this->validUnprocessableEntityJsonResponse($validator->errors());
        }

        return back()->withErrors($validator)->withInput();
    }

    /**
     * Format route name and view path modifiers.
     *
     * @return void
     */
    private function _formatRouteNameAndViewPathModifiers()
    {
        if ($this->alias) {
            $this->alias = str_finish($this->alias, '.');
        }

        if ($this->theme) {
            $this->theme = str_finish($this->theme, '.');
        }

        if ($this->module) {
            if (!ends_with($this->module, '::')) {
                $this->module .= '::';
            }
        }

        if ($this->prefix) {
            $this->prefix = str_finish($this->prefix, '.');
        }

        if ($this->resourceName) {
            $this->resourceName = str_finish($this->resourceName, '.');
        }
    }

    /**
     * Throw an exception if repository property is not set.
     *
     * @throws ResourceControllerException
     *
     * @return void
     */
    private function _checkRepositoryProperty()
    {
        if (!$this->repository) {
            if (Lang::has('resource-controller.propertynotset')) {
                $message = trans('resource-controller.propertynotset', ['property' => '$repository']);
            } else {
                $message = '$repository property must be set.';
            }

            throw new ResourceControllerException($message);
        }
    }

    /**
     * Throw an exception if resourceName property is not set.
     *
     * @throws ResourceControllerException
     *
     * @return void
     */
    private function _checkResourceNameProperty()
    {
        if (!$this->resourceName) {
            if (Lang::has('resource-controller.propertynotset')) {
                $message = trans('resource-controller.propertynotset', ['property' => '$resourceName']);
            } else {
                $message = '$resourceName property must be set.';
            }

            throw new ResourceControllerException($message);
        }
    }
}
