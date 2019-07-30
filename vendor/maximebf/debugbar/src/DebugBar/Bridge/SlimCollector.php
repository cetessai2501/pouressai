<?php
/*
 * This file is part of the DebugBar package.
 *
 * (c) 2013 Maxime Bouroumeau-Fuseau
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DebugBar\Bridge;

use DebugBar\DataCollector\MessagesCollector;
use Psr\Log\LogLevel;
use Slim\Log;
use App\MyApp;

/**
 * Collects messages from a Slim logger
 *
 * http://slimframework.com
 */
class SlimCollector extends MessagesCollector
{
    protected $slim;

    protected $originalLogWriter;

    public function __construct(MyApp $slim)
    {
        $this->slim = $slim;
        
    }

    
}
