<?php

namespace Hennig\Common\Controller;

use Illuminate\Database\Eloquent\Model;

trait HasModelFunctions
{
    /**
     * @return Model
     */
    abstract public function getModel();

    /**
     * @param Model $model
     * @return Model
     */
    abstract public function beforeGetData($model);
    
    /**
     * Helper called on form load
     *
     * @param string $id
     * @return array
     */
    public function getFormData(string $id)
    {
        if ($id) {
            $model = $this->beforeGetData($this->getModel());
            $row = $model->find($id);
            if (empty($row)) {
                return null;
            }

            if (method_exists($this, 'afterGetData')) {
                return $this->afterGetData($row);
            }

            return $row->toArray();
        }

        return $this->getModel()->newModelInstance();
    }
}
