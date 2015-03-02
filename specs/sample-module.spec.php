<?php
namespace Peridot\Easy\Test;

require_once(__DIR__. '/../src/sample-module.php');

use Peridot\Test\Example\SampleModule;

describe('Sample Module', function() {
  beforeEach(function() {
    $this->module = new SampleModule('hoge');
  });

  afterEach(function() {
    unset($this->module);
  });

  context('when using a context', function() {
    describe('hello()', function() {
      it('should return "hello <name>"', function() {
        assert()->strictEqual($this->module->hello(), 'hello hoge');
      });
    });

    xdescribe('this test is pending', function() {
      it('bye() <not implement>', function() {
        expect($this->module->bye())->to->equal('bye hoge');
      });
    });
  });

  describe('this test will fail', function() {
    it('this will not pass!! OMG', function() {
      $this->module->bad();
    });
  });
});
