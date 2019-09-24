<?php

require_once(__DIR__ . DIRECTORY_SEPARATOR . 'Travel.php');

/**
 * Class to create a branch which takes care of minimization and setup of a branch
 */
class TravelFactoryBCAV2
{
  /**
   * @param  array $route Route previously done not including $newCity
   * @param  int $newCity New city to travel to
   * @param  array $distances Previous weight matix
   * @param  float $previousWeight Previous weight
   * @param  float $jumpWeight Weight of this jump
   *
   * @return TravelBCAV2 A branch node
   */
  public function create(array $route, int $newCity, array $distances, float $previousWeight, float $jumpWeight) : TravelBCAV2
  {
    // We dont change the distances graph if we have not traveled
    if(count($route))
    {
      $previousCity = $route[count($route) - 1];

      // Kill the distances on the origin and destination city
      for($i = 0; $i < count($distances); $i++)
      {
        $distances[$previousCity][$i] = INF;
        $distances[$i][$newCity]      = INF;
      }
    }

    // Kill Distance to city Beijing as we don't want to account for it
    $distances[$newCity][0] = INF;


    // Now we add the common minimum cost while reducing the distances graph
    // Distances has been modified after this call
    $calculatedReduction = $this->calculateWeightMinimizing($distances);

    // Weight of a travel is the sum of:
    // previous travel weight
    // current travel weight (previous, new)
    // calculated reducion
    $weight = $previousWeight + $jumpWeight + $calculatedReduction;


    $travels = count($route);
    $route[] = $newCity;

    return new TravelBCAV2($route, $newCity, $travels, $distances, $weight);
  }


  /**
   * Minimize the distances matrix and return the minization cost
   * @param  array $distances Matrix to minimize
   *
   * @return float Minimization cost
   */
  private function calculateWeightMinimizing(array &$distances) : float
  {
    // Get the minimums while reducing the distances
    $rowMin     = $this->minRow($distances);
    $columnMin  = $this->minColumn($distances);

    $weight     = 0;

    // cost is sum of both minimizations is the weight
    for ($i = 0; $i < count($distances); $i++)
    {
      $weight += $rowMin[$i] !== INF
        ? $rowMin[$i]
        : 0;
      $weight += $columnMin[$i] !== INF
        ? $columnMin[$i]
        : 0;
		}

		return $weight;
  }

  /**
   * @param  array $distances Matrix to extract the row minimization
   *
   * @return array Row minimization cost
   */
  protected function minRow(array &$distances) : array
	{
		// initialize row array to INF
		$min = array_fill(0, count($distances), INF);

		// min[i] contains minimum in row i
    for ($i = 0; $i < count($distances); $i++)
    {
      for ($j = 0; $j < count($distances); $j++)
      {
        if ($distances[$i][$j] < $min[$i])
        {
					$min[$i] = $distances[$i][$j];
        }
      }
    }

		// substract on each row the minimum found for itself
    for ($i = 0; $i < count($distances); $i++)
    {
      for ($j = 0; $j < count($distances); $j++)
      {
        if ($distances[$i][$j] !== INF && $min[$i] !== INF)
        {
					$distances[$i][$j] -= $min[$i];
        }
      }
    }

    return $min;
	}

  /**
   * @param  array $distances Matrix to extract the column minimization
   *
   * @return array Column minimization cost
   */
	protected function minColumn(array &$distances) : array
	{
		// initialize row array to INF
		$min = array_fill(0, count($distances), INF);

		// min[i] contains minimum in column i
    for ($i = 0; $i < count($distances); $i++)
    {
      for ($j = 0; $j < count($distances); $j++)
      {
        if ($distances[$i][$j] < $min[$j])
        {
					$min[$j] = $distances[$i][$j];
        }
      }
    }

		// substract on each column the minimum found for itself
    for ($i = 0; $i < count($distances); $i++)
    {
      for ($j = 0; $j < count($distances); $j++)
      {
        if ($distances[$i][$j] !== INF && $min[$j] !== INF)
        {
          $distances[$i][$j] -= $min[$j];
        }
      }
    }

    return $min;
	}
}
