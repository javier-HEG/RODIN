
jQuery(document).ready(function() {
	// Load help content into divisions
	jQuery('#rodinSearchHelp').load('../../app/help/search.php?lang=' + __lang);
	jQuery('#visualizationHelp').load('../../app/help/visualization.php?lang=' + __lang);
	jQuery('#rodinFaqHelp').load('../../app/help/faq.php?lang=' + __lang);
	
	jQuery('#rodinSearchHelpButton').click(
		function(event) {
			jQuery('#rodinSearchHelp').modal({
				position: [(jQuery('#rodinSearchHelpButton').offset().top + 20) + 'px',
					(jQuery('#rodinSearchHelpButton').offset().left - 420) + 'px'],
				onShow: function(dialog) { dialog.container.css("height", "auto") },
				modal: false
			});
	});
});

jQuery('#rodinBoards').ready(function() {
	jQuery('#rodinWidgetsHelp').load('../../app/help/widgets.php?lang=' + __lang);
	jQuery('#rodinOntologiesHelp').load('../../app/help/ontologies.php?lang' + __lang);
	jQuery('#rodinTagCloudHelp').load('../../app/help/tagcloud.php?lang' + __lang);
	
	jQuery('#ontologiesHelpButton').click(
		function(event) {
			jQuery('#rodinOntologiesHelp').modal({
				position: [(jQuery('#ontologiesHelpButton').offset().top + 16) + 'px',
					(jQuery('#ontologiesHelpButton').offset().left) + 'px'],
				onShow: function(dialog) { dialog.container.css("height", "auto") },
				modal: false
			});
	});

	jQuery('#tagcloudHelpButton').click(
		function(event) {
			jQuery('#rodinTagCloudHelp').modal({
				position: [(jQuery('#tagcloudHelpButton').offset().top + 16) + 'px',
					(jQuery('#tagcloudHelpButton').offset().left) + 'px'],
				onShow: function(dialog) { dialog.container.css("height", "auto") },
				modal: false
			});
	});
});

jQuery('#modules').ready(function() {
	jQuery('#rodinResultsHelp').load('../../app/help/results.php?lang' + __lang);

	jQuery('#modules').prepend('<img id="rodinResultsHelpButton" src="../../posh/images/ico_help_rodin.gif" />');
	jQuery('#rodinResultsHelpButton').css('position', 'absolute');
	jQuery('#rodinResultsHelpButton').css('right', '6px');
	jQuery('#rodinResultsHelpButton').css('top', '16px');

	jQuery('#rodinResultsHelpButton').click(
		function(event) {
			jQuery('#rodinResultsHelp').modal({
				position: [(jQuery('#rodinResultsHelpButton').offset().top + 16) + 'px',
					(jQuery('#rodinResultsHelpButton').offset().left - 420) + 'px'],
				onShow: function(dialog) { dialog.container.css("height", "auto") },
				modal: false
			});
	});
});

function showWidgetsHelp() {
	jQuery('#rodinWidgetsHelp').modal({
		position: [(jQuery('#vmenuHelpButton').offset().top + 16) + 'px',
			(jQuery('#vmenuHelpButton').offset().left) + 'px'],
		onShow: function(dialog) { dialog.container.css("height", "auto") },
		modal: false
	});
}

function showVisualizationHelp() {
	jQuery('#visualizationHelp').modal({
		position: [(jQuery('#visualizationHelpButton').offset().top + 16) + 'px',
			(jQuery('#visualizationHelpButton').offset().left - 420) + 'px'],
		onShow: function(dialog) { dialog.container.css("height", "auto") },
		modal: false
	});
}

function showFaqHelp() {
	jQuery('#rodinFaqHelp').modal({
		overlayClose: true,
		closeHTML: '',
		closeClass: 'simplemodal-close-no',
		minWidth: '40%',
	});
}
