<?php

abstract class EStateStatus extends CBehavior {

    public function canChangeTo($id)
    {
        return true;
    }

    public function onEntry($event)
    {
        $this->raiseEvent('onEntry', $event);
    }

    public function onExit($event)
    {
         $this->raiseEvent('onExit', $event);
    }
}
