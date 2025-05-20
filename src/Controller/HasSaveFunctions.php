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
        $builder = $this->getModel();
        $pk_name = $builder->getModel()->getKeyName();

        if (empty($params[$pk_name])) {
            $model = $builder->make();
        } else {
            $model = $builder->find($params[$pk_name]);
        }

        if (empty($model)) {
            throw new ERecordNotFound();
        }

        if (method_exists($this, 'beforeSave')) {
            $params = $this->beforeSave($model, $params);
        }

        $model->fill($params);
        $model->save();

        if (method_exists($this, 'afterSave')) {
            $this->afterSave($model, $params);
        }

        return [
            'message' => $this->saved_message,
            'data' => $model
        ];
    }

    public function saveMany($params)
    {
        if (!$params) {
            throw new ESimple('Parâmetro de "saveMany" não informado');
        }

        if (!is_array($params)) {
            throw new ESimple('Parâmetro de "saveMany" incorreto');
        }

        $responses = [];
        foreach ($params as $p) {
            try {
                $r = $this->save($p);
                $responses[] = ['result' => $r['data']];
            } catch (\Throwable $e) {
                $responses[] = ['error' => $e->getMessage()];
            }
        }

        return $responses;
    }
}
