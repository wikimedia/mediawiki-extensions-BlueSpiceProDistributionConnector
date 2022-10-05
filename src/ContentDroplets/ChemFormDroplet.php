<?php

declare( strict_types = 1 );

namespace BlueSpice\ProDistributionConnector\ContentDroplets;

use MediaWiki\Extension\ContentDroplets\Droplet\GenericDroplet;
use Message;
use RawMessage;

class ChemFormDroplet extends GenericDroplet {

	/**
	 */
	public function __construct() {
	}

	/**
	 * @inheritDoc
	 */
	public function getName(): Message {
		return new RawMessage( 'Chemical formula' );
	}

	/**
	 * @inheritDoc
	 */
	public function getDescription(): Message {
		return new RawMessage( "Chemical formula" );
	}

	/**
	 * @inheritDoc
	 */
	public function getIcon(): string {
		return 'check';
	}

	/**
	 * @inheritDoc
	 */
	public function getRLModule(): string {
		return 'ext.math.visualEditor';
	}

	/**
	 * @return array
	 */
	public function getCategories(): array {
		return [ 'content', 'visualization' ];
	}

	/**
	 * @inheritDoc
	 */
	public function getContent(): string {
		return '<chem>A + B</chem>';
	}

	/**
	 * @inheritDoc
	 */
	public function getVeCommand(): ?string {
		return 'chemDialog';
	}
}