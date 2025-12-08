bs.util.registerNamespace( 'ext.proDistribution.ve.datatransferhandler' );

ext.proDistribution.ve.datatransferhandler.ExternalContentUrl = function () {
	// Parent constructor
	ext.proDistribution.ve.datatransferhandler.ExternalContentUrl.super.apply( this, arguments );

};

OO.inheritClass( ext.proDistribution.ve.datatransferhandler.ExternalContentUrl, ve.ui.HTMLStringTransferHandler );

ext.proDistribution.ve.datatransferhandler.ExternalContentUrl.static.name = 'externalContentUrl';
ext.proDistribution.ve.datatransferhandler.ExternalContentUrl.static.supportedUrls = require( './supportedUrls.json' );
ext.proDistribution.ve.datatransferhandler.ExternalContentUrl.static.types = [ 'text/plain' ];
ext.proDistribution.ve.datatransferhandler.ExternalContentUrl.static.handlesPaste = true;

ext.proDistribution.ve.datatransferhandler.ExternalContentUrl.static.matchFunction = function ( item ) {
	// Supports only pasting the URL directly (not as part of a text or HTML string)
	// This is becuase we are *including* the content here, not just transforming the lin
	if ( ext.proDistribution.ve.datatransferhandler.ExternalContentUrl.static.types.indexOf( item.type ) >= 0 ) {
		const subject = item.getAsString();
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
	const text = this.item.getAsString().trim();
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
