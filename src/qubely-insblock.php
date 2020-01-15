<?php
function insblock_register_block() {

	// Only load if Gutenberg is available.
	if ( ! function_exists( 'register_block_type' ) ) {
		return;
	}

	register_block_type('insblock/gutenberg-insblock', array(
		'render_callback' => 'insblock_render_callback',
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
add_action('init', 'insblock_register_block');


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

function insblock_render_callback( array $attributes ){
	$attributes = wp_parse_args(
		$attributes,
		[
			'token'           => '',
			'hasEqualImages'  => false,
			'numberImages'    => 4,
			'gridGap'         => 0,
			'showProfile'     => false,
			'class'       => '',
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

	$output .= '<div class="qubely-instagramfeed-wrap">';
		$output .= '<div class="qubely-instagramfeed-row">';
		
			if($showProfile) {
				$profile 	= $result->profile->data;

				$output .= '<div class="qubely-instagram-profile-bio-container">';
					$output .= '<div class="qubely-instagram-profile-image">';
						$output .= '<a href="https://instagram.com/'.$profile->username.'" target="_blank">';
							$output .= '<img class="instagram-profile-image" src="'.esc_attr($profile->profile_picture).'" alt="'.esc_attr($profile->full_name).'"/>';
						$output .= '</a>';
					$output .= '</div>';

					$output .= '<div class="qubely-instagram-profile-bio-info">';
						$output .= '<div class="qubely-instagram-bio">';
							$output .= '<a class="qubely-follow" href="https://instagram.com/'.$profile->username.'" target="_blank">';
								$output .= '<h1 class="qubely-instagram-username">'.$profile->username.'</h1>';
							$output .= '</a>';

							$output .= '<a class="qubely-follow" rel="nofollow" target="_blank" href="https://www.instagram.com/accounts/login/?next=%2F'.$profile->username.'%2Ffollowers%2F&source=followed_by_list">';
								$output .= '<button class="qubely-instagram-follow" type="button">Follow</button>';
							$output .= '</a>';

						$output .= '</div>';

						$output .= '<ul class="qubely-instagram-notifications">';
							$output .= '<li>';
								$output .= '<span class="qubely-instagram-post-count"><span class="qubely-post-number">'.$profile->counts->media.'</span> posts</span>';
							$output .= '</li>';
							$output .= '<li>';
								$output .= '<a class="qubely-followers" href="https://www.instagram.com/accounts/login/?next=%2F'.$profile->username.'%2F&source=followed_by_list" target="_blank">';
								$output .= '<span class="qubely-post-number" title="'.$profile->counts->follows.'">'.$profile->counts->follows.'</span> Followers</a>';
							$output .= '</li>';
							$output .= '<li>';
								$output .= '<a class="qubely-followers" href="https://www.instagram.com/accounts/login/?next=%2F'.$profile->username.'%2F&source=follows_list" target="_blank">';
								$output .= '<span class="qubely-post-number">'.$profile->counts->followed_by.'</span> Following</a>';
							$output .= '</li>';
						$output .= '</ul>';

						$output .= '<div class="qubely-instagram-profile-name">';
							$output .= '<span class="profile-name">'.$profile->full_name.'</span>';
							$output .= '<span class="profile-bio">'.$profile->bio.'</span>';
							$output .= '<span class="profile-bio">'.$profile->website.'</span>';
						$output .= '</div>';
					$output .= '</div>';
				$output .= '</div>';
			}

			if( is_array($thumbs) ) {
				foreach( $thumbs as $thumb ) {

					$image = esc_attr($thumb->images->standard_resolution->url);

					$output .= '<div class="qubely-instagram-image qubely-col-'.esc_attr($numberCols).' '.(($equalimagesize) ? 'equal-images' : '').'">';
						$output .= '<div class="qubely-instagram-image-wrap qubely-post-img-'.esc_attr($imageAnimation).'">';
							$output .= '<a class="qubely-insblock-image-wrapper '.$equalimagesize.'" href="'.esc_attr($thumb->link).'" target="_blank">';
								$output .= '<img key="'.esc_attr($thumb->id).'" src="'.$image.'" />';
								$output .= '<div class="qubely-image-overlay">';
									$output .= '<ul>';
										
										if ($showCount) {
											$output .= '<li class="qubely-listing">';
												$output .= '<span class="dashicons dashicons-heart"></span>';
												$output .= '<span class="qubely-count qubely-like-count">'.$thumb->likes->count.'</span>';
											$output .= '</li>';
											
											$output .= '<li class="qubely-listing">';
												$output .= '<span class="dashicons dashicons-admin-comments"></span>';
												$output .= '<span class="qubely-count qubely-comments-count">'.$thumb->comments->count.'</span>';
											$output .= '</li>';
										}
										
										if ($showCaption == true && $thumb->caption != null) {
											$output .= '<li class="qubely-caption">';
												$output .= '<p class="caption-title">'.$thumb->caption->text.'</p>';
											$output .= '</li>';
										}
										
									$output .= '</ul>';
								$output .= '</div>';
							$output .= '</a>';
						$output .= '</div>';
					$output .= '</div>';
				}
			}
		$output .= '</div>';
	$output .= '</div>';

	wp_reset_postdata();

	return $output;
}
