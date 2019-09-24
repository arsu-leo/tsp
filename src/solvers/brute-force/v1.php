<?php

require_once(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'TravelerSolver.php');

/**
 * For this solution, we do a permutation of all possible routes and then calculate the distances
 * following a DFS with preorder travesal
 * We don't try to do any kind of optimizations, the main idea for this solution is:
 * - get used to the data
 * - think about data possible structures
 * - come up with good solution when input is small
 * - verify basic asumptions about the problem and get ideas about possible optimizations
 * - be albe to output possible data results to verify the result
 * computation complexity is !n where n is num of cities
 * takes an insane amount of memory (List of 12 cities gets over 4GB of memory!)
 * takes quite amount of time, could not run it even with 12 cities without crashing
 */
class BruteForceTravelerSolverV1 extends TravelerSolver
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
    $routes       = $this->permute([0]);
    $bestRoute    = $this->findBestRoute($routes);
    return $bestRoute;
  }

  /**
   * Performs a recursive iteration until the route is the size of the amount of cities
   * @return array containing a list of possible routes, each route is an array of integers
   */
  protected function permute(array $route) : array
  {
    // Route length is equal to num of distances
    // num of distanes is equal to num of cities
    if(count($route) === count($this->distances))
    {
      return [$route];
    }
    $routes = [];
    for($i = 1; $i < count($this->distances); $i++)
    {
      // Not permuted yet
      if(!in_array($i, $route))
      {
        // php array copy by value
        $nextRoute    = $route;
        $nextRoute[]  = $i; // Add this city
        $newRoutes    = $this->permute($nextRoute);
        $routes       = array_merge($routes, $newRoutes);
      }
    }
    return $routes;
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

  protected function findBestRoute(array $routes) : array
  {
    $solutions    = array(
      array(
        "route"     => $routes[0],
        "distance"  => $this->calculateRouteDistance($routes[0])
      )
    );
    $bestSolution = 0;
    $bestDistance = $solutions[0]["distance"];

    // create a list of solutions and while marking down the best one
    for($i = 1; $i < count($routes); $i++)
    {
      $route = $routes[$i];
      $distance = $this->calculateRouteDistance($route);
      $solution = array(
        "route"     => $route,
        "distance"  => $distance
      );
      $solutions[] = $solution;
      if($distance < $bestDistance)
      {
        $bestDistance = $distance;
        $bestSolution = $i;
      }
    }

    // ToDo: Remove before summission
    echo "Brute force v1: Solution count: " . count($solutions) . PHP_EOL;
    return $solutions[$bestSolution];
  }

}
