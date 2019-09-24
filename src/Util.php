<?php

/**
 * Class to just aggreegate most of miscellaneous functionalities
 * Must have created classes for almost each but is too overhead for the given problem
 * Additionaly there is no expectation of extension or any other modification
 */
class Util
{
  /**
   * readData Read the cities from a text file
   *
   * @param  string $filePath
   *
   * @return array of cities where each is represented through an array with keys [name, x, y]
   */
  public static function readCitiesFromPath(string $filePath = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . "cities.txt") : array
  {
    // get input data
    $fileContent = file_get_contents($filePath);
    if($fileContent === false)
    {
      $err = "File not found for path '{$filePath}'";
      throw new Exception("File not found");
    }

    $fileLines   = explode("\n", $fileContent);
    $cities      = [];

    foreach($fileLines as $line)
    {
      // Take care about windows /r being added silently even if it's not part of the specification input
      $line     = trim($line);
      $fields   = explode("\t", $line);
      $city     = array(
        "city"  => $fields[0],
        "x"     => (float)$fields[1],
        "y"     => (float)$fields[2]
      );
      $cities[] = $city;
    }

    return $cities;
  }

  /**
   * mapCitiesDistance
   *
   * @param  array $cities Array of cities on array format with keys [name, x, y]
   *
   * @return array 2d array where [i,j] is the computed distance between cities i and j
   *
   * we could have a class that handles cities distances and takes half space as would manage the structure the following way:
   * i = j then 0
   * i > j then exchange i and j values
   * return [i, j]
   * This way every comparison is done on a single direction
   * If the amount of cities would be extremly big, this optimization may be worth to the amount of constant memory used
   * by this data
   */
  public static function mapCitiesDistance(array $cities) : array
  {
    $count  = count($cities);
    $result = array_fill(0, $count, []);

    // for each city, calc distance with other cities
    for($i = 0; $i < $count; ++$i)
    {
      $city1 = $cities[$i];
      // 0 distance itself
      $result[$i][$i] = 0;

      // we calculate only one way distance, we don't want to calculate twice
      // so we only calculate the ones that have not been calculated already
      for($j = $i + 1; $j < $count; ++$j)
      {
        $city2          = $cities[$j];
        $distance       = self::distance($city1, $city2);
        $result[$i][$j] = $distance;
        $result[$j][$i] = $distance;
      }
    }
    return $result;
  }

  /**
   * Get distance between two points using distance formula
   * sqrt( pow(x1 - x2, 2) + pow(y1 - y2, 2))
   *
   * @param  array $p1 array with keys x, y
   * @param  array $p2 array with keys x, y
   *
   * @return float
   */
  protected static function distance(array $p1, array $p2) : float
  {
    $x    = $p1["x"] - $p2["x"];
    $y    = $p1["y"] - $p2["y"];
    $pow  = pow($x, 2) + pow($y, 2);
    $dist = sqrt($pow);

    return $dist;
  }


  /**
   * Map the city names of a given list of indexes
   *
   * @param  array $indexes List of integers representing the cities indexes
   * @param  array $cities List of cities
   *
   * @return array
   */
  public static function mapCitiesFromIndexes(array $indexes, array $cities) : array
  {
    $result = [];
    foreach ($indexes as $index)
    {
      $result[] = $cities[$index]["city"];
    }
    return $result;
  }


  /**
   * Write the list of cities through the std channel
   *
   * @param  array $citiesList List of string names
   *
   * @return void
   */
  public static function writeData(array $citiesList) : void
  {
    echo implode("\n", $citiesList);
  }
}
