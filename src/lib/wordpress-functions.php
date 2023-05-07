<?php
function shortcode_atts( $pairs, $atts, $shortcode = '' ) {
	$atts = (array) $atts;
	$out  = array();
	foreach ( $pairs as $name => $default ) {
		if ( array_key_exists( $name, $atts ) ) {
			$out[ $name ] = $atts[ $name ];
		} else {
			$out[ $name ] = $default;
		}
	}

	if ( $shortcode ) {
		/**
		 * Filters shortcode attributes.
		 *
		 * If the third parameter of the shortcode_atts() function is present then this filter is available.
		 * The third parameter, $shortcode, is the name of the shortcode.
		 *
		 * @since 3.6.0
		 * @since 4.4.0 Added the `$shortcode` parameter.
		 *
		 * @param array  $out       The output array of shortcode attributes.
		 * @param array  $pairs     The supported attributes and their defaults.
		 * @param array  $atts      The user defined shortcode attributes.
		 * @param string $shortcode The shortcode name.
		 */
		$out = apply_filters( "shortcode_atts_{$shortcode}", $out, $pairs, $atts, $shortcode );
	}

	return $out;
}

function add_shortcode( $tag, $callback ) {
    global $shortcode_tags;

    if ( '' === trim( $tag ) ) {
		_doing_it_wrong(
			__FUNCTION__,
			__( 'Invalid shortcode name: Empty name given.' ),
			'4.4.0'
		);
		return;
	}

	if ( 0 !== preg_match( '@[<>&/\[\]\x00-\x20=]@', $tag ) ) {
		_doing_it_wrong(
			__FUNCTION__,
			sprintf(
				/* translators: 1: Shortcode name, 2: Space-separated list of reserved characters. */
				__( 'Invalid shortcode name: %1$s. Do not use spaces or reserved characters: %2$s' ),
				$tag,
				'& / < > [ ] ='
			),
			'4.4.0'
		);
		return;
	}

    $shortcode_tags[ $tag ] = $callback;
}

function evaluate_tag( $contentId, $page ) {
	$contentId = preg_quote($contentId);

	$tagRegex = '/<pre[^>]*id="'.$contentId.'">(.*?)<\\/pre>/si';
	$pageContent = file_get_contents($page);

	preg_match($tagRegex, $pageContent, $matches);
	
	if (isset($matches[1])) {
		$matchedContent = $matches[1];

		$shortcodeRegex = '/\[[^\]]*\]/si';
		preg_match($shortcodeRegex, $matchedContent, $matches);
		
		if (isset($matches[0])) {
			$shortcodeTagContent = $matches[0];
			$lenShortcodeTag = strlen($shortcodeTagContent);
			$tagArray = explode(" ", substr($shortcodeTagContent, 1, $lenShortcodeTag - 2));

			$shortcode = $tagArray[0];

			for ($i = 1; $i < count($tagArray); $i++) {
				preg_match('/(\w+)=\"(.*)\"/', $tagArray[$i], $matches);
				$attrs[$matches[1]] = $matches[2];
			}

			global $shortcode_tags;

			if (isset($shortcode_tags[$shortcode])) {
				$content = substr($matchedContent, $lenShortcodeTag, strlen($matchedContent) - ($lenShortcodeTag + strlen($shortcode) + 3)); //+3 => [/]
				echo $shortcode_tags[$shortcode]($attrs, $content);
			}
		}
	}
}