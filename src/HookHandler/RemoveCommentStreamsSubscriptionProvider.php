<?php

namespace BlueSpice\ProDistributionConnector\HookHandler;

use MediaWiki\Extension\NotifyMe\Hook\NotifyMeSubscriberProviderFactoryHook;
use MediaWiki\Extension\NotifyMe\SubscriberProvider\SubscriberProviderFactory;

class RemoveCommentStreamsSubscriptionProvider implements NotifyMeSubscriberProviderFactoryHook {

	/**
	 * @inheritDoc
	 */
	public function onNotifyMeSubscriberProviderFactory( SubscriberProviderFactory $factory ) {
		// In BlueSpice 5.3, do not use normal NotifyMe subscription provider from CommentStreams
		$factory->unregisterProvider( 'comment-streams-watchers' );
	}
}
