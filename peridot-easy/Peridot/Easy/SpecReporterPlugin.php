<?php
namespace Peridot\Easy;

use Evenement\EventEmitterInterface;
use Peridot\Reporter\ReporterFactory;
use Symfony\Component\Console\Input\InputInterface;

require_once(__DIR__. '/SpecReporter.php');

/**
 * This plugin registers the Easy\SpecReporter with Peridot
 * @package Peridot\Easy\SpecReporter
 */
class SpecReporterPlugin {
  /**
   * @var EventEmitterInterface
   */
  protected $emitter;

  /**
   * @param EventEmitterInterface $emitter
   */
  public function __construct(EventEmitterInterface $emitter) {
    $this->emitter = $emitter;
    $this->emitter->on('peridot.reporters', [$this, 'onPeridotReporters']);
  }

  /**
   * @param InputInterface $input
   * @param ReporterFactory $reporters
   */
  public function onPeridotReporters(InputInterface $input, ReporterFactory $reporters) {
    $reporters->register(
      'easy',
      'spec reporter on gulp-easy-peridot',
      'Peridot\Easy\SpecReporter'
    );
  }
}
