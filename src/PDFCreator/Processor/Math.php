<?php

namespace BlueSpice\ProDistributionConnector\PDFCreator\Processor;

use BlueSpice\ProDistributionConnector\PDFCreator\Utility\ImageFinder;
use MediaWiki\Extension\Math\Render\RendererFactory;
use MediaWiki\Extension\PDFCreator\IProcessor;
use MediaWiki\Extension\PDFCreator\Utility\ExportContext;
use MediaWiki\Utils\UrlUtils;

class Math implements IProcessor {

	/** @var UrlUtils */
	private $urlUtils;

	/** @var RendererFactory */
	private $rendererFactory;

	/**
	 * @param UrlUtils $urlUtils
	 * @param RendererFactory $rendererFactory
	 */
	public function __construct( UrlUtils $urlUtils, RendererFactory $rendererFactory ) {
		$this->urlUtils = $urlUtils;
		$this->rendererFactory = $rendererFactory;
	}

	/**
	 * @inheritDoc
	 */
	public function execute( array &$pages, array &$images, array &$attachments,
		ExportContext $context, string $module = '', $params = [] ): void {
		$imageFinder = new ImageFinder( $this->urlUtils, $this->rendererFactory );
		$results = $imageFinder->execute( $pages, $images );

		/** @var WikiFileResource */
		foreach ( $results as $result ) {
			$filename = $result->getFilename();
			$images[$filename] = $result->getAbsolutePath();
		}
	}

	/**
	 * @inheritDoc
	 */
	public function getPosition(): int {
		return 50;
	}
}
