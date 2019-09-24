<?php

class TravelBBV1
{
  public function __construct(array $route, int $city, int $travels, array $distances, float $weight)
  {
    $this->route      = $route;
    $this->city       = $city;
    $this->travels    = $travels;
    $this->distances  = $distances;
    $this->weight     = $weight;
  }
}
