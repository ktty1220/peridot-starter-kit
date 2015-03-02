<?php
use Evenement\EventEmitterInterface;

return function(EventEmitterInterface $emitter) {
  $bootstrap = require('./peridot-easy/bootstrap.php');
  $bootstrap($emitter);

  // Write your config
  /*
  $emitter->on('peridot.configure', function ($configuration, $application) {
    if (preg_match('/utf-8/i', getenv('LANG')) === 1) {
      $configuration->inputEncoding = 'Shift_JIS';
      $configuration->outputEncoding = 'UTF-8';
    }
  };
   */
};
