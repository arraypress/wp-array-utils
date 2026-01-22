<?php
/**
 * Array Helper Utilities
 *
 * Essential array manipulation utilities for WordPress development.
 * Focuses on the most commonly needed operations that speed up daily workflow.
 *
 * @package ArrayPress\ArrayUtils
 * @since   1.0.0
 * @author  ArrayPress
 * @license GPL-2.0-or-later
 */

declare( strict_types=1 );

namespace ArrayPress\ArrayUtils;

/**
 * Arr Class
 *
 * Core operations for working with arrays.
 */
class Arr {

	/** Selection Operations ******************************************************/

	/**
	 * Get the first element of an array.
	 *
	 * @param array $array The input array.
	 *
	 * @return mixed|null The first element or null if the array is empty.
	 */
	public static function first( array $array ) {
		return empty( $array ) ? null : reset( $array );
	}

	/**
	 * Get the last element of an array.
	 *
	 * @param array $array The input array.
	 *
	 * @return mixed|null The last element or null if the array is empty.
	 */
	public static function last( array $array ) {
		return empty( $array ) ? null : end( $array );
	}

	/**
	 * Filter an array by an array of allowed keys.
	 *
	 * @param array $array        The array to filter.
	 * @param array $allowed_keys The allowed keys.
	 *
	 * @return array The filtered array.
	 */
	public static function only( array $array, array $allowed_keys ): array {
		return array_intersect_key( $array, array_flip( $allowed_keys ) );
	}

	/**
	 * Exclude specified keys from an array.
	 *
	 * @param array $array The input array.
	 * @param array $keys  The keys to exclude.
	 *
	 * @return array The filtered array.
	 */
	public static function except( array $array, array $keys ): array {
		return array_diff_key( $array, array_flip( $keys ) );
	}

	/** Dot Notation Access *******************************************************/

	/**
	 * Get a value from an array using "dot" notation.
	 *
	 * @param array  $array   The array to retrieve from.
	 * @param string $path    The path to retrieve using dot notation.
	 * @param mixed  $default The default value to return if the path doesn't exist.
	 *
	 * @return mixed The retrieved value or default.
	 */
	public static function get( array $array, string $path, $default = null ) {
		if ( isset( $array[ $path ] ) ) {
			return $array[ $path ];
		}

		foreach ( explode( '.', $path ) as $segment ) {
			if ( ! is_array( $array ) || ! array_key_exists( $segment, $array ) ) {
				return $default;
			}
			$array = $array[ $segment ];
		}

		return $array;
	}

	/**
	 * Set a nested value in an array using "dot" notation.
	 *
	 * @param array  $array The array to modify.
	 * @param string $path  Path using dot notation.
	 * @param mixed  $value Value to set.
	 *
	 * @return array The modified array.
	 */
	public static function set( array $array, string $path, $value ): array {
		$keys    = explode( '.', $path );
		$current = &$array;

		foreach ( $keys as $key ) {
			if ( ! is_array( $current ) ) {
				$current = [];
			}
			$current = &$current[ $key ];
		}

		$current = $value;

		return $array;
	}

	/**
	 * Check if a nested key exists in an array using "dot" notation.
	 *
	 * @param array  $array The array to check.
	 * @param string $path  Path using dot notation.
	 *
	 * @return bool True if the nested key exists, false otherwise.
	 */
	public static function has( array $array, string $path ): bool {
		if ( isset( $array[ $path ] ) ) {
			return true;
		}

		$keys = explode( '.', $path );
		foreach ( $keys as $key ) {
			if ( ! is_array( $array ) || ! array_key_exists( $key, $array ) ) {
				return false;
			}
			$array = $array[ $key ];
		}

		return true;
	}

	/**
	 * Get a value from an array trying multiple keys in order.
	 *
	 * Supports dot notation for nested arrays.
	 *
	 * @param array $array   The array to search.
	 * @param array $keys    Keys to try in order (supports dot notation).
	 * @param mixed $default Default value if no key is found.
	 *
	 * @return mixed The first found value or default.
	 */
	public static function get_first( array $array, array $keys, $default = null ) {
		foreach ( $keys as $key ) {
			$value = self::get( $array, $key );
			if ( $value !== null ) {
				return $value;
			}
		}

		return $default;
	}

	/** Sorting Operations ********************************************************/

