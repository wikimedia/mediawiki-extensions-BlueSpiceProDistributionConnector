<?php

namespace BlueSpice\ProDistributionConnector\Rest;

use File;
use GuzzleHttp\Psr7\LimitStream;
use MediaWiki\HookContainer\HookContainer;
use MediaWiki\HookContainer\HookRunner;
use MediaWiki\Permissions\GroupPermissionsLookup;
use MediaWiki\Permissions\PermissionManager;
use MediaWiki\Rest\HttpException;
use MediaWiki\Rest\Response;
use MediaWiki\Rest\SimpleHandler;
use MediaWiki\Rest\Stream;
use MediaWiki\Title\Title;
use RepoGroup;
use Wikimedia\FileBackend\HTTPFileStreamer;
use Wikimedia\ParamValidator\ParamValidator;

/**
 * REST handler copy of img_auth.php with access token auth.
 *
 * GET /rest.php/bluespice/prodistributionconnector/v1/file-auth/{title}
 * Authorization: Bearer <access_token>
 */
class FileAuthHandler extends SimpleHandler {

	private RepoGroup $repoGroup;
	private PermissionManager $permissionManager;
	private GroupPermissionsLookup $groupPermissionsLookup;
	private HookContainer $hookContainer;

	public function __construct(
		RepoGroup $repoGroup,
		PermissionManager $permissionManager,
		GroupPermissionsLookup $groupPermissionsLookup,
		HookContainer $hookContainer
	) {
		$this->repoGroup = $repoGroup;
		$this->permissionManager = $permissionManager;
		$this->groupPermissionsLookup = $groupPermissionsLookup;
		$this->hookContainer = $hookContainer;
	}

	/**
	 * @param string $title
	 * @return Response
	 * @throws HttpException
	 */
	public function run( string $title ): Response {
		$params = $this->getValidatedParams();
		$shouldDownload = $params['download'];
		$authority = $this->getAuthority();
		$user = $authority->getUser();
		$publicWiki = $this->groupPermissionsLookup->groupHasPermission( '*', 'read' );

		$titleObj = Title::makeTitleSafe( NS_FILE, $title );
		if ( !$titleObj ) {
			throw new HttpException( 'Invalid file title', 400 );
		}

		$file = $this->repoGroup->findFile( $titleObj, [ 'private' => $authority ] );
		if ( !$file || !$file->exists() || $file->isDeleted( File::DELETED_FILE ) ) {
			throw new HttpException( 'File not found', 404 );
		}

		$hookRunner = new HookRunner( $this->hookContainer );

		if ( !$publicWiki ) {
			if ( !$this->permissionManager->userCan( 'read', $user, $titleObj ) ) {
				throw new HttpException( 'Access denied', 403 );
			}

			$path = '/' . $file->getRel();
			$name = $file->getName();
			$authResult = [];
			if ( !$hookRunner->onImgAuthBeforeStream( $titleObj, $path, $name, $authResult ) ) {
				throw new HttpException( 'Access denied', 403 );
			}
		}

		$util = $this->getConditionalHeaderUtil();
		$util->setValidators( '"' . $file->getSha1() . '"', $file->getTimestamp(), true );
		$preconditionStatus = $util->checkPreconditions( $this->getRequest() );
		if ( $preconditionStatus !== null ) {
			$response = $this->getResponseFactory()->create();
			$response->setStatus( $preconditionStatus );
			$util->applyResponseHeaders( $response );
			return $response;
		}

		$localPath = $file->getLocalRefPath();
		if ( $localPath === false || !is_readable( $localPath ) ) {
			throw new HttpException( 'File not accessible', 500 );
		}

		$extraHeaders = [];
		if ( !$publicWiki ) {
			$extraHeaders['Cache-Control'] = 'private';
			$extraHeaders['Vary'] = 'Cookie';
		}
		$hookRunner->onImgAuthModifyHeaders( $titleObj->getTitleValue(), $extraHeaders );

		$request = $this->getRequest();
		if ( $shouldDownload ) {
			$extraHeaders['Content-Disposition'] = 'attachment; filename="' . addslashes( $file->getName() ) . '"';
		}

		$response = $this->getResponseFactory()->create();
		$response->setHeader( 'Content-Type', $file->getMimeType() );
		$response->setHeader( 'Accept-Ranges', 'bytes' );
		$util->applyResponseHeaders( $response );

		foreach ( $extraHeaders as $headerName => $headerValue ) {
			$response->setHeader( $headerName, $headerValue );
		}

		$fileSize = $file->getSize();
		$rangeHeader = $request->getHeaderLine( 'Range' ) ?: null;
		$rangeStart = 0;
		$rangeLength = null;

		if ( $rangeHeader !== null ) {
			$range = HTTPFileStreamer::parseRange( $rangeHeader, $fileSize );
			if ( is_array( $range ) ) {
				[ $rangeStart, $rangeEnd, $rangeLength ] = $range;
				$response->setStatus( 206 );
				$response->setHeader( 'Content-Length', (string)$rangeLength );
				$response->setHeader( 'Content-Range', "bytes $rangeStart-$rangeEnd/$fileSize" );
			} elseif ( $range === 'invalid' ) {
				$response->setStatus( 416 );
				$response->setHeader( 'Content-Range', "bytes */$fileSize" );
				return $response;
			} else {
				// Unrecognised range format — serve the full file
				$response->setStatus( 200 );
				$response->setHeader( 'Content-Length', (string)$fileSize );
			}
		} else {
			$response->setStatus( 200 );
			$response->setHeader( 'Content-Length', (string)$fileSize );
		}

		$fh = fopen( $localPath, 'rb' );
		if ( $fh === false ) {
			throw new HttpException( 'Cannot read file', 500 );
		}

		$body = new Stream( $fh );
		if ( $rangeLength !== null ) {
			$body = new LimitStream( $body, $rangeLength, $rangeStart );
		}
		$response->setBody( $body );

		return $response;
	}

	/**
	 * @return bool
	 */
	public function needsWriteAccess(): bool {
		return false;
	}

	/**
	 * @return array[]
	 */
	public function getParamSettings(): array {
		return [
			'title' => [
				self::PARAM_SOURCE => 'path',
				ParamValidator::PARAM_TYPE => 'string',
				ParamValidator::PARAM_REQUIRED => true,
			],
			'download' => [
				self::PARAM_SOURCE => 'query',
				ParamValidator::PARAM_TYPE => 'boolean',
				ParamValidator::PARAM_DEFAULT => false,
			]
		];
	}
}
