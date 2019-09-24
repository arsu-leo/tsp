<?php

// requires
$srcPath = __DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR;
require_once($srcPath . 'Util.php');
// Best found solver
require_once($srcPath . 'solvers' . DIRECTORY_SEPARATOR . 'branch-cut-aprox' . DIRECTORY_SEPARATOR . 'v2' . DIRECTORY_SEPARATOR . 'v2.php');

// get a list of cities with positions
// Pre: first city is always Beijing
$cities         = Util::readCitiesFromPath();
// For smaller dataset testing, uncomment this line
//$cities = array_slice($cities, 0, 10);

$distanceGraph  = Util::mapCitiesDistance($cities);


// Run solver
$solver     = new BranchCutAproxTravelerSolverV2($distanceGraph);
$starttime  = microtime(true);

// In case of out of memory, change the params given at the end of the readme.md file to fit your computer spec
$solution   = $solver->solve(2, 6);

// Output data
$citiesBestRoute = Util::mapCitiesFromIndexes($solution["route"], $cities);
Util::writeData($citiesBestRoute);
