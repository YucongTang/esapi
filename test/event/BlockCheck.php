<?php

namespace test\Event;

use Esapi\Interfaces\EventInterface;

class BlockCheck implements EventInterface
{
    public function handle($args = null)
    {
//        print_r($args);exit;
    }
}