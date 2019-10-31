/**
 * BLOCK: qubely-insblock
 *
 * Registering a basic block with Gutenberg.
 * Simple block, renders and saves the same content without any interactivity.
 */

//  Import CSS.
import './editor.scss';
import './style.scss';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;

import { attributes } from './attributes';
import { default as edit } from './edit';

registerBlockType( 'qubelyinsblock/block-qubely-insblock', {
	title: __( 'Qubely Insblock' ),
	icon: 'shield', 
	category: 'common',
	attributes,
	edit,
	save( { attributes, className } ) {
		return null;
	},
} );
