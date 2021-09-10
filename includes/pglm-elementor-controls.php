<?php

/**
 * Register load more display controls.
 *
 * @param null $obj
 */
function pglm_register_load_more_controls( $obj ) {

	$obj->start_controls_section(
		'section_load_more',
		[
			'label' => __( 'Load More', 'jet-woo-builder' ),
		]
	);

	$obj->add_control(
		'enable_load_more',
		[
			'label'              => __( 'Enable Load More', 'jet-woo-builder' ),
			'type'               => Elementor\Controls_Manager::SWITCHER,
			'frontend_available' => true,
		]
	);

	$obj->add_control(
		'load_more_type',
		[
			'label'              => __( 'Load More Type', 'jet-woo-builder' ),
			'type'               => Elementor\Controls_Manager::SELECT,
			'default'            => 'click',
			'options'            => [
				'click'  => __( 'By Button Click', 'jet-woo-builder' ),
				'scroll' => __( 'By Page Scrolling', 'jet-woo-builder' ),
			],
			'frontend_available' => true,
			'condition'          => [
				'enable_load_more' => 'yes',
			],
		]
	);

	$obj->add_control(
		'load_more_trigger_id',
		[
			'label'              => __( 'Load More Trigger ID', 'jet-woo-builder' ),
			'type'               => Elementor\Controls_Manager::TEXT,
			'frontend_available' => true,
			'condition'          => [
				'enable_load_more' => 'yes',
				'load_more_type'   => 'click',
			],
		]
	);

	$obj->end_controls_section();

}