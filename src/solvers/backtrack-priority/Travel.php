<?php

class TravelBt
{
  public function __construct($previousRoute, $currentCity, $lastDistance, $previousDistance, $n)
  {
    $this->visited      = array_fill(0, $n, false);
    $this->route        = $previousRoute;
    $this->route[]      = $currentCity;
    foreach ($this->route as $city)
    {
      $this->visited[$city] = true;
    }
    $this->currentCity      = $currentCity;
    $this->lastDistance     = $lastDistance;
    $this->previousDistance = $previousDistance;
    $this->totalDistance    = $previousDistance + $lastDistance;
    $this->length           = count($this->route);
  }

  public function isVisited($i)
  {
    return $this->visited[$i];
  }
}
