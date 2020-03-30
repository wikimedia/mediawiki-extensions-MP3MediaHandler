<?php

# Stream MP3 using HTML5 <audio> tag

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
