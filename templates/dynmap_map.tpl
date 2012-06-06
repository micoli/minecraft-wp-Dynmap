<span style="display:none;" id="dynmap_iframe_mode_{$data.prms.mode}" rel="{$data.prms.dynmapurl}?foo=1">{$data.prms.uid}</span>
{if $data.prms.mode eq 'iframe'}
<div class="dynmap_container" style="width:{$data.prms.width};height:{$data.prms.height};">
	<iframe id="{$data.prms.uid}" style="width:{$data.prms.width};height:{$data.prms.height};" src="{strip}
{$data.prms.dynmapurl}?foo=1
{if $data.prms.world}&worldname={$data.prms.world}{/if}
{if $data.prms.map}&mapname={$data.prms.map}{/if}
{if $data.prms.nopanel}&nopanel={$data.prms.nopanel}{/if}
{if $data.prms.chat eq 'true'}&hidechat=false{else}&hidechat=false{/if}
{/strip}" ></iframe>

</div>
{else}
	<script>
	{literal}
		var scripturlRoot = '{/literal}{$data.siteUrl}{$data.scripturlRoot}{literal}';
		var dynmapAjaxUrl = '{/literal}{$data.siteUrl}{literal}/wp-admin/admin-ajax.php?action=';
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
			tileUrl		: '{/literal}{$data.siteUrl}{$data.scripturlRoot}{literal}/tiles/',
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
{/if}