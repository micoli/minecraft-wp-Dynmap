var objectEquals = function(compareFrom,compareTo) {
	if(!compareFrom) return false;
	if (!compareTo || compareFrom.length != compareTo.length) {
		return false;
	}
	for (var i = 0; i < compareFrom.length; ++i) {
		if (compareFrom[i] !== compareTo[i]) {
			return false;
		}
	}
	return true;
};

function dynmapPatchMap(scripturlRoot){
	KzedMapType.prototype.options.errorTileUrl= scripturlRoot+'/images/blank.png';
	FlatMapType.prototype.options.errorTileUrl= scripturlRoot+'/images/blank.png';
	HDMapType.prototype.options.errorTileUrl= scripturlRoot+'/images/blank.png';

	DynMap.prototype.getParameterByName= function(name) {
		name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
		if(this.options && this.options.urlArgsOverride && this.options.urlArgsOverride[name]!=undefined){
			return this.options.urlArgsOverride[name];
		}
		var regexS = "[\\?&]"+name+"=([^&#]*)";
		var regex = new RegExp( regexS );
		var results = regex.exec( window.location.href );
		if( results == null )
			return "";
		else
			return decodeURIComponent(results[1].replace(/\+/g, " "));
	};
}

componentconstructors['minecraft-wp']= function(dynmap, configuration) {
	console.log('initialize minecraft-wp');
	$(dynmap).bind('mapinitialized', function() {
		var options = [{
			'option' : 'showlayercontrol'	,'class':'leaflet-control-layers'
		},{
			'option' : 'largeclock'			,'class':'largeclock'
		},{
			'option' : 'link'				,'class':'dynmap-link'
		},{
			'option' : 'chat'				,'class':'chat'
		}]
		$(options).each(function(index,value){
			if(configuration[value.option]!=undefined && !configuration[value.option]){
				$('.'+value['class']	,$(dynmap.options.container)).css('display','none');
			}
		});
	});
	var clickOnMapLinkCallback = function(){
		var coord = {};
		eval("coord="+$(this).attr('rel'));
		if(coord.x !=undefined && (coord.container==undefined || (coord.container!=undefined && objectEquals(dynmap.options.container,$(coord.container))))){
			dynmap.panToLocation(coord);
			if(coord.zoom != undefined){
				dynmap.map.setZoom(coord.zoom);	
			}
		}
		return false;
	}
	$('a.dynmapcoord').click(clickOnMapLinkCallback);
	if(configuration.zoomOnFirstLink){
		$('a.dynmapcoord:first').each(clickOnMapLinkCallback);
	}
	/*
	$(dynmap).bind('mapchanging', function() { console.log('mapchanging'); });
	$(dynmap).bind('mapchanged', function() { console.log('mapchanged'); });
	$(dynmap).bind('zoomchanged', function() { console.log('zoomchanged'); });
	$(dynmap).bind('worldupdating', function() { console.log('worldupdating'); });
	$(dynmap).bind('worldupdate', function() { console.log('worldupdate'); });
	$(dynmap).bind('worldupdated', function() { console.log('worldupdated'); });
	$(dynmap).bind('worldupdatefailed', function() { console.log('worldupdatefailed'); });
	$(dynmap).bind('playeradded', function() { console.log('playeradded'); });
	$(dynmap).bind('playerremoved', function() { console.log('playerremoved'); });
	$(dynmap).bind('playerupdated', function() { console.log('playerupdated'); });
	*/
};
jQuery(document).ready(function($) {
	var clickOnMapLinkCallback = function(){
		var coord = {};
		eval("coord="+$(this).attr('rel'));
		if($('#dynmap_iframe_mode_iframe').length>0){
			var $iframe = $('#'+$('#dynmap_iframe_mode_iframe').html());
			var newUrl = $('#dynmap_iframe_mode_iframe').attr('rel')+'&x='+coord.x+'&z='+coord.z+'&y='+coord.y+'&worldname='+coord.world+'&mapname='+coord.map+'&zoom='+coord.zoom;
			$iframe.attr('src',newUrl);
		}
		return false;
	}
	$('a.dynmapcoord').click(clickOnMapLinkCallback);
});
