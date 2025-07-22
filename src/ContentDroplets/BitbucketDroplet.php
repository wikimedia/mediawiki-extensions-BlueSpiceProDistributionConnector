<?php

namespace BlueSpice\ProDistributionConnector\ContentDroplets;

use MediaWiki\Message\Message;

class BitbucketDroplet extends EmbedDroplet {

	/**
	 * Get target for the template
	 * @return string
	 */
	protected function getFunction(): string {
		return 'bitbucket';
	}

	/**
	 * @inheritDoc
	 */
	public function getName(): Message {
		return Message::newFromKey( 'bs-pro-distribution-droplet-bitbucket-name' );
	}

	/**
	 * @inheritDoc
	 */
	public function getDescription(): Message {
		return Message::newFromKey( 'bs-pro-distribution-droplet-bitbucket-desc' );
	}

	/**
	 * @inheritDoc
	 */
	public function getIcon(): string {
		return 'droplet-bitbucket';
	}

	/**
	 * @inheritDoc
	 */
	public function getRLModules(): array {
		return [ 'ext.prodistributionconnector.droplet.extenalcontent.bitbucket' ];
	}

	/**
	 * @inheritDoc
	 */
	public function getCategories(): array {
		return [ 'content' ];
	}
}
