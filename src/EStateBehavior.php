<?php

class EStateBehavior extends CBehavior
{

    public $attr = 'status';
    public $initial = '';
    public $prefix = '';
    public $transitions = array();
    private $options = array();
    private $status = null;

    public function attach($owner)
    {
        parent::attach($owner);

        if (!isset($this->prefix) || empty($this->prefix))
            $this->prefix = get_class($this->getOwner());
        
        if (!isset($this->initial))
            throw new CException();

        $this->options = array_keys($this->transitions);

        foreach ($this->transitions as $k => $t)
        {
            if (!is_array($t))
                $this->transitions[$k] = explode(",", $t);
        }

        $this->getStatus();
    }

    private function getStatus()
    {
        if ($this->status)
            return $this->status;
        else
            return $this->setStatus($this->getStatusId());
    }

    private function setStatus($id)
    {
        if ($this->setStatusId($id))
        {
            $className = $this->prefix.ucfirst($this->getStatusId());
            $this->status = new $className;
            $this->getOwner()->detachBehavior($this->attr);
            $this->getOwner()->attachBehavior($this->attr, $this->status);
            $this->getStatus();
        }

        return $this->status;
    }

    private function getStatusId()
    {
        if (($id = $this->getOwner()->{$this->attr}) === null)
            return $this->initial;
        else
            return $id;
    }

    private function setStatusId($id)
    {
        if (!in_array($id, $this->options))
            throw new CException('Status not avaiable');

        $this->getOwner()->{$this->attr} = $id;
        return true;
    }

    private function canChangeTo($id)
    {
        return in_array($id, $this->options) &&
        in_array($id, $this->transitions[$this->getStatusId()]) && $this->status->canChangeTo($id);
    }

    public function changeTo($id, $data=array())
    {
        if ($this->canChangeTo($id))
        {
            if ($this->status instanceof EStateStatus)
                $this->status->onExit(new EStateEvent($this, $data));
            
            $this->setStatus($id);
            $this->status->onEntry(new EStateEvent($this, $data));
            return true;
        }
        else
            throw new CException('Cant change from '.$this->getStatusId().' to status '.$id);
        }
    }

}
