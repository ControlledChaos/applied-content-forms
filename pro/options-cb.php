<?php
if ( isset( $args->fallback_cb ) && $args->fallback_cb && is_callable( $args->fallback_cb ) ) {
	return call_user_func( $args->fallback_cb, (array) $args );
}