<?php
/**
 * Renderer for map fields: map, osm. These fields are not cloneable.
 */

namespace MBViews\Fields;

use RWMB_Field;

class MapRenderer extends BaseRenderer {
	public static function get_single_value( $value ) {
		// Groups send location, normal fields send array of map info.
		if ( ! is_array( $value ) ) {
			list( $latitude, $longitude, $zoom ) = explode( ',', $value . ',,' );
			$value                               = compact( 'latitude', 'longitude', 'zoom' );
		}

		$field = array_merge( self::$field, [
			'api_key' => '',
		] );

		return array_merge( $value, [
			'rendered' => RWMB_Field::call( $field, 'render_map', implode( ',', $value ), [
				'language' => $field['language'],
				'region'   => $field['region'],
				'api_key'  => $field['api_key'],
			] ),
		] );
	}
}
