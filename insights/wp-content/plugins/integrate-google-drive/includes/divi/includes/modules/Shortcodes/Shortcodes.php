<?php

namespace IGD\Divi;

use IGD\Shortcode;

class Shortcodes extends \ET_Builder_Module {

	public $slug = 'igd_shortcodes';
	public $vb_support = 'on';

	protected $module_credits = array(
		'module_uri' => 'https://softlabbd.com/integrate-google-drive/',
		'author'     => 'SoftLab',
		'author_uri' => 'https://softlabbd.com/',
	);

	public function init() {
		$this->name = esc_html__( 'Google Drive Modules', 'integrate-google-drive' );


		$this->settings_modal_toggles = [
			'general' => [
				'toggles' => [
					'main_content' => 'Module Configuration',
				],
			],
		];

		$this->advanced_fields = [
			'background'     => false,
			'borders'        => false,
			'box_shadow'     => false,
			'button'         => false,
			'filters'        => false,
			'fonts'          => false,
			'margin_padding' => false,
			'text'           => false,
			'link_options'   => false,
			'height'         => false,
			'scroll_effects' => false,
			'animation'      => false,
			'transform'      => false,
		];
	}

	public function get_fields() {

		$shortcodes = Shortcode::get_shortcodes();

		if ( ! empty( $shortcodes ) ) {
			$shortcodes = array_column( $shortcodes, 'title', 'id' );
		}

		$options = [ '' => __( 'Select Shortcode', 'integrate-google-drive' ) ] + $shortcodes;

		$current_post_id = \ET_Builder_Element::get_current_post_id();

		return array(

			'id'           => array(
				'label'           => esc_html__( 'Google Drive Modules', 'integrate-google-drive' ),
				'type'            => 'igd_configure',
				'option_category' => 'configuration',
				'description'     => esc_html__( 'Select an existing Google Drive module or crate a new one.', 'integrate-google-drive' ),
				'toggle_slug'     => 'main_content',
				'default'         => '',
				'options'         => $options,
				'post_id'         => $current_post_id ,
			),

			// add a toggle switch to show or hide the shortcode
			'show_preview' => array(
				'label'           => esc_html__( 'Show Preview', 'integrate-google-drive' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'description'     => esc_html__( 'Show/ hide the shortcode preview in the builder.', 'integrate-google-drive' ),
				'toggle_slug'     => 'main_content',
				'default'         => 'on',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'integrate-google-drive' ),
					'off' => esc_html__( 'No', 'integrate-google-drive' ),
				),
			),

		);
	}

	public function render( $attrs, $content = null, $render_slug = null ) {

		$id = $this->props['id'];

		if ( ! $id ) {
			return;
		}

		return Shortcode::instance()->render_shortcode( [ 'id' => $id, ] );
	}
}

new Shortcodes;
