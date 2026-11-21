<?php
namespace SavannahCrest\Core;

defined( 'ABSPATH' ) || exit;

$experience = 'experience' === ( $attributes['type'] ?? '' );
$taxonomy = $experience ? EXPERIENCE : DESTINATION;
$terms = ready_terms( $taxonomy, $experience ? array() : array( 'parent' => 0 ) );
if ( $terms ) {
	echo '<section class="scs-shell scs-section">';
	term_items( $terms );
	echo '</section>';
}
