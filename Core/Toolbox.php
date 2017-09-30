<?php

namespace Hedo\Core;

use \Datetime;

/**
 * The toolbox...
 *
 * @version 0.0.1
 * @package Core
 * @author  Clément Cazaud <opportus@gmail.com>
 *
 * @todo Implement more tools...
 */
class Toolbox
{
	/**
	 * @var object $config
	 */
	protected $config;

	/**
	 * Constructor.
	 *
	 * @param object $config
	 */
	public function __construct(Config $config)
	{
		$this->init($config);
	}

	/**
	 * Initializes the toolbox.
	 *
	 * @param object $config
	 */
	protected function init(Config $config)
	{
		$this->config = $config;
	}

	/**
	 * Escapes HTML.
	 *
	 * @param  string $string
	 * @return string
	 */
	public function escHtml($string)
	{
		return filter_var($string, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
	}

	/**
	 * Sanitizes strings.
	 *
	 * @param  string $string
	 * @return string
	 */
	public function sanitizeString($string)
	{
		return filter_var($string, FILTER_SANITIZE_STRING);
	}

	/**
	 * Sanitizes SQL query strings.
	 *
	 * @param  string $key
	 * @return string $key
	 */
	public function sanitizeKey($key)
	{
		$key = strtolower($key);
		$key = preg_replace('/[^a-z0-9_*\-]/', '', $key);	

		return $key;
	}

	/**
	 * Sanitizes SQL query comparison operators.
	 *
	 * @param  string $operator
	 * @return string $operator
	 */
	public function sanitizeOperator($operator)
	{
		$operatorWl = array(
			'>',
			'<',
			'<>',
			'=',
			'>=',
			'<=',
			'<=>',
			'!=',
			'IS',
			'IS NULL',
			'IS NOT',
			'IS NOT NULL',
			'LIKE',
			'NOT LIKE',
		);

		if (! in_array( $operator, $operatorWl)) {
			$operator = '';
		}

		return $operator;
	}

	/**
	 * Sanitizes SQL query conditions.
	 *
	 * @param  string $condition
	 * @return string $condition
	 */
	public function sanitizeCondition($condition)
	{
		$conditionWl = array(
			'AND',
			'OR',
		);

		if (! in_array( $condition, $conditionWl)) {
			$condition = '';
		}

		return $condition;
	}

	/**
	 * Sanitizes integers.
	 *
	 * @param  int $int
	 * @return int $int
	 */
	public function sanitizeInt($int)
	{
		return (int) $int;
	}

	/**
	 * Sanitizes email.
	 *
	 * @param  string $email
	 * @return string
	 */
	public function sanitizeEmail($email)
	{
		return filter_var($email, FILTER_SANITIZE_EMAIL);
	}

	/**
	 * Sanitizes url.
	 *
	 * @param  string $url
	 * @return string
	 */
	public function sanitizeUrl($url)
	{
		return filter_var($url, FILTER_SANITIZE_URL);
	}

	/**
	 * Formats/Sanitizes datetime.
	 *
	 * @param  string $datetime
	 * @param  string $format   Default: ''
	 * @param  string $type     Default: 'datetime' Possible values: 'datetime', 'date', 'time'
	 * @return string
	 */
	public function formatDatetime($datetime, $format = '', $type = 'datetime')
	{
		$datetimeFormatter = new DateTime($datetime);

		if ('' === $format) {
			if (extension_loaded('intl')) {
				switch ($type) {
					case 'datetime':
						$dateType = \IntlDateFormatter::FULL;
						$timeType = \IntlDateFormatter::MEDIUM;
						break;
					case 'date':
						$dateType = \IntlDateFormatter::FULL;
						$timeType = \IntlDateFormatter::NONE;
						break;
					case 'time':
						$dateType = \IntlDateFormatter::NONE;
						$timeType = \IntlDateFormatter::MEDIUM;
						break;
					default :
						$dateType = \IntlDateFormatter::FULL;
						$timeType = \IntlDateFormatter::MEDIUM;
						break;
				}

				$intlDatetimeFormatter = new \IntlDateFormatter($this->config->getApp('locale'), $dateType, $timeType);

				return ucwords($intlDatetimeFormatter->format($datetimeFormatter));

			} else {
				switch ($type) {
					case 'datetime':
						$format = $this->config->getApp('defaultDateFormat') . ' ' . $this->config->getApp('defaultTimeFormat');
						break;
					case 'date':
						$format = $this->config->getApp('defaultDateFormat');
						break;
					case 'time':
						$format = $this->config->getApp('defaultTimeFormat');
						break;
				}
			}
		}

		return $datetimeFormatter->format($format);
	}

	/**
	 * Validates string.
	 *
	 * @param  string $string
	 * @return bool
	 */
	public function validateString($string)
	{
		return $this->sanitizeString($string) === $string;
	}

	/**
	 * Validates key.
	 *
	 * @param  string $key
	 * @return bool
	 */
	public function validateKey($key)
	{
		return $this->sanitizeKey($key) === $key;
	}

	/**
	 * Validates integer.
	 *
	 * @param  int  $int
	 * @return bool
	 */
	public function validateInt($int)
	{
		return $this->sanitizeInt($int) === $int;
	}

	/**
	 * Validates email.
	 *
	 * @param  string $email
	 * @return bool
	 */
	public function validateEmail($email)
	{
		return filter_var($email, FILTER_VALIDATE_EMAIL);
	}

	/**
	 * Validates URL.
	 *
	 * @param  string $url
	 * @return bool
	 */
	public function validateUrl($url)
	{
		return filter_var($url, FILTER_VALIDATE_URL);
	}

	/**
	 * Validates datetime.
	 *
	 * @param  string $datetime
	 * @param  string $format
	 * @return bool
	 */
	public function validateDatetime($datetime, $format)
	{
		return $this->formatDatetime($datetime, $format) === $datetime;
	}

	/**
	 * Generates token.
	 *
	 * @param  string $salt Default: ''
	 * @param  string $key  Default: ''
	 * @param  string $algo Default: 'sha256'
	 * @return string
	 */
	public function generateToken($salt = '', $key = '', $algo = 'sha256')
	{
		$salt = $salt ? $salt : bin2hex(random_bytes(32));
		$key  = $key  ? $key  : $this->config->getApp('secret');

		return hash_hmac($algo, $salt, $key);
	}

	/**
	 * Checks token.
	 *
	 * @param  string knownToken
	 * @param  string userToken
	 * @return bool
	 */
	public function checkToken($knownToken, $userToken)
	{
		return hash_equals($knownToken, $userToken);
	}
}

