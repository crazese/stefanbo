<?php
class delTag {
	function __destruct() {
		global $mc,$getLoginKey;
		$tag = MC.$getLoginKey;
		$mc->delete($tag);
	}
}