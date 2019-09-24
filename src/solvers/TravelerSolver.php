<?php

/**
 *  Abstract class to define an interface to solve the problem common to each solution I come up with
 */
abstract class TravelerSolver
{
  protected $distances;

  /**
   * __construct
   *
   * @param  mixed $distances data strcuture containg the initial distances data
   */
  public function __construct($distances)
  {
    $this->distances = $distances;
  }


  /**
   * Calculate a solution
   *
   * @return array with the solution
   * @abstract
   */
  abstract public function solve() : array;
}
