<?php

require_once(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'TravelerSolver.php');

/**
 * Still DFS preorder
 * Now im not sure where to optimize anymore, so i try to do small changes praying for a better result on the 14 cities
 * But im really struggling right now
 */
class BacktrackCutTravelerSolverV3 extends TravelerSolver
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
    $this->permute([0], 0, 0, $bestRoute);
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
  protected function permute(array $route, int $currentCity,  float $accDistance, array &$bestRoute)
  {
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

    // Loop through all the indexes
    // Keep the best result
    // Skip going deeper if result is worst than best one found
    for($i = 1; $i < $this->count; $i++)
    {
      // Not permuted yet
      if(!in_array($i, $route))
      {
        // php array copy by value
        $nextRoute          = $route;
        // Add this city
        $nextRoute[]        = $i;
        $nextRouteDistance  = $accDistance + $this->distances[$currentCity][$i];

        //Early cut
        if($nextRouteDistance < $bestRoute["distance"])
        {
          $this->permute($nextRoute, $i, $nextRouteDistance, $bestRoute);
        }
      }
    }
  }
}
