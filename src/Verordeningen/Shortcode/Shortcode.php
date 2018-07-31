<?php

namespace OWC\PDC\Verordeningen\Shortcode;

class Shortcode
{

	/**
	 * Default fields for verordeningen.
	 *
	 * @var array
	 */
	protected $defaults = [
		'_pdc-verordeningen-active-date' => null,
		'_pdc-verordeningen-price'       => null,
		'_pdc-verordeningen-new-price'   => null,
	];

	/**
	 * Add the shortcode rendering.
	 *
	 * @param $attributes
	 *
	 * @return string
	 */
	public function addShortcode($attributes)
	{

		$attributes = shortcode_atts([
			'id' => 0
		], $attributes);

		if ( ! isset($attributes['id']) OR empty($attributes['id']) OR ( count($attributes['id']) < 1 ) ) {
			return false;
		}

		if ( ! $this->postExists($attributes['id']) ) {
			return false;
		}

		$id         = absint($attributes['id']);
		$metaData   = $this->mergeWithDefaults(get_metadata('post', $id));
		$price      = $metaData['_pdc-verordeningen-price'];
		$newPrice   = $metaData['_pdc-verordeningen-new-price'];
		$dateActive = $metaData['_pdc-verordeningen-active-date'];

		if ( $this->hasDate($dateActive) AND $this->dateIsNow($dateActive) ) {
			$price = $newPrice;
		}

		$format = apply_filters('owc/pdc/verordeningen/shortcode/format', '<span>&euro; %d</span>');
		$output = sprintf($format, $price);
		$output = apply_filters('owc/pdc/verordeningen/shortcode/after-format', $output);

		return $output;
	}

	/**
	 * Determines if a post, identified by the specified ID, exist
	 * within the WordPress database.
	 *
	 * @param    int $id The ID of the post to check
	 *
	 * @return   bool          True if the post exists; otherwise, false.
	 */
	protected function postExists($id)
	{
		return get_post_status($id);
	}

	/**
	 * @param $metaData
	 *
	 * @return array
	 */
	private function mergeWithDefaults($metaData)
	{
		$output = [];
		foreach ( $metaData as $key => $data ) {

			if ( ! in_array($key, array_keys($this->defaults)) ) {
				continue;
			}

			$output[ $key ] = ( ! is_array($data) ) ? $data : $data[0];
		}

		return $output;
	}

	/**
	 * Readable check if date is not empty.
	 *
	 * @param $dateActive
	 *
	 * @return bool
	 */
	private function hasDate($dateActive)
	{
		return ! empty($dateActive);
	}

	/**
	 * Return true if date from Verorveningen is smaller or equal to current date.
	 *
	 * @param $dateActive
	 *
	 * @return bool
	 */
	private function dateIsNow($dateActive)
	{
		return ( new \DateTime($dateActive) <= new \DateTime('now') );
	}

}
