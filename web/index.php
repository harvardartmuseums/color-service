<?php

require(__DIR__.'/../vendor/autoload.php');
require_once(__DIR__.'/../lib/colors.inc.php');

use Symfony\Component\HttpFoundation\Request;

date_default_timezone_set('America/New_York');

$app = new Silex\Application();
$app['debug'] = true;

// Register the monolog logging service
$app->register(new Silex\Provider\MonologServiceProvider(), array(
  'monolog.logfile' => 'php://stderr',
));

// Our web handlers
$app->get('/', function() use($app) {
  $app['monolog']->addDebug('logging output.');
  return $app->json(array("status"=>"ok"), 201);
});

$app->get('/extract', function(Request $request) use($app) {
	$app['monolog']->addDebug('logging output.');
	
	$delta = 25;
	$reduce_brightness = true;
	$reduce_gradients = true;
	$num_results = 10;

	$image_URL = $request->get("image_url");

	// Make sure the parameter value is a valid URL
	if (!filter_var($image_URL, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED) === false) {
		$ex = new GetMostCommonColors();  

		$colors = $ex->Get_Color($image_URL, 
							$num_results, 
							$reduce_brightness, 
							$reduce_gradients, 
							$delta);  
		
		$color_list = "";
		$percentage_list= "";
		$output = array();

		foreach ( $colors as $hex => $percentage ) {
			if ( $percentage > 0 ) {
				$color_list = $color_list . "#" . $hex . ", ";
				$percentage_list = $percentage_list . $percentage . ", ";

				$c = array();
				$c["color"] = "#" . $hex;
				$c["percent"] = $percentage;
				$c["hue"] = $ex->Get_Hue($hex);
				$c["css3"] = "#" . $ex->Get_CSS3($hex);
				$c["spectrum"] = "#" . $ex->Get_Spectrum($hex);

				$output[] = $c;
			}
		}	

		return $app->json(array("colors"=>$output), 201);
	} else {
		return $app->json(array("status"=>"not ok"), 400);
	}
});

$app->run();
