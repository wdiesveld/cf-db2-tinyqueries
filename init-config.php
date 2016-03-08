<?php
/**
 * Setup script TinyQueries
 *
 * This script is invoked during setup of the server by the script .extensions/tinyqueries/extensions.py 
 *
 * @author wouter@tinyqueries.com
 */
 
require_once( dirname(__FILE__) . '/libs/TinyQueries/SetupCloudFoundry.php' );

/**
 * Runs the setup for CloudFoundry
 *
 */
try
{
	TinyQueries\SetupCloudFoundry::run();
}
catch (Exception $e)
{
	echo "Error during setup TinyQueries: " . $e->getMessage() . "\n";
	exit(1);
}

echo "TinyQueries setup complete\n";
exit(0);

