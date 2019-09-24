<?php

require_once(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'TravelerSolver.php');

/**
 * Still DFS preorder
 * On this approach we calculate the distance when on the final recursion level
 * This way we avoid creating a huhe memory space with all the possible routes
 * we create and discard them just on the branch we are using a route
 * On this iteration of development, I start feeling the need of a class with functionality for results
 * Just decided to create a protected function for now
 * This solver works better than the previsous one, can work with the 12 without memory crash but takes over 2.5 minutes
 */
class BruteForceTravelerSolverV2 extends TravelerSolver
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
   * @return array with the solution having keys [route, distance] where route is an integer array with the indexes of cities and distance is a float with that route distance
   * @abstract
   */
  public function solve() : array
  {
    $bestRoute = $this->permute([0]);
    return $bestRoute;
  }


  /**
   * Performs a recursive iteration until the route is the size of the amount of cities
   * @return array containing the best result for the possible routes possible from the input
   */
  protected function permute(array $route) : array
  {
    // Route length is equal to num of distances
    // num of distanes is equal to num of cities
    if(count($route) === count($this->distances))
    {
      return array(
        "route"     => $route,
        "distance"  => $this->calculateRouteDistance($route)
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
        $nextRoute[]      = $i;
        $bestRouteOnPath  = $this->permute($nextRoute);
        // Compare routes and just keep the best one
        $bestRoute        = $bestRoute
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

  protected function calculateRouteDistance(array $route) : float
  {
    $total = 0;
    for($i = 0; $i < count($route) - 1; ++$i)
    {
      $city1 = $route[$i];
      $city2 = $route[$i + 1];
      $total += $this->distances[$city1][$city2];
    }
    return $total;
  }
}
