<?php

namespace BlueSpice\ProDistributionConnector\ContentDroplets;

use MediaWiki\Extension\ContentDroplets\Droplet\ParserFunctionDroplet;
use MediaWiki\Message\Message;

class EmbedDroplet extends ParserFunctionDroplet {

	/**
	 * Get target for the template
	 * @return string
	 */
	protected function getFunction(): string {
		return 'embed';
	}

	/**
	 * Template params
	 * @return array
	 */
	protected function getParams(): array {
		return [
			'lang' => '',
			'showLines' => '',
			'lineNumbers' => '',
		];
	}

	/**
	 * @return string
	 */
	protected function getMainParam(): string {
		return 'url';
	}

	/**
	 * @inheritDoc
	 */
	public function getName(): Message {
		return Message::newFromKey( 'bs-pro-distribution-droplet-embed-name' );
	}

	/**
	 * @inheritDoc
	 */
	public function getDescription(): Message {
		return Message::newFromKey( 'bs-pro-distribution-droplet-embed-desc' );
	}

	/**
	 * @inheritDoc
	 */
	public function getIcon(): string {
		return 'droplet-embed';
	}

	/**
	 * @inheritDoc
	 */
	public function getRLModules(): array {
		return [ 'ext.prodistributionconnector.droplet.extenalcontent.embed' ];
	}

	/**
	 * @inheritDoc
	 */
	public function getCategories(): array {
		return [ 'content', 'featured' ];
	}
}
