<?php
namespace SavannahCrest\Core;

defined( 'ABSPATH' ) || exit;

if ( 'explore' === ( $attributes['action'] ?? '' ) ) {
	echo action_link( catalogue_url(), __( 'Explore safaris', 'scs-core' ), 'scs-button scs-button--outline' );
} else {
	echo action_link( planner_url(), __( 'Plan your safari', 'scs-core' ), 'scs-button', 'page' );
}
