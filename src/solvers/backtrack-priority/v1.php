<?php

require_once(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'TravelerSolver.php');
require_once(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'ReversePriorityQueue.php');
require_once(__DIR__ . DIRECTORY_SEPARATOR . 'Travel.php');

/**
 * Now we try to approach reducing the amount of cases
 * After some documentation, felt this as a natural approach before taking the hard way implementation of the branch and bound algoritm
 */
class BacktrackPriorityTravelerSolverV1 extends TravelerSolver
{
  /**
   * __construct
   *
   * @param  array $distances: 2d array of distances where [i,j] is the distance between city on index i and city on index j
   */
  public function __construct(array $distances)
  {
    parent::__construct($distances);
    $this->count      = count($distances);
    $this->bestRoute  = array("route" => [], "distance" => INF);
  }

  private function isSolved() : bool
  {
    return count($this->bestRoute["route"]) > 0;
  }

  /**
   * Calculate a solution
   *
   * @return array with the solution having keys [route, distance] where route is an int array with the indexes of cities and distance is a float with that route distance
   * @abstract
   */
  public function solve() : array
  {
    $queue      = new ReversePriorityQueue();
    $distances  = $this->distances;
    $count      = $this->count;


    for($i = 1; $i < $this->count; $i++)
    {
      $travel = new TravelBt([0], $i, $this->distances[0][$i], 0, $count);
      $queue->insert($travel, $travel->totalDistance);
    }

    while($queue->valid())
    {
      $lowestTravel = $queue->extract();
      //Is this ok? as soon as we have one complete we are done?
      if($lowestTravel->length == $count)
      {
        return array(
          "route"     => $lowestTravel->route,
          "distance"  => $lowestTravel->totalDistance
        );
      }
      for($i = 1; $i < $count; $i++)
      {
        if(!$lowestTravel->isVisited($i))
        {
          $newDistance  =  $this->distances[$lowestTravel->currentCity][$i];
          $travel       = new TravelBt($lowestTravel->route, $i, $newDistance, $lowestTravel->totalDistance, $count);
          $queue->insert($travel, $travel->totalDistance);
        }
      }
    }
    die("SHould not happen");
  }
}
