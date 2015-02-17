<?php
namespace Peridot\Easy;

use Peridot\Core\Test;
use Peridot\Core\Suite;
use Peridot\Runner\Context;
use Peridot\Core\TestInterface;

class SpecReporter extends \Peridot\Reporter\SpecReporter {
  /**
   * Initialize reporter. Setup and listen for runner events
   *
   * @return void
   */
  public function init() {
    if (strpos(PHP_OS, 'WIN') !== false) {
      $this->symbols['check'] = '/';
    }
    $this->colors['file'] = ['left' => "\033[33m", 'right' => "\033[39m"];
    parent::init();
  }

  /**
   * Output a test failure.
   *
   * @param int $errorIndex
   * @param Test $test
   * @param $exception - an exception like interface with ->getMessage(), ->getTraceAsString()
   */
  protected function outputError($errorNumber, TestInterface $test, $exception) {
    $this->output->writeln(sprintf("  %d)%s:", $errorNumber, $test->getTitle()));
    $message = sprintf("     %s", str_replace(PHP_EOL, PHP_EOL . "     ", $exception->getMessage()));
    $this->output->writeln($this->color('error', $message));
    $this->output->writeln('');
  }

  /**
   * @param Suite $suite
   */
  public function onSuiteStart(Suite $suite) {
    if ($suite != $this->root) {
      ++$this->column;
      $file = '';
      if ($suite->getParent() === $this->root) {
        $file = $this->color('file', ' ['. basename($suite->getFile()). ']');
      }
      $this->output->writeln(sprintf('%s%s%s', $this->indent(), $suite->getDescription(), $file));
    }
  }
}
