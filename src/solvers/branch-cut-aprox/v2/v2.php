<?php

require_once(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'ReversePriorityQueue.php');
require_once(__DIR__ . DIRECTORY_SEPARATOR . 'TravelFactory.php');

/**
 * We jump too much backwards and we don't really care anymore if we don't get the best exact solution.
 * Now we are trying to get something "good enough", so we limit the route length difference of the branches on the queue
 */
class BranchCutAproxTravelerSolverV2
{
  /**
   * @param  array $distances Original distances matrix
   * @param  int $branchLimit Amount of branches to generate out of a list of possible paths
   * @param  int $branchDeepLimit Max difference allowed between the branch with the longest route and the one with the shortest.
   * If the route length is too small compared to the longest route available, we will skip it
   */
  public function __construct(array $distances, int $branchLimit = 3, int $branchDeepLimit = 3)
  {
    $this->distances      = $this->setupInputDistance($distances);
    $this->count          = count($this->distances);
    $this->travelFactory  = new TravelFactoryBCAV2();
    $this->branchLimit    = $branchLimit;
    $this->branchDeepLimit= $branchDeepLimit;
  }


  /**
   * This function transforms the input distances to be able to be used by this solver
   * This is a special case of the Travel Salesman, to solve it we add 2 cities to distances list.
   * The first one "middlewareCity" can be reached by everyone at cost 0 but you can't go anywhere from there
   * The other one "finalCity" can only be reached by "middlewareCity" and it connects to city 0
   * Finally, no one should be albe to reach city 0
   * @param  array $distances Matrix of original distances between cities
   *
   * @return array Matrix of cost
   */
  protected function setupInputDistance(array $distances) : array
  {
    //Add city of distance 0 to everyone, one way path, let's call it middlewareCity
    for($i = 0; $i < count($distances); $i++)
    {
      // Distance to middlewareCity is 0
      $distances[$i][] = 0;
    }
    // distance from middlewareCity to anyone else is INF, there is no path to reach them
    $distances[] = array_fill(0, count($distances) + 1, INF);

    // Add city that no one can go to, let's call it finalCity
    for($i = 0; $i < count($distances) - 1; $i++)
    {
      // No one can travel to finalCity
      $distances[$i][] = INF;
    }
    // Only middlewareCity can go to finalCity with distance 0
    $distances[count($distances) - 1][] = 0;

    // Add row for finalCity, you can't go anywhere except Beijing at distance 0
    $distances[] = array_fill(0, count($distances) + 1, INF);
    $distances[count($distances) - 1][0] = 0;

    // Setup self distance as INF
    for($i = 0; $i < count($distances); $i++)
    {
      $distances[$i][$i] = INF;
    }
    return $distances;
  }

	/**
	 *
	 * @param  int $branchLimit Amount of branches to generate out of a list of possible paths, this param allows you to overide the default one with the same name
   * @param  int $branchDeepLimit Max difference allowed between the branch with the longest route and the one with the shortest.
   * If the route length is too small compared to the longest route available, we will skip it, this param allows you to overide the default one with the same name
	 *
	 * @return array with keys "distance" and "route" for the found solution
	 */
	public function solve(int $branchLimit = null, int $branchDeepLimit = null) : array
	{
    $branchLimit      = $branchLimit      ? $branchLimit      : $this->branchLimit;
    $branchDeepLimit  = $branchDeepLimit  ? $branchDeepLimit  : $this->branchDeepLimit;
    // Optimization props
    $cut          = INF;
    $deepestLevel = 0;
		// Queue to setup priorities of different possible branches
		$queue = new ReversePriorityQueue();
		// create initial branch and calculate it's weight
    // In this case, we start from Beijing
    $first = $this->travelFactory->create([], 0, $this->distances, 0, 0);

		// Add the first to the queue of possible results
		$queue->insert($first, $first->weight);

    // We want to skip the last 2 cities
    $count = $this->count - 2;
		while($queue->valid())
		{
			// Get next
			$current = $queue->extract();

			// if all cities are visited (We dont account for middlewareCity and finalCity)
			if ($current->travels == $count - 1)
			{
        $route = $current->route;
        return array('route' => $route, 'distance' => $this->calculateRouteDistance($route));
			}

      $currentCity = $current->city;

      // If only want to explore this item if it's not too far behind the best heuristical selected paths we have already explored
      // Otherwise, we would not want to go on this one as it creates a very big expansion tree and we are trying to avoid so
      if($current->travels >= $deepestLevel - $branchDeepLimit)
      {
        // Here we will store the current iteration results
        $branchQueue = new ReversePriorityQueue();

        // branching starts here
        // for each possible travel, we dont need to accound for j = 1, n - 1 and n - 2 as there is only one solution of cost 0
        for ($j = 1; $j < $count; $j++)
        {
          if ($current->distances[$currentCity][$j] !== INF)
          {
            // Create the next possible min branch
            $next = $this->travelFactory->create($current->route, $j, $current->distances, $current->weight, $current->distances[$currentCity][$j]);

            // If we have equal or better current result than our best final result we want to keep working on that branch
            if($next->weight <= $cut)
            {
              //echo implode(' ', $next->route) . ' ' . $next->weight . PHP_EOL;
              // If this is one possible final result
              // Keep in mind that the last 2 jumps have 0 cost so we can disregard them
              if($next->travels == $count - 1)
              {
                // Setup the cut as we have a working solution
                $cut = $next->weight;
              }
              //$queue->insert($next, $next->weight);
              $branchQueue->insert($next, $next->weight);
            }
          }
        }

        // If we created branches to go
        if($branchQueue->valid())
        {
          // We note down a new deepest level
          if($current->travels > $deepestLevel)
          {
            $deepestLevel = $current->travels + 1;
          }
          for($i = 0; $i < $branchLimit && $branchQueue->valid(); $i++)
          {
            $next = $branchQueue->extract();
            $queue->insert($next, $next->weight);
          }
        }
      }
    }

    // If we reach this point means there is no solution found to this problem!
    throw new Exception("Solution not found");
  }

  /**
   * Get the distance of a given route
   * @param  array $route Route to calculate the distance for
   *
   * @return float Distance of a given route
   */
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
