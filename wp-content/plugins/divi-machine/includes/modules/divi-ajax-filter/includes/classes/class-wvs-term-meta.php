<?php

	defined( 'ABSPATH' ) or die( 'Keep Silent' );

	if ( ! class_exists( 'divifilter_Term_Meta' ) ):
		class divifilter_Term_Meta {

			private $taxonomy;
			private $post_type;
			private $fields = array();

			public function __construct( $taxonomy, $post_type, $fields = array() ) {

				$this->taxonomy  = $taxonomy;
				$this->post_type = $post_type;
				$this->fields    = $fields;

				// Category/term ordering
				// add_action( 'create_term', array( $this, 'create_term' ), 5, 3 );

				add_action( 'delete_term', array( $this, 'delete_term' ), 5, 4 );

				// Add form
				add_action( "{$this->taxonomy}_add_form_fields", array( $this, 'add' ) );
				add_action( "{$this->taxonomy}_edit_form_fields", array( $this, 'edit' ), 10 );
				add_action( "created_term", array( $this, 'save' ), 10, 3 );
				add_action( "edit_term", array( $this, 'save' ), 10, 3 );
				add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

				// Add columns
				add_filter( "manage_edit-{$this->taxonomy}_columns", array( $this, 'taxonomy_columns' ) );
				add_filter( "manage_{$this->taxonomy}_custom_column", array( $this, 'taxonomy_column' ), 10, 3 );

			}

			public function taxonomy_columns( $columns ) {
				$new_columns                       = array();
				// $new_columns[ 'cb' ]               = $columns[ 'cb' ];
				$new_columns[ 'wvs-meta-preview' ] = '';
				unset( $columns[ 'cb' ] );

				return array_merge( $new_columns, $columns );
			}

			public function taxonomy_column( $columns, $column, $term_id ) {

				$attribute       = bodycommerce_get_wc_attribute_taxonomy( $this->taxonomy );
				$fields          = bodycommerce_taxonomy_meta_fields( $attribute->attribute_type );
				$available_types = bodycommerce_available_attributes_types( $attribute->attribute_type );
				
				if ( isset( $available_types[ 'preview' ] ) && is_callable( $available_types[ 'preview' ] ) ) {
					call_user_func( $available_types[ 'preview' ], $term_id, $attribute, $fields );
				}
			}

			public function delete_term( $term_id, $tt_id, $taxonomy, $deleted_term ) {
				global $wpdb;

				$term_id = absint( $term_id );
				if ( $term_id and $taxonomy == $this->taxonomy ) {
					$wpdb->delete( $wpdb->termmeta, array( 'term_id' => $term_id ), array( '%d' ) );
				}
			}

			public function enqueue_scripts() {
				wp_enqueue_media();
				wp_enqueue_style( 'wp-color-picker' );
				wp_enqueue_script( 'wp-color-picker' );
			}

			public function save( $term_id, $tt_id = '', $taxonomy = '' ) {

				if ( $taxonomy == $this->taxonomy ) {
					foreach ( $this->fields as $field ) {
						foreach ( $_POST as $post_key => $post_value ) { // phpcs:ignore
							if ( $field[ 'id' ] == $post_key ) {
								switch ( $field[ 'type' ] ) {
									case 'text':
									case 'color':
										$post_value = esc_html( $post_value );
										break;
									case 'url':
										$post_value = esc_url( $post_value );
										break;
									case 'image':
										$post_value = esc_html( $post_value );
										break;
									case 'textarea':
										$post_value = esc_textarea( $post_value );
										break;
									case 'editor':
										$post_value = wp_kses_post( $post_value );
										break;
									case 'select':
									case 'select2':
										$post_value = sanitize_key( $post_value );
										break;
									default:
										do_action( 'bodycommerce_save_term_meta', $term_id, $field, $post_value, $taxonomy );
										break;
								}
								update_term_meta( $term_id, $field[ 'id' ], $post_value );
							}
						}
					}
					do_action( 'bodycommerce_after_term_meta_saved', $term_id, $taxonomy );
				}
			}

			public function add() {
				$this->generate_fields();
			}

			private function generate_fields( $term = FALSE ) {

				$screen = get_current_screen();

				if ( ( $screen->post_type == $this->post_type ) and ( $screen->taxonomy == $this->taxonomy ) ) {
					self::generate_form_fields( $this->fields, $term );
				}
			}

			public static function generate_form_fields( $fields, $term ) {

				$fields = apply_filters( 'bodycommerce_term_meta_fields', $fields, $term );

				if ( empty( $fields ) ) {
					return;
				}

				foreach ( $fields as $field ) {

					$field = apply_filters( 'bodycommerce_term_meta_field', $field, $term );

					$field[ 'id' ] = esc_html( $field[ 'id' ] );

					if ( ! $term ) {
						$field[ 'value' ] = isset( $field[ 'default' ] ) ? $field[ 'default' ] : '';
					} else {
						$field[ 'value' ] = get_term_meta( $term->term_id, $field[ 'id' ], TRUE );
					}

					$field[ 'size' ]        = isset( $field[ 'size' ] ) ? $field[ 'size' ] : '40';
					$field[ 'required' ]    = ( isset( $field[ 'required' ] ) and $field[ 'required' ] == TRUE ) ? ' aria-required="true"' : '';
					$field[ 'placeholder' ] = ( isset( $field[ 'placeholder' ] ) ) ? ' placeholder="' . $field[ 'placeholder' ] . '" data-placeholder="' . $field[ 'placeholder' ] . '"' : '';
					$field[ 'desc' ]        = ( isset( $field[ 'desc' ] ) ) ? $field[ 'desc' ] : '';

					$field[ 'dependency' ]       = ( isset( $field[ 'dependency' ] ) ) ? $field[ 'dependency' ] : array();

					self::field_start( $field, $term );
					switch ( $field[ 'type' ] ) {
						case 'text':
						case 'url':
							ob_start();
							?>
                            <input name="<?php echo esc_attr( $field[ 'id' ] ) ?>" id="<?php echo esc_attr( $field[ 'id' ] ) ?>"
                                   type="<?php echo esc_attr( $field[ 'type' ] ) ?>"
                                   value="<?php echo esc_attr( $field[ 'value' ] ) ?>"
                                   size="<?php echo esc_attr( $field[ 'size' ] ) ?>" <?php echo esc_attr( $field[ 'required' ] . $field[ 'placeholder' ] ) ?>>
							<?php
							echo ob_get_clean(); // phpcs:ignore
							break;
						case 'color':
							ob_start();
							?>
							<?php if ($field[ 'value' ] != ""){
								?>
									<div class="wvs-preview wvs-color-preview" style="margin-bottom: 20px;width: 80px;height: 80px;background-color:<?php echo esc_html( $field[ 'value' ] ) ?>;"></div>
								<?php }?>
			<input name="<?php echo esc_attr( $field[ 'id' ] ) ?>" id="<?php echo esc_attr( $field[ 'id' ] ) ?>" type="text" class="wvs-color-picker" value="<?php echo esc_html( $field[ 'value' ] ) ?>" data-default-color="<?php echo esc_html( $field[ 'value' ] ) ?>" size="<?php echo esc_attr( $field[ 'size' ] ) ?>" <?php echo esc_attr( $field[ 'required' ] . $field[ 'placeholder' ] ) ?>>
							<?php
							echo ob_get_clean(); // phpcs:ignore
							break;
						case 'textarea':
							ob_start();
							?>
    <textarea name="<?php echo esc_attr( $field[ 'id' ] ) ?>" id="<?php echo esc_attr( $field[ 'id' ] ) ?>" rows="5" cols="<?php echo esc_attr( $field[ 'size' ] ) ?>" <?php echo esc_attr( $field[ 'required' ] . $field[ 'placeholder' ] ) ?>><?php echo esc_html( $field[ 'value' ] ) ?></textarea>
							<?php
							echo ob_get_clean(); // phpcs:ignore
							break;
						case 'editor':
							$field[ 'settings' ] = isset( $field[ 'settings' ] )
								? $field[ 'settings' ]
								: array(
									'textarea_rows' => 8,
									'quicktags'     => FALSE,
									'media_buttons' => FALSE
								);
							ob_start();
							wp_editor( $field[ 'value' ], $field[ 'id' ], $field[ 'settings' ] );
							echo ob_get_clean(); // phpcs:ignore
							break;
						case 'select':
						case 'select2':

							$field[ 'options' ] = isset( $field[ 'options' ] ) ? $field[ 'options' ] : array();
							$field[ 'multiple' ] = isset( $field[ 'multiple' ] ) ? ' multiple="multiple"' : '';
							$css_class           = ( $field[ 'type' ] == 'select2' ) ? 'wvs-selectwoo' : '';

							ob_start();
							?>
                            <select name="<?php echo esc_attr( $field[ 'id' ] ) ?>" id="<?php echo esc_attr( $field[ 'id' ] ) ?>" class="<?php echo esc_attr( $css_class ) ?>" <?php echo esc_attr( $field[ 'multiple' ] ) ?>>
								<?php
									foreach ( $field[ 'options' ] as $key => $option ) {
										echo '<option' . selected( $field[ 'value' ], $key, FALSE ) . ' value="' . esc_attr( $key ) . '">' . esc_html( $option ) . '</option>';
									}
								?>
                            </select>
							<?php
							echo ob_get_clean(); // phpcs:ignore
							break;
							case 'image':
								ob_start();
								?>
								<?php if ($field[ 'value' ] != ""){
									?>
													<img style="padding-bottom:20px;width: 80px;" src="<?php echo esc_html( $field[ 'value' ] ) ?>">
														<?php }?>
	                            <input name="<?php echo esc_attr( $field[ 'id' ] ) ?>" id="<?php echo esc_attr( $field[ 'id' ] ) ?>" type="text" class="wvs-image-url" value="<?php echo esc_attr( $field[ 'value' ] ) ?>" data-default-color="<?php echo esc_html( $field[ 'value' ] ) ?>" size="<?php echo esc_attr( $field[ 'size' ] ) ?>" <?php echo esc_attr( $field[ 'required' ] ) . esc_attr( $field[ 'placeholder' ] ) ?>>


								<?php
								echo ob_get_clean(); // phpcs:ignore
								break;
						default:
							do_action( 'bodycommerce_term_meta_field', $field, $term );
							break;

					}
					self::field_end( $field, $term );

				}
			}

			private static function field_start( $field, $term ) {
				// Example:
				// http://emranahmed.github.io/Form-Field-Dependency/
				/*'dependency' => array(
					array( '#show_tooltip' => array( 'type' => 'equal', 'value' => 'yes' ) )
				)*/

				$depends = empty( $field[ 'dependency' ] ) ? '' : "data-depends='" . wp_json_encode( $field[ 'dependency' ] ) . "'";

				ob_start();
				if ( ! $term ) {
					?>
                    <div <?php echo esc_attr( $depends ) ?> class="form-field <?php echo esc_attr( $field[ 'id' ] ) ?> <?php echo empty( $field[ 'required' ] ) ? '' : 'form-required' ?>">
                    <label for="<?php echo esc_attr( $field[ 'id' ] ) ?>"><?php echo esc_html( $field[ 'label' ] ) ?></label>
					<?php
				} else {
					?>
                    <tr <?php echo esc_attr( $depends ) ?> class="form-field  <?php echo esc_attr( $field[ 'id' ] ) ?> <?php echo empty( $field[ 'required' ] ) ? '' : 'form-required' ?>">
                    <th scope="row"><label for="<?php echo esc_attr( $field[ 'id' ] ) ?>"><?php echo esc_html( $field[ 'label' ] ) ?></label></th>
                    <td>
					<?php
				}
				echo ob_get_clean(); // phpcs:ignore
			}

			private static function get_img_src( $thumbnail_id = FALSE ) {
				if ( ! empty( $thumbnail_id ) ) {
					$image = wp_get_attachment_thumb_url( $thumbnail_id );
				} else {
					$image = self::placeholder_img_src();
				}

				return $image;
			}

			private static function placeholder_img_src() {
				return woo_variation_swatches()->images_uri( 'placeholder.png' );
			}

			private static function field_end( $field, $term ) {

				ob_start();
				if ( ! $term ) {
					?>
                    <p><?php echo esc_html( $field[ 'desc' ] ) ?></p>
                    </div>
					<?php
				} else {
					?>
                    <p class="description"><?php echo esc_html( $field[ 'desc' ] ) ?></p></td>
                    </tr>
					<?php
				}
				echo ob_get_clean(); // phpcs:ignore
			}

			public function edit( $term ) {
				$this->generate_fields( $term );
			}
		}
	endif;
