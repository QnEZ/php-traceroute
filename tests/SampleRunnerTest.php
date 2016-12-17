<?php

namespace Qnez\TraceRoute\Tests;

use PHPUnit\Framework\TestCase;
use Qnez\TraceRoute\TraceRoute;

class SampleRunnerTest extends TestCase
{
    public function test_processing_finds_path()
    {
        $trace = new TraceRoute('127.0.0.1');

        $trace->process();
    }
}