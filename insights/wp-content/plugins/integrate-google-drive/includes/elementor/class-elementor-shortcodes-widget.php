<?php

namespace IGD;

use Elementor\Controls_Manager;
use Elementor\Plugin;
use Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit();

class Shortcodes_Widget extends Widget_Base {

	public function get_name() {
		return 'igd_shortcodes';
	}

	public function get_title() {
		return __( 'Google Drive Modules', 'integrate-google-drive' );
	}

	public function get_icon() {
		return 'igd-shortcodes';
	}

	public function get_categories() {
		return [ 'integrate_google_drive' ];
	}

	public function get_keywords() {
		return [
			"google drive",
			"drive",
			"shortcode",
			"module",
			"cloud",
			"shortcode"
		];
	}

	public function get_script_depends() {
		return [ 'igd-frontend' ];
	}

	public function get_style_depends() {
		return [
			'igd-frontend',
		];
	}

	public function register_controls() {

		$this->start_controls_section( $this->get_name(),
			[
				'label' => __( 'Google Drive Modules', 'integrate-google-drive' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$shortcodes = Shortcode::get_shortcodes();

		$options = [
			'' => __( 'Select Module', 'integrate-google-drive' ),
		];

		if ( ! empty( $shortcodes ) ) {
			foreach ( $shortcodes as $shortcode ) {
				$options[ $shortcode['id'] ] = $shortcode['title'];
			}
		}

		$this->add_control( 'module_id',
			[
				'label'       => __( 'Select Module', 'integrate-google-drive' ),
				'type'        => Controls_Manager::SELECT2,
				'label_block' => true,
				'options'     => $options,
				'render_type' => 'template',
				'description' => __( 'Select the module you want to display.', 'integrate-google-drive' ),
			] );

		$this->add_control( 'show_preview', [
			'label'        => __( 'Show Preview', 'integrate-google-drive' ),
			'type'         => Controls_Manager::SWITCHER,
			'label_on'     => __( 'Show', 'integrate-google-drive' ),
			'label_off'    => __( 'Hide', 'integrate-google-drive' ),
			'return_value' => 'yes',
			'default'      => 'yes',
			'render_type'  => 'template',
			'description'  => __( 'Show preview of the selected module in the editor.', 'integrate-google-drive' ),
			'condition'    => [
				'module_id!' => '', // Show only when a shortcode is selected
			],
		] );

		// Edit button
		$this->add_control( 'edit_module', [
			'type'        => Controls_Manager::BUTTON,
			'text'        => '<i class="eicon eicon-settings"></i>' . __( 'Configure Module', 'integrate-google-drive' ),
			'event'       => 'igd:editor:edit_module',
			'separator'   => 'before',
			'classes'     => $this->get_name(),
			'description' => __( 'Configure or create a new module', 'integrate-google-drive' ),
		] );

		$this->end_controls_section();
	}

	public function render() {
		$settings  = $this->get_settings_for_display();
		$module_id = $settings['module_id'];

		$is_editor         = Plugin::$instance->editor->is_edit_mode();
		$shortcode_content = do_shortcode( '[integrate_google_drive id="' . $module_id . '"]' );

		$show_preview = $settings['show_preview'];

		if ( $is_editor ) {
			if ( $module_id && $show_preview ) {
				echo $shortcode_content;
			} else {
				Elementor::builder_empty_placeholder( $this->get_name(), $module_id );
			}
		} else {
			echo $shortcode_content;
		}

	}

}