<?php
use Evenement\EventEmitterInterface;

return function(EventEmitterInterface $emitter) {
  $bootstrap = require('./peridot-easy/bootstrap.php');
  $bootstrap($emitter);

  // Write your config
};
