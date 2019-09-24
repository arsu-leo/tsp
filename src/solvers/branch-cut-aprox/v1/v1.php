<?php

require_once(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'ReversePriorityQueue.php');
require_once(__DIR__ . DIRECTORY_SEPARATOR . 'TravelFactory.php');

/**
 * I have realized there is no way I can make a problem with factorial complexity grow properly on a home computer
 * WIth +16 cities I have problems already
 * And on worst case scenario, branch cut can almost run all permutations until finding the best solution
 * I have been struggling to make an exact solution and the point is that I end up with no solution
 * So I look for aproximation ideas, Nearest Neighbour (NN) is one of the most known but complexity is quite lower and results may not be as close as i would like
 * E(n + n -1), for n = 32 -> we get to 528 iterations
 *
 * Counting some numbers, possibly I can make 32!/24! iterations as it's a reasonable value not further than 15!
 * So now I explore solutions that reduces the amount of branches trying to lose as least as possible
 * First idea is just to limit the creation of branches by a static number (l), but with high n, l is still useless
 */

class BranchCutAproxTravelerSolverV1
{
  public function __construct(array $distances, $branchLimit = 3)
  {
    $this->distances      = $this->setupInputDistance($distances);
    $this->count          = count($this->distances);
    $this->travelFactory  = new TravelFactoryBCAV1();
    $this->cut            = INF;
    $this->branchLimit    = $branchLimit;
  }

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

    // Setup self distance is INF
    for($i = 0; $i < count($distances); $i++)
    {
      $distances[$i][$i] = INF;
    }
    return $distances;
  }

	public function solve() : array
	{
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

      // Here we will store the current iteration results
      $branchQueue = new ReversePriorityQueue();

      // branching starts here
      // for each possible travel, we dont need to accound for j = 1, n - 1 and n - 2 as there is only one solution of cost 0
			for ($j = 1; $j < $count; $j++)
			{
				if ($current->distances[$currentCity][$j] !== INF)
				{
          // Create the next possible min path
          $next = $this->travelFactory->create($current->route, $j, $current->distances, $current->weight, $current->distances[$currentCity][$j]);

          // If we have equal or better current result than our best final result we want to keep working on that branch
          if($next->weight <= $this->cut)
          {
            //echo implode(' ', $next->route) . ' ' . $next->weight . PHP_EOL;
            // If this is one possible final result
            // Keep in mind that the last 2 jumps have 0 cost so we can disregard them
            if($next->travels == $count - 1)
            {
              // Setup the cut as we have a working solution
              $this->cut = $next->weight;
            }
            //$queue->insert($next, $next->weight);
            $branchQueue->insert($next, $next->weight);
          }
				}
      }

      for($i = 0; $i < $this->branchLimit && $branchQueue->valid(); $i++)
      {
        $next = $branchQueue->extract();
        $queue->insert($next, $next->weight);
      }
    }
    // If we reach this point means there is no solution found to this problem!
    throw new Exception("Solution not found");
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
