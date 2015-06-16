<?php
class Flash extends Phalcon\Flash\Direct
{
    public function message($type, $message)
    {
        $message .= ' <div class="callout callout-danger">';
        parent::message($type, $message.'</div>');
    }
}