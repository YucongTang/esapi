<?php

namespace test\app\v1;

use Esapi\Console\Application;

class test extends Application
{
    public function index()
    {
        return $this->input->get();
    }
}