	/**
	 * Sort an array of numeric values and filter out any invalid values.
	 *
	 * @param array $array      The array to sort and filter.
	 * @param bool  $descending Whether to sort in descending order. Default false.
	 *
	 * @return array The sorted and filtered array.
	 */
	public static function sort_numeric( array $array, bool $descending = false ): array {
		$array = array_filter( array_map( 'absint', $array ) );

		if ( $descending ) {
			arsort( $array );
		} else {
			asort( $array );
		}

		return array_values( $array );
	}

	/**
	 * Sort an array alphabetically.
	 *
	 * @param array $array      The array to sort.
	 * @param bool  $descending Whether to sort in descending order. Default false.
	 * @param int   $flags      Sorting flags. Default is SORT_REGULAR.
	 *
	 * @return array The sorted array.
	 */
	public static function sort_alphabetic( array $array, bool $descending = false, int $flags = SORT_REGULAR ): array {
		if ( $descending ) {
			arsort( $array, $flags );
		} else {
			asort( $array, $flags );
		}

		return array_values( $array );
	}

	/**
	 * Sort an array by key.
	 *
	 * @param array $array      The array to sort.
	 * @param bool  $descending Whether to sort in descending order. Default false.
	 * @param int   $flags      Sorting flags. Default is SORT_REGULAR.
	 *
	 * @return array The sorted array.
	 */
	public static function sort_by_key( array $array, bool $descending = false, int $flags = SORT_REGULAR ): array {
		if ( $descending ) {
			krsort( $array, $flags );
		} else {
			ksort( $array, $flags );
		}

		return $array;
	}

	/**
	 * Sort a multidimensional array by a specific key.
	 *
	 * @param array  $array      The array to sort.
	 * @param string $key        The key to sort by.
	 * @param bool   $descending Whether to sort in descending order. Default false.
	 *
	 * @return array The sorted array.
	 */
	public static function sort_by_column( array $array, string $key, bool $descending = false ): array {
		$sort_flag = $descending ? SORT_DESC : SORT_ASC;
		array_multisort( array_column( $array, $key ), $sort_flag, $array );

		return $array;
	}

	/** Data Processing ************************************************************/

	/**
	 * Group an array of associative arrays by a specified key.
	 *
	 * @param array  $array The array of associative arrays to group.
	 * @param string $key   The key to group by.
	 *
	 * @return array The grouped array.
	 */
	public static function group_by( array $array, string $key ): array {
		$groups = [];
		foreach ( $array as $item ) {
			$value = $item[ $key ] ?? null;
			if ( ! isset( $groups[ $value ] ) ) {
				$groups[ $value ] = [];
			}
			$groups[ $value ][] = $item;
		}

		return $groups;
	}

	/**
	 * Pluck an array of values from an array.
	 *
	 * @param array  $array The array to pluck from.
	 * @param string $key   The key to pluck.
	 *
	 * @return array The plucked values.
	 */
	public static function pluck( array $array, string $key ): array {
		return array_map( function ( $item ) use ( $key ) {
			return is_object( $item ) ? $item->$key : $item[ $key ];
		}, $array );
	}

	/**
	 * Flatten a multidimensional array.
	 *
	 * @param array $array  The multidimensional array.
	 * @param bool  $unique Whether to return unique values. Default false.
	 *
	 * @return array The flattened array.
	 */
	public static function flatten( array $array, bool $unique = false ): array {
		$flat_array = [];
		array_walk_recursive( $array, function ( $item ) use ( &$flat_array ) {
			$flat_array[] = $item;
		} );

		return $unique ? array_unique( $flat_array ) : $flat_array;
	}

	/** Array Manipulation ********************************************************/

	/**
	 * Insert an element after a specific key in an array.
	 *
	 * @param array  $array The original array.
	 * @param string $key   The key to insert after.
	 * @param array  $new   The new element to insert.
	 *
	 * @return array The updated array.
	 */
	public static function insert_after( array $array, string $key, array $new ): array {
		$position = array_search( $key, array_keys( $array ) );

		if ( $position === false ) {
			$position = count( $array );
		} else {
			$position += 1;
		}

		return array_slice( $array, 0, $position, true ) +
		       $new +
		       array_slice( $array, $position, null, true );
	}

