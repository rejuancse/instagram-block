const { Component, Fragment } = wp.element;
const { InspectorControls } = wp.editor;
const {
	PanelBody,
	RangeControl,
	TextControl,
	ToggleControl,
	Spinner,
} = wp.components;

const { __ } = wp.i18n;

export default class InstagramEdit extends Component {
	state = {
		loading: true,
		apiResponseCode: 200,
		apiErrorMessage: '',
	};

	componentDidMount() {
		this.fetchPhotos();
		this.fetchBio();
	}

	fetchPhotos( count, token ) {
		const _COUNT = count ? count : this.props.attributes.numberImages;
		const _TOKEN = token ? token : this.props.attributes.token;

		if ( ! _TOKEN ) {
			return false;
		}

		return fetch(
			`https://api.instagram.com/v1/users/self/media/recent/?access_token=${ _TOKEN }&count=${ _COUNT }`
		)

		.then( res => res.json() )
		.then( json => {
			console.log( json );

			if ( json.meta ) {
				this.setState( {
					apiResponseCode: json.meta.code,
					loading: false,
				} );

				if ( json.meta.code === 200 ) {
					this.props.setAttributes( {
						thumbs: json.data,
					} );
				} else {
					this.props.setAttributes( {
						thumbs: [],
					} );

					this.setState( {
						apiErrorMessage: json.meta.error_message,
					} );
				}
			}
		} );
	}

	fetchBio() {
		const _TOKEN = this.props.attributes.token;

		if ( ! _TOKEN ) {
			return false;
		}

		return fetch(
			`https://api.instagram.com/v1/users/self/?access_token=${ _TOKEN }`
		)
        .then( res => res.json() )
        .then( json => {
            if ( json.meta && json.meta.code === 200 ) {
                this.props.setAttributes( {
                    profile: json.data,
                } );
            } else {
                this.props.setAttributes( {
                    profile: [],
                } );
            }
        } );
	}

	onChangeToken = token => {
		this.props.setAttributes( {
			token,
		} );
		this.fetchPhotos( this.props.attributes.numberImages, token );
	};

	onChangeImages = numberImages => {
		this.props.setAttributes( {
			numberImages,
		} );
		this.fetchPhotos( numberImages );
	};

	onChangeShowProfile = showProfile => {
		this.props.setAttributes( {
			showProfile,
		} );
		this.fetchBio();
	};

	render() {
		const {
			attributes: {
				token,
				numberCols,
				numberImages,
				hasEqualImages,
				thumbs,
				gridGap,
				showProfile,
				profile,
			},
			className,
			setAttributes,
        } = this.props;
        
        const { apiResponseCode, apiErrorMessage, loading } = this.state;
        
        let instagramImagerender;
		let profileImage;
		
		if( profile != undefined ){
			console.log('InsProfile', profile.counts.follows);
		}

        profileImage = (showProfile) ? (
			((profile != undefined) ? (
				<div className="insblock-instagram-profile-bio-container">
					<div className="insblock-instagram-profile-image">
						<img className="instagram-profile-image" src={ profile.profile_picture } alt={ profile.full_name }/>
					</div>
					<div className="insblock-instagram-profile-bio-info">
						<div className="insblock-instagram-bio">
							<h1 className="insblock-instagram-username">{ profile.username }</h1>
						</div>
						<ul className="insblock-instagram-notifications">
							<li>
								<span className="insblock-instagram-post-count"><span className="insblock-post-number">{profile.counts.media}</span> posts</span>
							</li>
							<li>
								<a className="insblock-followers" href="#">
									<span className="insblock-followers-count" title={profile.counts.follows}>{profile.counts.follows}</span> followers</a>
								</li>
							<li>
								<a className="insblock-followers" href="#">
									<span className="insblock-followers-count">{profile.counts.followed_by}</span>following</a>
							</li>
						</ul>
						<div className="insblock-instagram-profile-name">
							<span className="profile-name">{ profile.full_name }</span>
							<span className="profile-bio">{ profile.bio }</span>
							<span className="profile-bio">{ profile.website }</span>
						</div>
					</div>
				</div>
			) : '')
        ) : '';


        if ( token && apiResponseCode === 200 ) {
            if ( loading ) {
                instagramImagerender = (
                    <p className={ className }>
                        <Spinner />
                        { __( 'Loading feed' ) }
                    </p>
                );
            } else {
                instagramImagerender = (
					
					<div className={`insblock-instagramfeed-wrap`}>
                        <div className="insblock-instagramfeed-row">

                            { profileImage }

                            { thumbs &&
                                thumbs.map( photo => {
                                    return (
                                        <div className={`insblock-instagram-image insblock-col-${numberCols} ${hasEqualImages ? 'has-equal-images' : ''}`} key={ photo.id } >  
											<div className={`insblock-instagram-image-wrap`}>
												<img src={ photo.images.standard_resolution.url } />
												<div className="insblock-image-overlay">
													<ul>
														{photo.likes != null && 
															<li className="insblock-listing">
																<span className="dashicons dashicons-heart"></span>
																<span className="insblock-count insblock-like-count">{photo.likes.count}</span>
															</li>
														}
														{photo.comments != null && 
															<li className="insblock-listing">
																<span className="dashicons dashicons-admin-comments"></span>
																<span className="insblock-count insblock-comments-count">{photo.comments.count}</span>
															</li>
														}

														{photo.caption != null && 
															<li className="insblock-caption">
																{photo.caption != null && 
																	<p className="caption-title">{photo.caption.text}</p>
																}
															</li>
														}
														
													</ul>
												</div>
											</div>
                                        </div>
                                    );
                                } ) 
                            }
                        </div>
                    </div>

                );
            }
        } else if ( apiResponseCode !== 200 ) {
            instagramImagerender = <div>Something is wrong: { apiErrorMessage }</div>;
        } else {
            instagramImagerender = (
                <div className={ className }>
                    To get Instagram Access Token.{ ' ' }
                    <a target="_blank" href="https://outofthesandbox.com/pages/instagram-access-token" >click here.</a>
                </div>
            );
        }

		return (
			<Fragment>
				<InspectorControls>
					<PanelBody title={ __( 'Access Tokens' ) }>
						<TextControl
							label={ __( 'Instagram Access Token' ) }
							value={ token }
                            help={`To get token link https://outofthesandbox.com/pages/instagram-access-token` }
							onChange={ this.onChangeToken }
						/>
					</PanelBody>
                    <PanelBody title={ __( 'Layout Settings' ) }>
						<RangeControl
							value={ numberCols }
							onChange={ numberCols => setAttributes( { numberCols } ) }
							min={ 1 }
							max={ 6 }
							step={ 1 }
							label={ __( 'Columns' ) }
						/>

						<RangeControl
							value={ numberImages }
							onChange={ this.onChangeImages }
							min={ 1 }
							max={ 20 }
							step={ 1 }
							label={ __( 'Number of Images' ) }
						/>

						<RangeControl
							value={ gridGap }
							onChange={ gridGap => setAttributes( { gridGap } ) }
							min={ 0 }
							max={ 20 }
							step={ 1 }
							label={ __( 'Image spacing (px)' ) }
						/>

						<ToggleControl
							label={ __( 'Show profile?' ) }
							checked={ showProfile }
							onChange={ this.onChangeShowProfile }
						/>

						<ToggleControl
							label={ __( 'Show equal sized images?' ) }
							checked={ hasEqualImages }
							onChange={ hasEqualImages => setAttributes( { hasEqualImages } ) }
						/>

					</PanelBody>
				</InspectorControls>
                
                {instagramImagerender}
            </Fragment>
		);
	}
}
