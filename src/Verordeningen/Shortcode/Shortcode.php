<?php
/**
 * Handles shortcode generation.
 */

namespace OWC\PDC\Verordeningen\Shortcode;

/**
 * Handles shortcode generation.
 */
class Shortcode
{

    /**
     * Default fields for verordeningen.
     *
     * @var array $defaults
     */
    protected $defaults = [
        '_pdc-verordening-active-date' => null,
        '_pdc-verordening-link'       => null,
        '_pdc-verordening-new-link'   => null,
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

        if (! isset($attributes['id']) or empty($attributes['id']) or ( count($attributes['id']) < 1 )) {
            return false;
        }

        if (! $this->postExists($attributes['id'])) {
            return false;
        }

        $id         = absint($attributes['id']);
        $metaData   = $this->mergeWithDefaults(get_metadata('post', $id));
        $link      = $metaData['_pdc-verordening-link'];
        $newlink   = $metaData['_pdc-verordening-new-link'];
        $dateActive = $metaData['_pdc-verordening-active-date'];

        if ($this->hasDate($dateActive) and $this->dateIsNow($dateActive)) {
            $link = $newlink;
        }

        $format = apply_filters('owc/pdc/verordeningen/shortcode/format', '<a href="%1$s" class="pdc-verordening-link" title="%2$s">%2$s</a>');
        $output = sprintf($format, $link, get_the_title($id));
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
     * Merges the settings with defaults, to always have proper settings.
     *
     * @param $metaData
     *
     * @return array
     */
    private function mergeWithDefaults($metaData)
    {
        $output = [];
        foreach ($metaData as $key => $data) {
            if (! in_array($key, array_keys($this->defaults))) {
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
