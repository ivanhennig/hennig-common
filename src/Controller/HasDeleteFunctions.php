<?php

namespace Hennig\Common\Controller;

trait HasDeleteFunctions
{
    /**
     * Delete a record and generate logs
     *
     * @param string $id
     * @return bool
     */
    public function delete(string $id) {
        $this->getModel()->getModel()->destroy($id);
        return true;
    }
}
