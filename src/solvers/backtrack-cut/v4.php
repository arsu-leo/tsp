<?php

require_once(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'TravelerSolver.php');

/**
 * Here we try to avoid operations over arrays as unnecesary assignements (copies) and passing as parameters that copies aswell
 */
class BacktrackCutTravelerSolverV4 extends TravelerSolver
{
  /**
   * __construct
   *
   * @param  array $distances: 2d array of distances where [i,j] is the distance between city on index i and city on index j
   */
  public function __construct(array $distances)
  {
    parent::__construct($distances);
    $this->count = count($distances);
  }

  /**
   * Calculate a solution
   *
   * @return array with the solution having keys [route, distance] where route is an int array with the indexes of cities and distance is a float with that route distance
   * @abstract
   */
  public function solve() : array
  {
    $bestDist   = INF;
    $bestRoute  = array("route" => [], "distance" => INF);
    $visited    = array();
    for($i = 0; $i < $this->count; $i++)
    {
      $visited[] = false;
    }
    $this->permute([], $visited, 0, 0, $bestRoute);
    if(!$bestRoute)
    {
      $err = "Unexpected no route result {$bestDist}";
      throw new Error($err);
    }
    return $bestRoute;
  }


  /**
   * Performs a recursive iteration until the route is the size of the amount of cities
   * @return array containing the best result for the possible routes possible from the input
   */
  protected function permute(array $route, array $visited, int $currentCity,  float $accDistance, array &$bestRoute)
  {
    // Arrays are passed by value, so we use that do avoid reassigning multiple times unnecesarily
    $route[]                = $currentCity;

    // Route length is equal to num of distances
    // num of distanes is equal to num of cities
    if(count($route) === $this->count)
    {
      if($accDistance < $bestRoute["distance"])
      {
        $bestRoute["route"]     = $route;
        $bestRoute["distance"]  = $accDistance;
      }
      return;
    }

    $visited[$currentCity]  = true;

    // Loop through all the indexes
    // Keep the best result
    // Skip going deeper if result is worst than best one found
    for($i = 1; $i < $this->count; $i++)
    {
      // Not permuted yet
      if(!$visited[$i])
      {
        $nextRouteDistance  = $accDistance + $this->distances[$currentCity][$i];

        //Early cut
        if($nextRouteDistance < $bestRoute["distance"])
        {
          //pass arrays by copy
          $this->permute($route, $visited, $i, $nextRouteDistance, $bestRoute);
        }
      }
    }
  }
}
