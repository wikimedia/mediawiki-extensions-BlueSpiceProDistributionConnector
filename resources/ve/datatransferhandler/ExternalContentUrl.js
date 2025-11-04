bs.util.registerNamespace( 'ext.proDistribution.ve.datatransferhandler' );

ext.proDistribution.ve.datatransferhandler.ExternalContentUrl = function () {
	// Parent constructor
	ext.proDistribution.ve.datatransferhandler.ExternalContentUrl.super.apply( this, arguments );

};

OO.inheritClass( ext.proDistribution.ve.datatransferhandler.ExternalContentUrl, ve.ui.HTMLStringTransferHandler );

ext.proDistribution.ve.datatransferhandler.ExternalContentUrl.static.name = 'externalContentUrl';
ext.proDistribution.ve.datatransferhandler.ExternalContentUrl.static.supportedUrls = require( './supportedUrls.json' );
ext.proDistribution.ve.datatransferhandler.ExternalContentUrl.static.types = [ 'text/plain', 'text/html' ];
ext.proDistribution.ve.datatransferhandler.ExternalContentUrl.static.handlesPaste = true;

ext.proDistribution.ve.datatransferhandler.ExternalContentUrl.static.getAnchorHref = function ( item ) {
	const isAnchorTag = /^<a [^>]*href=["']([^"']+)["'][^>]*>.*<\/a>$/.exec( item );
	if ( isAnchorTag ) {
		// Get href value
		return isAnchorTag[ 1 ];
	}
	return item;
};

ext.proDistribution.ve.datatransferhandler.ExternalContentUrl.static.matchFunction = function ( item ) {
	// Supports only pasting the URL directly (not as part of a text or HTML string)
	// This is because we are *including* the content here, not just transforming the lin
	if ( ext.proDistribution.ve.datatransferhandler.ExternalContentUrl.static.types.indexOf( item.type ) >= 0 ) {
		let subject = item.getAsString();
		subject = ext.proDistribution.ve.datatransferhandler.ExternalContentUrl.static.getAnchorHref( subject ).trim();
		const supported = ext.proDistribution.ve.datatransferhandler.ExternalContentUrl.static.supportedUrls.whitelist;

		// Each this.supportedUrls entry is a RegExp
		for ( const pattern of supported ) {
			if ( new RegExp( pattern ).test( subject ) ) {
				return true;
			}
		}
		return false;
	}
};

ext.proDistribution.ve.datatransferhandler.ExternalContentUrl.prototype.process = function () {
	const bitbucketUrls = ext.proDistribution.ve.datatransferhandler.ExternalContentUrl.static.supportedUrls.bitbucket;
	let text = this.item.getAsString().trim();
	text = ext.proDistribution.ve.datatransferhandler.ExternalContentUrl.static.getAnchorHref( text ).trim();
	let functionKeyword = 'embed';
	for ( let i = 0; i < bitbucketUrls.length; i++ ) {
		const pattern = bitbucketUrls[ i ];
		if ( new RegExp( pattern ).test( text ) ) {
			functionKeyword = 'bitbucket';
			break;
		}
	}

	const nodeName = functionKeyword === 'embed' ? 'EcEmbedInline' : 'EcBitbucketInline';

	this.resolve( [ {
		type: 'contentDroplet/' + nodeName,
		attributes: {
			mw: {
				parts: [
					{
						template: {
							target: { wt: `#${ functionKeyword }:${ text }`, function: functionKeyword },
							params: {}
						}
					}
				]
			}
		}
	} ] );
};

/* Registration */
ve.ui.dataTransferHandlerFactory.register( ext.proDistribution.ve.datatransferhandler.ExternalContentUrl );
