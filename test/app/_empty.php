<?php

namespace test\app;

use Esapi\Console\Application;

class _empty extends Application
{
    public function index()
    {
        raise('file is not found',404);
    }
}