<?php


namespace Hennig\Builder;


trait HasEvents
{
    /**
     * Set event handlers
     * Ex.:
     *  ->on('click','js_function_name', [param1]);
     *
     * @param string $jsevent
     * @param string $method
     * @param array $params
     * @return $this
     */
    public function on($jsevent, $method, $params = [])
    {
        $this->on[$jsevent] = ['method' => $method, 'params' => $params];
        return $this;
    }
}
