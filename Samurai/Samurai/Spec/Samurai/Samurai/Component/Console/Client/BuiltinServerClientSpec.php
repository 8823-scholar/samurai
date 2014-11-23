<?php

namespace Samurai\Samurai\Spec\Samurai\Samurai\Component\Console\Client;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;
use PhpSpec\Exception\Example\PendingException;

class BuiltinServerClientSpec extends PHPSpecContext
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Samurai\Component\Console\Client\BuiltinServerClient');
    }


    public function it_is_log()
    {
        throw new PendingException();
        // how shelve error_log ?
        $this->log('log to built-in server console.');
    }
    
    public function it_is_info_log()
    {
        throw new PendingException();
        // how shelve error_log ?
        $this->info('info log to built-in server console.');
    }
    
    public function it_is_warn_log()
    {
        throw new PendingException();
        // how shelve error_log ?
        $this->warn('warn log to built-in server console.');
    }
    
    public function it_is_error_log()
    {
        throw new PendingException();
        // how shelve error_log ?
        $this->error('error log to built-in server console.');
    }
}

