bs.util.registerNamespace( 'ext.proDistribution.objects' );

ext.proDistribution.objects.BitbucketDroplet = function ( cfg ) {
	ext.proDistribution.objects.BitbucketDroplet.parent.call( this, cfg );
};

OO.inheritClass( ext.proDistribution.objects.BitbucketDroplet, ext.proDistribution.objects.EmbedDroplet );

ext.proDistribution.objects.BitbucketDroplet.prototype.functionMatches = function ( data ) {
	if ( !data ) {
		return false;
	}
	return data.target.function === 'bitbucket';
};

ext.contentdroplets.registry.register( 'ec-bitbucket', ext.proDistribution.objects.BitbucketDroplet );
