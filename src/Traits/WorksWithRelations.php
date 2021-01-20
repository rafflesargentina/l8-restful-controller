<?php

namespace RafflesArgentina\RestfulController\Traits;

use Lang;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Relations\{HasOne, MorphOne, BelongsTo};
use Illuminate\Database\Eloquent\Relations\{HasMany, MorphMany, MorphToMany, BelongsToMany};
use Illuminate\Database\Eloquent\MassAssignmentException;

trait WorksWithRelations
{
    /**
     * Update or create relations handling array type request data.
     *
     * @param Request $request The Request object.
     * @param Model   $model   The eloquent model.
     *
     * @return void
     */
    public function updateOrCreateRelations(Request $request, Model $model)
    {
        $parameterBag = $request->request;
        foreach ($parameterBag->all() as $name => $attributes) {
            if (is_array($request->{$name})) {
                $this->_checkRelationExists($model, $name);

                $relation = $model->{$name}();
                $this->handleRelations($attributes, $model, $relation);
            }
        }
    }

    /**
     * Handle relations.
     *
     * @param array    $fillable The relation fillable.
     * @param Model    $model    The eloquent model.
     * @param Relation $relation The eloquent relation.
     *
     * @return void
     */
    protected function handleRelations(array $fillable, Model $model, Relation $relation)
    {
        switch (true) {
        case $relation instanceof HasOne || $relation instanceof MorphOne:
            $this->updateOrCreateHasOne($fillable, $model, $relation);
            break;
        case $relation instanceof BelongsTo:
            $this->updateOrCreateBelongsToOne($fillable, $model, $relation);
            break;
        case $relation instanceof HasMany || $relation instanceof MorphMany:
            $this->updateOrCreateHasMany($fillable, $model, $relation);
            break;
        case $relation instanceof BelongsToMany || $relation instanceof MorphToMany:
            $this->updateOrCreateBelongsToMany($fillable, $model, $relation);
            break;
        }
    }

    /**
     * HasOne relation updateOrCreate logic.
     *
     * @param array    $fillable The relation fillable.
     * @param Model    $model    The eloquent model.
     * @param Relation $relation The eloquent relation.
     *
     * @return Model | null
     */
    protected function updateOrCreateHasOne(array $fillable, Model $model, Relation $relation)
    {
        $id = '';

        if (array_key_exists('id', $fillable)) {
            $id = $fillable['id'];
        }

        if (array_except($fillable, ['id'])) {
            if (property_exists($this, 'pruneHasOne') && $this->pruneHasOne !== false) {
                $relation->update($fillable);
            }

            return $relation->updateOrCreate(['id' => $id], $fillable);
        }

        return null;
    }

    /**
     * BelongsToOne relation updateOrCreate logic.
     *
     * @param array    $fillable The relation fillable.
     * @param Model    $model    The eloquent model.
     * @param Relation $relation The eloquent relation.
     *
     * @return Model
     */
    protected function updateOrCreateBelongsToOne(array $fillable, Model $model, Relation $relation)
    {
        $related = $relation->getRelated();

        if (array_except($fillable, ['id'])) {
            if (!$relation->first()) {
                $record = $relation->associate($related->create($fillable));
                $model->save();
            } else {
                $record = $relation->update($fillable);
            }

            return $record;
        }

        return null;
    }

    /**
     * HasMany relation updateOrCreate logic.
     *
     * @param array    $fillable The relation fillable.
     * @param Model    $model    The eloquent model.
     * @param Relation $relation The eloquent relation.
     *
     * @return array
     */
    protected function updateOrCreateHasMany(array $fillable, Model $model, Relation $relation)
    {
        $keys = [];
        $id = '';
        $records = [];

        foreach ($fillable as $fields) {
            if (is_array($fields)) {
                if (array_key_exists('id', $fields)) {
                    $id = $fields['id'];
                }

                if (array_except($fields, ['id'])) {
                    $record = $relation->updateOrCreate(['id' => $id], $fields);
                    array_push($keys, $record->id);
                    array_push($records, $record);
                }
            } else {
                if (array_except($fillable, ['id'])) {
                    $record = $relation->updateOrCreate(['id' => $id], $fillable);
                    array_push($keys, $record->id);
                    array_push($records, $record);
                }
            }
        }

        if ($keys && (property_exists($this, 'pruneHasMany') && $this->pruneHasMany !== false)) {
            $notIn = $relation->getRelated()->whereNotIn('id', $keys)->get();
            foreach ($notIn as $record) {
                $record->delete();
            }
        }

        return $records;
    }

    /**
     * BelongsToMany relation updateOrCreate logic.
     *
     * @param array    $fillable The relation fillable.
     * @param Model    $model    The eloquent model.
     * @param Relation $relation The eloquent relation.
     *
     * @return array
     */
    protected function updateOrCreateBelongsToMany(array $fillable, Model $model, Relation $relation)
    {
        $keys = [];
        $records = [];

        $related = $relation->getRelated();

        foreach ($fillable as $fields) {
            if (array_key_exists('id', $fields)) {
                $id = $fields['id'];
                array_push($keys, $id);
            } else {
                $id = '';
            }

            if (array_except($fields, ['id'])) {
                $record = $related->updateOrCreate(['id' => $id], $fields);
                array_push($keys, $record->id);
                array_push($records, $record);
            }
        }

        $relation->sync($keys);

        return $records;
    }

    /**
     * Throw an exception if array type request data is not named after an existent Eloquent relation.
     *
     * @param Model  $model        The eloquent model.
     * @param string $relationName The eloquent relation name.
     *
     * @throws MassAssignmentException
     *
     * @return void
     */
    private function _checkRelationExists(Model $model, string $relationName)
    {
        if (!method_exists($model, $relationName) || !$model->{$relationName}() instanceof Relation) {
            if (Lang::has('resource-controller.data2relationinexistent')) {
                $message = trans('resource-controller.data2relationinexistent', ['relationName' => $relationName]);
            } else {
                $message = "Array type request data '{$relationName}' must be named after an existent relation.";
            }

            throw new MassAssignmentException($message);
        }
    }
}
