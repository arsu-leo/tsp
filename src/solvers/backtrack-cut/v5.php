<?php

require_once(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'TravelerSolver.php');

/**
 * Here we move solution as a class property
 */
class BacktrackCutTravelerSolverV5 extends TravelerSolver
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

  private function isSolved()
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
    $visited = array_fill(0, $this->count, false);
    $this->permute([], $visited, 0, 0);
    if(!$this->isSolved())
    {
      $err = "Unexpected no solution found";
      throw new Error($err);
    }
    return $this->bestRoute;
  }

  /**
   * Performs a recursive iteration until the route is the size of the amount of cities
   * @return array containing the best result for the possible routes possible from the input
   */
  protected function permute(array $route, array $visited, int $currentCity,  float $accDistance)
  {
    // Arrays are passed by value, so we use that do avoid reassigning multiple times unnecesarily
    $route[] = $currentCity;

    // Route length is equal to num of distances
    // num of distanes is equal to num of cities
    if(count($route) === $this->count)
    {
      if($accDistance < $this->bestRoute["distance"])
      {
        $this->bestRoute["route"]     = $route;
        $this->bestRoute["distance"]  = $accDistance;
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
        if($nextRouteDistance < $this->bestRoute["distance"])
        {
          //pass arrays by copy
          $this->permute($route, $visited, $i, $nextRouteDistance);
        }
      }
    }
  }
}
