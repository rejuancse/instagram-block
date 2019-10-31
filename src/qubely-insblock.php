<?php
function qubelyinsblock_register_block() {

	// Only load if Gutenberg is available.
	if ( ! function_exists( 'register_block_type' ) ) {
		return;
	}

	register_block_type('qubelyinsblock/block-qubely-insblock', array(
		'render_callback' => 'qubelyinsblock_render_callback',
			'attributes' => array(
					'numberCols' => array(
						'type' 		=> 'number',
						'default'	=> '4' // nb: a default is needed!
					),
					'token' => array(
							'type' 		=> 'string',
							'default' => ''
					),
					'hasEqualImages' => array(
						'type' 		=> 'boolean',
						'default' => false
					),
					'numberImages' => array(
						'type' 		=> 'number',
						'default' => 4
					),
					'gridGap' => array(
						'type' 		=> 'number',
						'default'	=> 0
					),
					'showProfile'	=> array(
						'type'		=> 'boolean',
						'default'	=> false
					),
					
			)
		)
	);
}
add_action('init', 'qubelyinsblock_register_block');


function qubelyinsblock_fetchData($url) {
	$request = wp_remote_get( $url );
	if(is_wp_error( $request )) {
		return false;
	}
	return wp_remote_retrieve_body( $request );
}

function qubelyinsblock_add_to_cache( $result, $suffix = '' ) {
	$expire = 6 * 60 * 60; // 6 hours in seconds
	set_transient( 'qubelyinsblock-api_'.$suffix, $result, '', $expire );
}

function qubelyinsblock_get_from_cache( $suffix = '' ) {
	return get_transient( 'qubelyinsblock-api_'.$suffix );
}

function qubelyinsblock_render_callback( array $attributes ){
	$attributes = wp_parse_args(
		$attributes,
		[
			'token'           => '',
			'hasEqualImages'  => false,
			'numberImages'    => 4,
			'gridGap'         => 0,
			'showProfile'     => false,
			'className'       => '',
		]
	);
	$token          = $attributes[ 'token' ]  ;
	$hasEqualImages = $attributes[ 'hasEqualImages' ] ? 'has-equal-images' : '';
	$numberImages   = $attributes[ 'numberImages' ];
	$numberCols     = $attributes[ 'numberCols' ];
	$gridGap        = $attributes[ 'gridGap' ];
	$showProfile    = $attributes[ 'showProfile' ];
	$user 			= substr($token, 0, stripos($token, '.'));
	$suffix 		= $user.'_'.$numberImages;

	if ( !qubelyinsblock_get_from_cache() ) {
		$result = json_decode(qubelyinsblock_fetchData("https://api.instagram.com/v1/users/self/media/recent/?access_token={$token}&count={$numberImages}"));
		if($showProfile) {
			$result->profile = json_decode(qubelyinsblock_fetchData("https://api.instagram.com/v1/users/self?access_token={$token}"));
		}
		qubelyinsblock_add_to_cache( $result, $suffix );
	} else {
		$result = qubelyinsblock_get_from_cache( $suffix );
	}
	$thumbs 	= $result->data;
    $profile = $profileContainer = '';
    $output = '';

	$output .= '<div class="qubely-insblock-for-gutenberg '.$attributes['className'].'">';
    $output .= '<div class="qubely-insblock-row">';
    
        if($showProfile) {
            $profile 	= $result->profile->data;
            $output .= '<a href="https://instagram.com/'.$profile->username.'" target="_blank" class="qubelyinsblock-profile-container display-grid">';
                $output .= '<div class="qubelyinsblock-profile-picture-container">';
                    $output .= '<img class="qubelyinsblock-profile-picture" src="'.esc_attr($profile->profile_picture).'" alt="'.esc_attr($profile->full_name).'"/>';
                $output .= '</div>';
                $output .= '<div class="qubelyinsblock-bio-container">';
                    $output .= '<h3>'.$profile->username.'</h3>';
                    $output .= '<p>'.$profile->bio.'</p>';
                $output .= '</div>';
            $output .= '</a>';
        }

        if( is_array($thumbs) ) {
            foreach( $thumbs as $thumb ) {
                $image = esc_attr($thumb->images->standard_resolution->url);
                $output .= '<div class="instagram-image qubely-col-'.esc_attr($numberCols).'">';
                    $output .= '<a class="qubely-insblock-image-wrapper '.$hasEqualImages.'" href="'.esc_attr($thumb->link).'" target="_blank">';
                        $output .= '<img class="qubely-instagram-image" key="'.esc_attr($thumb->id).'" src="'.$image.'" />';
                    $output .= '</a>';
                $output .= '</div>';
            }
        }
    $output .= '</div>';
    $output .= '</div>';

	return $output;
}
