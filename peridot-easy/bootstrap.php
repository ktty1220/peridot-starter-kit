<?php
require_once(__DIR__. '/autoload.php');

use Evenement\EventEmitterInterface;
use Peridot\Easy\CodeCoverage;
use Peridot\Easy\SpecReporterPlugin;

return function(EventEmitterInterface $emitter) {
  //exclude coverage from hhvm because its pretty flawed at the moment
  if (! defined('HHVM_VERSION')) {
    (new CodeCoverage($emitter))->register();
  }

  new SpecReporterPlugin($emitter);

  $bootstrap = getenv('PERIDOT_BOOTSTRAP');
  if (! empty($bootstrap)) {
    $bsfile = realpath($bootstrap);
    if (! file_exists($bsfile)) {
      throw new Exception('failed to load PERIDOT_BOOTSTRAP');
    }
    $configure = require_once($bsfile);
    // 2nd argument is "load from peridot-easy" flag
    $configure($emitter, true);
  }
};