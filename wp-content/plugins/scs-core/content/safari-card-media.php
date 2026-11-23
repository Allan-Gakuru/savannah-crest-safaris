<?php
namespace SavannahCrest\Core;

defined( 'ABSPATH' ) || exit;

// Use these alternates while the original cover is still assigned to the itinerary.
return array(
	'4-day-samburu-national-reserve-safari'                 => array( 'cover' => 85, 'attachment' => 86 ),
	'3-day-classic-kruger-national-park-safari'              => array( 'cover' => 59, 'attachment' => 61 ),
	'4-day-rwanda-gorilla-and-golden-monkey-trekking-safari' => array( 'cover' => 68, 'attachment' => 70 ),
	'3-day-amboseli-private-escapade'                        => array(
		'cover' => 24,
		'asset' => 'amboseli-elephants',
		'alt'   => __( 'Elephant herd beneath Mount Kilimanjaro in Amboseli National Park', 'scs-core' ),
	),
	'5-day-masai-mara-nakuru-naivasha-private-safari'        => array(
		'cover' => 25,
		'asset' => 'lake-naivasha-hippo',
		'alt'   => __( 'Hippopotamus grazing on the shore of Lake Naivasha, Kenya', 'scs-core' ),
	),
);
