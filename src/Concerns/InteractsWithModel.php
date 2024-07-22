<?php

namespace R3bzya\ActionWrapper\Concerns;

use Illuminate\Database\Eloquent\Model;
use R3bzya\ActionWrapper\ActionWrapper;

trait InteractsWithModel
{
    /**
     * Reload the model instance with fresh attributes from the database.
     */
    public function refreshModel(): ActionWrapper|static
    {
        return $this->after(fn(mixed $model) => $model instanceof Model ? $model->refresh() : $model);
    }

    /**
     * Unset all the loaded relations for the model instance.
     */
    public function unsetModelRelations(): ActionWrapper|static
    {
        return $this->after(fn(mixed $model) => $model instanceof Model ? $model->unsetRelations() : $model);
    }
}