<?php

namespace Web\Types;

/**
 * String class
 */

class Str
{
    /**
     * Determine if a given string contains a given substring.
     *
     * @param  string  $haystack
     * @param  string|string[]  $needles
     * @return bool
     */
    public static function contains($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if ($needle !== '' && mb_strpos($haystack, $needle) !== false) {
                return true;
            }
        }

        return false;
    }
    
    public static function escape($string)
    {
        return trim(filter_var($string, FILTER_SANITIZE_STRING));
    }

    public static function strip($string, $tags = '')
    {
        return strip_tags($string, $tags);
    }

    public static function serialize($input)
    {
        return serialize($input);
    }

    public static function unserialize($input)
    {
        return unserialize($input);
    }

    public static function jsonEncode($input)
    {
        return json_encode($input);
    }

    public static function jsonDecode($json)
    {
        return json_decode($json);
    }

    // This function expects the input to be UTF-8 encoded.
    public static function slug($string, $replace = array(), $delimiter = '-')
    {
        if (!empty($replace)) {
            $string = str_replace((array) $replace, ' ', $string);
        }

        return preg_replace('/[^A-Za-z0-9-]+/', $delimiter, $string);
    }

     /**
     * Generate a more truly "random" alpha-numeric string.
     *
     * @param  int  $length
     * @return string
     */
    public static function random($length = 16)
    {
        $string = '';

        while (($len = strlen($string)) < $length) {
            $size = $length - $len;

            $bytes = random_bytes($size);

            $string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
        }

        return $string;
    }

    public static function uuid($name = null, $version = 4)
    {
        if($version === 4) {
            return self::uuid_v4();
        } else {
            return self::uuid_v5($name);
        }
    }

    public static function uuid_v4()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand( 0, 0xffff ),
            mt_rand( 0, 0xffff ),
            mt_rand( 0, 0xffff ),
            mt_rand( 0, 0x0fff ) | 0x4000,
            mt_rand( 0, 0x3fff ) | 0x8000,
            mt_rand( 0, 0xffff ),
            mt_rand( 0, 0xffff ),
            mt_rand( 0, 0xffff )
        );
    }

    
    /**
	 * RFC 4122 compliant UUID version 5.
     * 
     * The RFC 4122 specification defines a Uniform Resource Name namespace for
     * UUIDs (Universally Unique IDentifier), also known as GUIDs (Globally
     * Unique IDentifier).  A UUID is 128 bits long, and requires no central
     * registration process.
     * 
     * NameSpace_DNS: {6ba7b810-9dad-11d1-80b4-00c04fd430c8}
     * NameSpace_URL: {6ba7b811-9dad-11d1-80b4-00c04fd430c8}
     * NameSpace_OID: {6ba7b812-9dad-11d1-80b4-00c04fd430c8}
     * NameSpace_X500:{6ba7b814-9dad-11d1-80b4-00c04fd430c8}
	 *
	 * @param  string $name    The name to generate the UUID from.
	 * @param  string $ns_uuid Namespace UUID. Default is for the NS when name string is a URL.
	 * @return string The UUID string.
	 */
	public static function uuid_v5( $name, $ns_uuid = '6ba7b811-9dad-11d1-80b4-00c04fd430c8' ) {

		// Compute the hash of the name space ID concatenated with the name.
		$hash = sha1( $ns_uuid . $name );

		// Intialize the octets with the 16 first octets of the hash, and adjust specific bits later.
		$octets = str_split( substr( $hash, 0, 16 ), 1 );

		/*
		 * Set version to 0101 (UUID version 5).
		 *
		 * Set the four most significant bits (bits 12 through 15) of the
		 * time_hi_and_version field to the appropriate 4-bit version number
		 * from Section 4.1.3.
		 *
		 * That is 0101 for version 5.
		 * time_hi_and_version is octets 6–7
		 */
		$octets[6] = chr( ord( $octets[6] ) & 0x0f | 0x50 );

		/*
		 * Set the UUID variant to the one defined by RFC 4122, according to RFC 4122 section 4.1.1.
		 *
		 * Set the two most significant bits (bits 6 and 7) of the
		 * clock_seq_hi_and_reserved to zero and one, respectively.
		 *
		 * clock_seq_hi_and_reserved is octet 8
		 */
		$octets[8] = chr( ord( $octets[8] ) & 0x3f | 0x80 );

		// Hex encode the octets for string representation.
		$octets = array_map( 'bin2hex', $octets );

		// Return the octets in the format specified by the ABNF in RFC 4122 section 3.
		return vsprintf( '%s%s-%s-%s-%s-%s%s%s', str_split( implode( '', $octets ), 4 ) );
	}

}