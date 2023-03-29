// eslint-disable-next-line no-global-assign
ext = ext || {};
ext.proDistribution = ext.proDistribution || {};
ext.proDistribution.objects = ext.proDistribution.objects || {};

ext.proDistribution.objects.SMWReportDroplet = function( cfg ) {
	ext.proDistribution.objects.SMWReportDroplet.parent.call( this, cfg );
};

OO.inheritClass( ext.proDistribution.objects.SMWReportDroplet, ext.contentdroplets.object.TransclusionDroplet );

ext.proDistribution.objects.SMWReportDroplet.prototype.templateMatches = function( templateData ) {
	if ( !templateData ) {
		return false;
	}
	var target = templateData.target.wt;
	return target.trim( '\n' ) === 'SMWReport';
};

ext.proDistribution.objects.SMWReportDroplet.prototype.toDataElement = function( domElements, converter  ) {
	return false;
};

ext.proDistribution.objects.SMWReportDroplet.prototype.getFormItems = function() {
	return [
		{
			name: 'count',
			label: mw.message( 'droplets-smw-report-count-label' ).plain(),
			type: 'number'
		},
		{
			name: 'namespaces',
			label: mw.message( 'droplets-smw-report-ns-label' ).plain(),
			type: 'text'
		},
		{
			name: 'categories',
			label: mw.message( 'droplets-smw-report-cat-label' ).plain(),
			type: 'text'
		},
		{
			name: 'modified',
			label: mw.message( 'droplets-smw-report-modified-label' ).plain(),
			type: 'text'
		},
		{
			name: 'printouts',
			label: mw.message( 'droplets-smw-report-printouts-label' ).plain(),
			type: 'textarea',
			rows: 3
		}
	];
};

ext.contentdroplets.registry.register( 'smw-report', ext.proDistribution.objects.SMWReportDroplet );
