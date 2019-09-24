<?php

require_once(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'TravelerSolver.php');

/**
 * Still DFS preorder
 * On this approach we accumulate the distance when on each recursion to remove the computation at the end of each branch
 * This way we avoid calculating almost the same sums out of each branch
 *
 * This solver works better than the previsous one, can work with the 12 cities list without memory crash but takes sligthly under 2 minutes
 */
class BruteForceTravelerSolverV3 extends TravelerSolver
{
  /**
   * __construct
   *
   * @param  array $distances: 2d array of distances where [i,j] is the distance between city on index i and city on index j
   */
  public function __construct(array $distances)
  {
    parent::__construct($distances);
  }

  /**
   * Calculate a solution
   *
   * @return array with the solution having keys [route, distance] where route is an int array with the indexes of cities and distance is a float with that route distance
   * @abstract
   */
  public function solve() : array
  {
    $bestRoute = $this->permute([0], 0, 0);
    return $bestRoute;
  }


  /**
   * Performs a recursive iteration until the route is the size of the amount of cities
   * @return array containing the best result for the possible routes possible from the input
   */
  protected function permute(array $route, int $currentCity,  float $distance) : array
  {
    // Route length is equal to num of distances
    // num of distanes is equal to num of cities
    if(count($route) === count($this->distances))
    {
      return array(
        "route"     => $route,
        "distance"  => $distance
      );
    }

    $bestRoute = false;

    for($i = 1; $i < count($this->distances); $i++)
    {
      // Not permuted yet
      if(!in_array($i, $route))
      {
        // php array copy by value
        $nextRoute        = $route;
        // Add this city
        $nextRoute[]        = $i;
        $nextRouteDistance  = $this->distances[$currentCity][$i];
        $bestRouteOnPath    = $this->permute($nextRoute, $i, $distance + $nextRouteDistance);
        // Compare routes and just keep the best one
        $bestRoute          = $bestRoute
          ? $this->getBestRoute($bestRoute, $bestRouteOnPath)
          : $bestRouteOnPath;
      }
    }
    return $bestRoute;
  }

  private function getBestRoute($r1, $r2)
  {
    return $r2["distance"] < $r1["distance"]
      ? $r2
      : $r1;
  }
}
