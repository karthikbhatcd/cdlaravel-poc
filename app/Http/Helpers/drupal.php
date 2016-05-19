<?php

use Cd\Drupal;

/**
 * Get drupal session data
 * 
 * @return array
 */
function drupal_session(){
	$session = false;
	foreach ($_COOKIE as $key => $value) {
		if (stripos($key, 'SESS') !== false && strlen($key) > 34 ) {
			$session = [ 'name' => $key, 'id' => $value ];
		}
	}
	return $session;
}

/**
 * Calculates a base-64 encoded, URL-safe sha-256 hmac.
 *
 * @param string $data
 *   String to be validated with the hmac.
 * @param string $key
 *   A secret string key.
 *
 * @return string
 *   A base-64 encoded sha-256 hmac, with + replaced with -, / with _ and
 *   any = padding characters removed.
 */
function drupal_hmac_base64($data, $key) {
	// Casting $data and $key to strings here is necessary to avoid empty string
	// results of the hash function if they are not scalar values. As this
	// function is used in security-critical contexts like token validation it is
	// important that it never returns an empty string.
	$hmac = base64_encode(hash_hmac('sha256', (string) $data, (string) $key, TRUE));
	// Modify the hmac so it's safe to use in URLs.
	return strtr($hmac, array('+' => '-', '/' => '_', '=' => ''));
}

/**
 * Ensures the private key variable used to generate tokens is set.
 *
 * @return
 *   The private key.
 */
function drupal_get_private_key() {
	$key = Drupal::table('variable')->where('name', 'drupal_private_key')->first();
	return unserialize($key->value);
}

/**
 * Gets a salt useful for hardening against SQL injection.
 *
 * @return
 *   A salt based on information in settings.php, not in the database.
 */
function drupal_get_hash_salt() {
	$drupal_hash_salt	= Config::get('app.drupal_hash_salt');
	$databases			= Config::get('app.drupal_databases');

	// If the $drupal_hash_salt variable is empty, a hash of the serialized
	// database credentials is used as a fallback salt.
	return empty($drupal_hash_salt) ? hash('sha256', serialize($databases)) : $drupal_hash_salt;
}

function drupal_get_token($value = '') {
	return drupal_hmac_base64($value, drupal_session()['id'] . drupal_get_private_key() . drupal_get_hash_salt());
}

/**
 * Determine if drupal session and input CSRF token match
 * 
 * @param  \Illuminate\Http\Request  $request
 * @return bool
 */
function drupalTokensMatch( $request ){
	$token = $request->header('X-CSRF-TOKEN');
	return ( $token === drupal_get_token('services') );
}
