<?php

require_once(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'TravelerSolver.php');

/**
 * Still DFS preorder
 * Now we try to find a first distance, if the next calculation is over the best we found, it's pointless to continue
 *
 * This solver works better, the 12 cities list is solved in 0.3 seconds
 */
class BacktrackCutTravelerSolverV1 extends TravelerSolver
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
    $bestDist = INF;
    $bestRoute = $this->permute([0], 0, 0, $bestDist);
    if($bestRoute === false)
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
  protected function permute(array $route, int $currentCity,  float $distance, float &$bestDistance)
  {

    // Route length is equal to num of distances
    // num of distanes is equal to num of cities
    if(count($route) === count($this->distances))
    {
      // If we get here possilby this is allways true?
      if($distance < $bestDistance)
      {
        $bestDistance = $distance;
      }
      return array(
        "route"     => $route,
        "distance"  => $distance
      );
    }

    $bestRoute = false;

    // Loop through all the indexes
    // Keep the best result
    // Skip going deeper if result is worst than best one found
    for($i = 1; $i < count($this->distances); $i++)
    {
      // Not permuted yet
      if(!in_array($i, $route))
      {
        // php array copy by value
        $nextRoute          = $route;
        // Add this city
        $nextRoute[]        = $i;
        $nextRouteDistance  = $distance + $this->distances[$currentCity][$i];

        //Early cut
        if($nextRouteDistance < $bestDistance)
        {
          $bestRouteOnPath    = $this->permute($nextRoute, $i, $nextRouteDistance, $bestDistance);
          // Compare routes and just keep the best one
          $bestRoute = $this->getBestRoute($bestRoute, $bestRouteOnPath);
        }
      }
    }
    return $bestRoute;
  }

  private function getBestRoute($r1, $r2)
  {
    // Return the lowest not false
    return $r1 && $r2
      ? $r2["distance"] < $r1["distance"]
        ? $r2
        : $r1
      : $r2
        ? $r2
        : $r1;
  }
}
