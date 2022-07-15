<?php
namespace MBViews\Fields\File;

use MBViews\Fields\BaseRenderer;
use RWMB_File_Field;

class Renderer extends BaseRenderer {
	public static function get_single_value( $value ) {
		// Groups send ID, normal fields send array of file info.
		$value = isset( $value['ID'] ) ? $value['ID'] : $value;
		return RWMB_File_Field::file_info( $value, null, self::$field );
	}
}
