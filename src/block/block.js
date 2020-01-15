/**
 * BLOCK: Gutenberg Insblock
 *
 * Registering a basic block with Gutenberg.
 * Simple block, renders and saves the same content without any interactivity.
 */

//  Import CSS.
import './editor.scss';
import './style.scss';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;

import { default as edit } from './edit';

registerBlockType( 'insblock/gutenberg-insblock', {
	title: __( 'Gutenberg Insblock' ),
	icon: 'shield', 
	category: 'common',

	edit,
	save: function( props ) {
		return null;
	}
} );
