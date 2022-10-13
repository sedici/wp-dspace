<?php
namespace Wp_dspace\Util;

abstract class queryMaker
{
    protected $model;
    protected $order;

    public abstract function buildQueryString();
    public abstract function executeQuery();
    
    public function get_model()
    {
        return $this->model;
    }

    public function setCmp($value)
    {
        $this->order->setCmp($value);
    }
    
    public function splitImputs($imput)
    {
        return explode(';', $imput);
    }
    
}

?>