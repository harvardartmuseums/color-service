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

	// Read in and evalute the query string parameters
	$image_URL = $request->get("image_url");
	
	if ($request->get("color_count") > 0) {
		$num_results = $request->get("color_count");
	};
		
	if ($request->get("delta") > 0 && $request->get("delta") < 256) {
		$delta = $request->get("delta");
	};

	$brightness_param = $request->get("reduce_brightness");
	if (isset($brightness_param)) {
		$brightness_param = filter_var($brightness_param, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
		if (is_bool($brightness_param)) {
			$reduce_brightness = $brightness_param;
		}
	}

	$gradients_param = $request->get("reduce_gradients");
	if (isset($gradients_param)) {
		$gradients_param = filter_var($gradients_param, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
		if (is_bool($gradients_param)) {
			$reduce_gradients = $gradients_param;
		}
	}

	// Build the info block of data
	$info = array();
	$info["image_url"] = $image_URL;
	$info["color_count"] = (float)$num_results;
	$info["delta"] = (float)$delta;
	$info["reduce_brightness"] = $reduce_brightness;
	$info["reduce_gradients"] = $reduce_gradients;

 
	// Make sure the parameter value is a valid URL
	if (!filter_var($image_URL, FILTER_VALIDATE_URL) === false) {

		// Extract the colors
		$ex = new GetMostCommonColors();  
		$colors = $ex->Get_Color($image_URL, 
							$num_results, 
							$reduce_brightness, 
							$reduce_gradients, 
							$delta);  
		
		// Process the list of colors
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

		return $app->json(array("status"=>"ok","info"=>$info,"colors"=>$output), 201);
	} else {
		return $app->json(array("status"=>"not ok"), 400);
	}
});

$app->run();
