<?php 
	/*	
	*	Goodlayers Script Inclusion File
	*	---------------------------------------------------------------------
	*	This file contains the script that helps you include the script to 
	* 	the location you want
	*	---------------------------------------------------------------------
	*/	

	// return the custom stylesheet path
	if( !function_exists('realfactory_get_style_custom') ){
		function realfactory_get_style_custom($local = false){

			$upload_dir = wp_upload_dir();
			$filename = '/rftr-style-custom.css';
			$local_file = $upload_dir['basedir'] . $filename;
			
			if( $local ){
				return $local_file;
			}else{
				if( file_exists($local_file) ){
					$filemtime = filemtime($local_file);

					if( is_ssl() ){
						$upload_dir['baseurl'] = str_replace('http://', 'https://', $upload_dir['baseurl']);
					}
					return $upload_dir['baseurl'] . $filename . '?' . $filemtime;
				}else{
					return get_template_directory_uri() . '/css/style-custom.css';
				}
			}
		}
	}

	// use to load theme style.css and necessary wordpress script on specific page
	add_action( 'wp_enqueue_scripts', 'realfactory_include_scripts' );
	if( !function_exists('realfactory_include_scripts') ){
		function realfactory_include_scripts(){
			
			// include site style
			wp_enqueue_style('realfactory-style-core', get_template_directory_uri() . '/css/style-core.css');
			if( is_child_theme() ){
				wp_enqueue_style('realfactory-child-theme-style', get_stylesheet_uri());
			}
			
			if( !is_customize_preview() ){
				wp_enqueue_style('realfactory-custom-style', realfactory_get_style_custom());
				wp_enqueue_style('ninja_style', get_template_directory_uri() . '../../../uploads/ninja.css');

				$upload_dir = wp_upload_dir();
				$modern_css = $upload_dir['basedir'] . '/integral-modern.css';
				$modern_js  = $upload_dir['basedir'] . '/integral-modern.js';
				$modern_ver = file_exists( $modern_css ) ? filemtime( $modern_css ) : '1.0.0';

				wp_enqueue_style(
					'integral-fonts',
					'https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Sora:wght@400;500;600;700&display=swap',
					array(),
					null
				);

				wp_enqueue_style(
					'integral-modern',
					$upload_dir['baseurl'] . '/integral-modern.css',
					array( 'ninja_style', 'integral-fonts' ),
					$modern_ver
				);

				if ( file_exists( $modern_js ) ) {
					wp_enqueue_script(
						'integral-modern',
						$upload_dir['baseurl'] . '/integral-modern.js',
						array( 'jquery' ),
						filemtime( $modern_js ),
						true
					);
				}
			}

			// include site script
			wp_enqueue_script('realfactory-script-core', get_template_directory_uri() . '/js/script-core.js', array('jquery', 'jquery-effects-core'), '1.0.0', true );
			wp_localize_script('realfactory-script-core', 'realfactory_script_core', array(
				'home_url' => home_url('/')
			));

			// wordpress comments script
			if( is_singular() && comments_open() && get_option('thread_comments') ){
				wp_enqueue_script( 'comment-reply' );
			}			
		
		}
	}
	
	// add action to add the necessary script to wp_head
	add_action( 'wp_head', 'realfactory_include_wp_head_script' );
	if( !function_exists('realfactory_include_wp_head_script') ){
		function realfactory_include_wp_head_script(){
?>
<!--[if lt IE 9]>
<script src="<?php echo esc_url(get_template_directory_uri()); ?>/js/html5.js"></script>
<![endif]-->
<?php
		}
	}
