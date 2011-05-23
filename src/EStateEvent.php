<?php

class EStateEvent extends CEvent
{
    public $data;

    public function  __construct($sender = null, $data = array())
    {
        $this->data = $data;
        parent::__construct($sender);
    }
}
