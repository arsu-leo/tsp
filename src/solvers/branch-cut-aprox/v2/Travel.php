<?php

/**
 * Class to keep track of a branch state
 */
class TravelBCAV2
{
  /**
   * @param  array $route Route done
   * @param  int $city City index
   * @param  int $travels Amount of travels done for this branch
   * @param  array $distances Matrix with different weights
   * @param  float $weight Weight to get to this route
   */
  public function __construct(array $route, int $city, int $travels, array $distances, float $weight)
  {
    $this->route      = $route;
    $this->city       = $city;
    $this->travels    = $travels;
    $this->distances  = $distances;
    $this->weight     = $weight;
  }
}
