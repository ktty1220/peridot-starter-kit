<?php
namespace Peridot\Easy;

use Evenement\EventEmitterInterface;
use Peridot\Console\Environment;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\OutputOption;

/**
 * Class CodeCoverage
 * @package Peridot\Easy
 */
class CodeCoverage {
  /**
   * @var EventEmitterInterface
   */
  private $emitter = null;

  /**
   * @var PHP_CodeCoverage
   */
  private $coverage = null;

  /**
   * @var PHP_CodeCoverage_Filter
   */
  private $filter = null;

  /**
   * @var is windows
   */
  private $windows = false;

  /**
   * @var Code coverage report directory
   */
  private $reportDir = [
    'html' => null,
    'clover' => null
  ];

  /**
   * Constructor.
   *
   * @param EventEmitterInterface $emitter
   */
  public function __construct(EventEmitterInterface $emitter) {
    $this->emitter = $emitter;
    $this->windows = (strpos(PHP_OS, 'WIN') !== false);
  }

  /**
   * Register the reporters.
   *
   * @return $this
   */
  public function register() {
    $this->emitter->on('peridot.start', [$this, 'onPeridotStart']);
    $this->emitter->on('peridot.execute', [$this, 'onPeridotExecute']);
    $this->emitter->on('runner.start', [$this, 'onRunnerStart']);
    $this->emitter->on('runner.end', [$this, 'onRunnerEnd']);
    return $this;
  }

  /**
   * Handle the peridot.start event.
   *
   * @param Environment $env
   */
  public function onPeridotStart(Environment $env) {
    $def = $env->getDefinition();
    $def->option(
      'coverage-html', null, InputOption::VALUE_REQUIRED,
      'Code coverage(HTML) report directory'
    );
    $def->option(
      'coverage-clover', null, InputOption::VALUE_REQUIRED,
      'Code coverage(Clover) report directory'
    );
    $def->option(
      'coverage-blacklist', 'B',
      InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
      'Blacklist file/dir for Code coverage'
    );
    $def->option(
      'coverage-whitelist', 'W',
      InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
      'Whitelist file/dir for Code coverage'
    );
    $def->getArgument('path')->setDefault('specs');
  }

  /**
   * Handle the peridot.execute event.
   *
   * @param InputInterface $input
   * @param OutputInterface $output
   */
  public function onPeridotExecute(InputInterface $input, OutputInterface $output) {
    $cov = false;
    foreach ($this->reportDir as $type => $path) {
      $dir = $input->getOption("coverage-${type}");
      if (! empty($dir)) {
        $cov = true;
        $this->reportDir[$type] = $dir;
      }
    }

    if (! $cov) { return; }

    if (! class_exists('PHP_CodeCoverage')) {
      throw new \RuntimeException('PHP_CodeCoverage is not installed');
    }

    $this->filter = new \PHP_CodeCoverage_Filter();
    $defaultBlacklists = [
      getenv('HOME'). (($this->windows) ? '\AppData\Roaming\Composer' : '/.composer'),
      getcwd(). '/vendor',
      $input->getArgument('path')
    ];
    foreach ($defaultBlacklists as $bl) {
      $this->filter->addDirectoryToBlacklist($bl);
    }

    foreach ($input->getOption("coverage-blacklist") as $bl) {
      if (is_dir($bl)) {
        $this->filter->addDirectoryToBlacklist($bl);
      } else if (file_exists($bl)) {
        $this->filter->addFileToBlacklist($bl);
      }
    }

    foreach ($input->getOption("coverage-whitelist") as $wl) {
      if (is_dir($wl)) {
        $this->filter->addDirectoryToWhitelist($wl);
      } else if (file_exists($wl)) {
        $this->filter->addFileToWhitelist($wl);
      }
    }

    $this->coverage = new \PHP_CodeCoverage(null, $this->filter);
  }

  /**
   * Handle the runner.start event.
   */
  public function onRunnerStart() {
    if (! $this->coverage) { return; }
    $this->coverage->start('peridot');
  }

  /**
   * Handle the runner.start event.
   *
   * @param float $runTime
   */
  public function onRunnerEnd($runTime) {
    if (! $this->coverage) { return; }
    $this->coverage->stop();

    if (! empty($this->reportDir['html'])) {
      $writer = new \PHP_CodeCoverage_Report_HTML();
      $writer->process($this->coverage, $this->reportDir['html']);
    }

    if (! empty($this->reportDir['clover'])) {
      $writer = new \PHP_CodeCoverage_Report_Clover();
      $writer->process($this->coverage, $this->reportDir['clover']. '/clover.xml');
    }
  }
}
