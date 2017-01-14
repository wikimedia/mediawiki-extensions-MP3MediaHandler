<?php

# Stream MP3 using HTML5 <audio> tag

$wgMediaHandlers['audio/mp3'] = 'MP3MediaHandler';
$wgMediaHandlers['audio/mpeg'] = 'MP3MediaHandler';
$wgFileExtensions[] = 'mp3';

$wgExtensionCredits['parserhook'][] = array(
	'name' => 'MP3MediaHandler',
	'descriptionmsg' => 'mp3mediahandler-desc',
	'author' => "Mark Clements (HappyDog)",
	'version' => '1.0',
	'url' => 'https://www.mediawiki.org/wiki/Extension:MP3MediaHandler'
);

class MP3MediaHandler extends MediaHandler {

	function validateParam( $name, $value ) { return true; }
	function makeParamString( $params ) { return ''; }
	function parseParamString( $string ) { return array(); }
	function normaliseParams( $file, &$params ) { return true; }
	function getImageSize( $file, $path ) { return false; }

	# Prevent "no higher resolution" message.
	function mustRender( $file ) { return true; }
	function getParamMap() { return array(); }

	function doTransform ( $file, $dstPath, $dstUrl, $params, $flags = 0 ) {
		return new MP3OutputRenderer( $file->getFullUrl(), $file->getTitle() );
	}
}

class MP3OutputRenderer extends MediaTransformOutput {
	var $pSourceFileURL;

	function __construct( $SourceFileURL, $FileName ){
		$this->pSourceFileURL = $SourceFileURL;
		$this->pFileName = $FileName;
	}

	function toHtml( $options=array() ) {
		$Output = '<audio controls="controls">'
				. '<source src="$1" type="audio/mp3" />'
				. $this->getFlashPlayerHTMLTemplate( '<p><a href="$1">$2</a></p>',
													 $this->pSourceFileURL )
				. '</audio>';

		$Args = array(
					'$1'	=> $this->pSourceFileURL,
					'$2'	=> $this->pFileName,
				);


		return $this->expandHtml( $Output, $Args );
	}

	function getFlashPlayerHTMLTemplate( $NonFlashFallback, $SourceFileURL ) {
		global $wgFlashPlayerPath, $wgFlashPlayerURLParam, $wgFlashPlayerParams;
		global $wgFlashPlayerFlashVars, $wgFlashPlayerWidth, $wgFlashPlayerHeight;

		if ( isset( $wgFlashPlayerPath ) ) {
		// A common default parameter name for the audio file to be loaded is 'url',
		// so we default to this.  Individual implementations can over-ride via
		// LocalSettings.php, if necessary.
			if ( !isset( $wgFlashPlayerURLParam ) ) {
				$wgFlashPlayerURLParam = "url";
			}

		// Initialise the arrays that may be used to configure the player.
			if ( !is_array( $wgFlashPlayerParams ) ) {
				$wgFlashPlayerParams = array();
			}

			if ( !is_array( $wgFlashPlayerFlashVars ) ) {
				$wgFlashPlayerFlashVars = array();
			}

		// Add the required 'movie' param to the set of player parameters.
			$wgFlashPlayerParams['movie'] = $wgFlashPlayerPath;

		// Add the source file URL to the list of FlashVars arguments, and build them
		// into a single FlashVars parameter to be passed into the movie.
			$wgFlashPlayerFlashVars[$wgFlashPlayerURLParam] = $SourceFileURL;
			$wgFlashPlayerParams['FlashVars'] = wfArrayToCGI( $wgFlashPlayerFlashVars );

		// Create the parameter string from the parameters array.
			$Params = "";
			foreach ( $wgFlashPlayerParams as $Param => $Value ) {
				$Params .= '<param name="' . htmlspecialchars( $Param )
							  . '" value="' . htmlspecialchars( $Value ) . '">';
			}

		// Set FlashPlayer size, if specified.
			$Sizes = "";
			if ( isset( $wgFlashPlayerWidth ) ) {
				$Sizes .= ' width="' . htmlspecialchars($wgFlashPlayerWidth) . '"';
			}

			if ( isset( $wgFlashPlayerHeight ) ) {
				$Sizes .= ' height="' . htmlspecialchars($wgFlashPlayerHeight) . '"';
			}

		// Build the final HTML.
			$HTML = '<object data="' . htmlspecialchars( $wgFlashPlayerPath )
				  . '" type="application/x-shockwave-flash"' . $Sizes . '>'
				  . $Params
				  . $NonFlashFallback
				  . '</object>';

			return $HTML;
		}
		else {
			return $NonFlashFallback;
		}
	}

	function expandHtml( $HTML, $Args ) {
		foreach ( $Args as $Key => $Value ) {
			$Args[$Key] = htmlspecialchars( $Value );
		}

		return str_replace( array_keys( $Args ), array_values( $Args ), $HTML );
	}

}
