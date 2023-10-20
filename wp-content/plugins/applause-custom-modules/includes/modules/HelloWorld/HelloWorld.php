<?php

class ACM_HelloWorld extends ET_Builder_Module {

	public $slug       = 'acm_hello_world';
	public $vb_support = 'on';

	protected $module_credits = array(
		'module_uri' => 'https://www.applause.com',
		'author'     => 'Tammy Shipps',
		'author_uri' => 'https://www.applause.com',
	);

	public function init() {
		$this->name = esc_html__( 'Hello World', 'acm-applause-custom-modules' );
	}

	public function get_fields() {
		return array(
			'content' => array(
				'label'           => esc_html__( 'Content', 'acm-applause-custom-modules' ),
				'type'            => 'tiny_mce',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Content entered here will appear inside the module.', 'acm-applause-custom-modules' ),
				'toggle_slug'     => 'main_content',
			),
		);
	}

	public function render( $attrs, $content = null, $render_slug ) {
		return sprintf( '<h1>%1$s</h1>', $this->props['content'] );
	}
}

new ACM_HelloWorld;
