<script>
{literal}
	var scripturlRoot = '{/literal}{$data.scripturlRoot}{literal}';
	var dynmapAjaxUrl = '/wp/wp-admin/admin-ajax.php?action=';
	dynmapPatchMap(scripturlRoot);
	var config = {
		url			: {
			configuration	: dynmapAjaxUrl+'minecraft_dynmap_fwd&path=up/configuration',
			update			: dynmapAjaxUrl+'minecraft_dynmap_fwd&path=up/world/{world}/{timestamp}',
			sendmessage		: dynmapAjaxUrl+'minecraft_dynmap_fwd&path=up/sendmessage',
			login			: dynmapAjaxUrl+'minecraft_dynmap_fwd&path=up/login',
			register		: dynmapAjaxUrl+'minecraft_dynmap_fwd&path=up/register',
			webroot			: scripturlRoot+'/'
		},
		urlArgsOverride	: {
			worldname		: '{/literal}{$data.prms.world}{literal}',
			mapname			: '{/literal}{$data.prms.map}{literal}',
			nopanel			: '{/literal}{$data.prms.nopanel}{literal}' // be carrefull the test is made on a string
			
		},
		tileUrl		: '{/literal}{$data.scripturlRoot}{literal}/tiles/',
		//tileUrl		: dynmapAjaxUrl+'minecraft_dynmap_fwd&path=tiles/',
		tileWidth	: 128,
		tileHeight	: 128,
		inlineComponents:[{
			type				: 'minecraft-wp',
			showlayercontrol	: {/literal}{$data.prms.showlayercontrol}{literal},
			chat				: {/literal}{$data.prms.chat}{literal},
			largeclock			: {/literal}{$data.prms.largeclock}{literal},
			link				: {/literal}{$data.prms.link}{literal},
			zoomOnFirstLink		: {/literal}{$data.prms.zoomonfirstlink}{literal}
		}]
	};
jQuery(document).ready(function($) {
	window.dynmap = new DynMap($.extend({
		container: $('#{/literal}{$data.prms.uid}{literal}')
	}, config));
});
{/literal}
</script>
<div class="dynmap_container" style="width:{$data.prms.width};height:{$data.prms.height};">
	<div id="{$data.prms.uid}" style="width:100%;height:100%;"></div>
</div>