	/**
	 * Insert an element before a specific key in an array.
	 *
	 * @param array  $array The original array.
	 * @param string $key   The key to insert before.
	 * @param array  $new   The new element to insert.
	 *
	 * @return array The updated array.
	 */
	public static function insert_before( array $array, string $key, array $new ): array {
		$position = array_search( $key, array_keys( $array ) );

		if ( $position === false ) {
			$position = 0;
		}

		return array_slice( $array, 0, $position, true ) +
		       $new +
		       array_slice( $array, $position, null, true );
	}

	/**
	 * Shuffle an array.
	 *
	 * @param array $array The array to shuffle.
	 *
	 * @return array The shuffled array.
	 */
	public static function shuffle( array $array ): array {
		shuffle( $array );

		return $array;
	}

	/** Array Comparison ***********************************************************/

	/**
	 * Check if elements in array1 exist in array2 with configurable matching criteria.
	 *
	 * @param array $array1    The first array to compare.
	 * @param array $array2    The second array to compare against.
	 * @param bool  $match_all Whether to check if ALL elements match (true) or ANY elements match (false). Default
	 *                         true.
	 *
	 * @return bool True if the matching criteria is met, false otherwise.
	 */
	public static function has_matches( array $array1, array $array2, bool $match_all = true ): bool {
		$intersection = array_intersect( $array1, $array2 );

		if ( $match_all ) {
			return count( $array1 ) === count( $intersection );
		}

		return ! empty( $intersection );
	}

	/**
	 * Check if all elements in array1 exist in array2.
	 *
	 * @param array $array1 The first array to compare.
	 * @param array $array2 The second array to compare against.
	 *
	 * @return bool True if all elements in array1 exist in array2.
	 */
	public static function has_all_matches( array $array1, array $array2 ): bool {
		return count( $array1 ) === count( array_intersect( $array1, $array2 ) );
	}

	/**
	 * Check if any elements in array1 exist in array2.
	 *
	 * @param array $array1 The first array to compare.
	 * @param array $array2 The second array to compare against.
	 *
	 * @return bool True if any elements match between the arrays.
	 */
	public static function has_any_matches( array $array1, array $array2 ): bool {
		return ! empty( array_intersect( $array1, $array2 ) );
	}

	/** Conversion Operations *****************************************************/

	/**
	 * Convert an array to a delimited string.
	 *
	 * @param array  $array     The input array.
	 * @param string $delimiter The delimiter to use between array elements. Default is ','.
	 * @param string $wrapper   Optional wrapper for each element. Default is empty.
	 * @param bool   $trim      Whether to trim each element. Default is true.
	 *
	 * @return string The resulting delimited string.
	 */
	public static function to_string( array $array, string $delimiter = ',', string $wrapper = '', bool $trim = true ): string {
		$result = array_map( function ( $item ) use ( $wrapper, $trim ) {
			$item = $trim ? trim( $item ) : $item;

			return $wrapper . $item . $wrapper;
		}, $array );

		return implode( $delimiter, $result );
	}

	/** WordPress-Specific Helpers ************************************************/

	/**
	 * Convert a key-value array to options format (value/label pairs).
	 *
	 * Supports nested arrays when a label_key is provided.
	 *
	 * @param array  $array     The key-value array to convert.
	 * @param string $label_key The key to use for the label when values are arrays.
	 *
	 * @return array<array{value: string, label: string}> Array of value/label pairs for select fields.
	 */
	public static function to_options( array $array, string $label_key = '' ): array {
		$options = [];

		foreach ( $array as $key => $value ) {
			if ( is_array( $value ) && $label_key ) {
				$value = $value[ $label_key ] ?? $key;
			}
			$options[] = [
				'value' => (string) $key,
				'label' => (string) $value,
			];
		}

		return $options;
	}

	/**
	 * Convert options format (value/label pairs) to key-value array.
	 *
	 * @param array $options Array of value/label pairs from select fields.
	 *
	 * @return array The converted key-value array.
	 */
	public static function from_options( array $options ): array {
		$array = [];
		foreach ( $options as $option ) {
			if ( isset( $option['value'] ) && isset( $option['label'] ) ) {
				$array[ $option['value'] ] = $option['label'];
			}
		}

		return $array;
	}

	/**
	 * Check if a value exists in an array using fast O(1) lookup.
	 *
	 * @param array $array  The array to search.
	 * @param mixed $needle The value to search for.
	 *
	 * @return bool True if the value exists in the array.
	 */
	public static function contains( array $array, mixed $needle ): bool {
		return isset( array_flip( $array )[ $needle ] );
	}

}