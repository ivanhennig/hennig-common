<?php

namespace Hennig\Common\Controller;

use Hennig\Common\ERecordNotFound;
use Hennig\Common\ESimple;
use Illuminate\Database\Eloquent\Model;

trait HasSaveFunctions
{
    use HasModelFunctions;

    protected $saved_message = 'Registro foi salvo com sucesso';

    /**
     * @param array $params
     * @return array
     * @throws ESimple
     */
    public function save($params)
    {
        /** @var Model $model */
        if (empty($params['_id'])) {
            $model = $this->getModel()->make();
        } else {
            $model = $this->getModel()->find($params['_id']);
        }

        if (empty($model)) {
            throw new ERecordNotFound();
        }

        if (method_exists($this, 'beforeSave')) {
            $params = $this->beforeSave($model, $params);
        }

        $model->fill($params);
        $model->save();

        return [
            'message' => $this->saved_message,
            'data' => $model
        ];
    }
}
