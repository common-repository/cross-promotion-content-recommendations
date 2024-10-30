<?php
$currentLayout = get_option("engageya_params_layout_type_id");
$plugin_folder = "cross-promotion-content-recommendations";

?>
<html>
<head>
	<script>
		var currentLayout = '<?php echo $currentLayout; ?>';
		var layoutsArr = [32,19,17,20,21,23,25,26,31,39,29,15,22];
		window.showLayouts = function()
		{
			var layoutWidth = 650;
			var webid = 599;
			var wid = 1344;
			if (typeof(sprk_reg) != 'undefined')
				sprk_reg = undefined;
			
			for (var i=0;i<layoutsArr.length;i++)
			{
				var checkedStatus = currentLayout == layoutsArr[i] ? 'checked' : '';
				document.writeln('<div style="position:relative;"><div id="eng_layout'+i+'" style="width:70px;float:left;position:relative;top:100px;"><input type="radio" '+checkedStatus+' onchange="updateLayout(this);" name="layout_id" value="'+layoutsArr[i]+'"></div><div style="width:'+layoutWidth+'px;display:inline-block;"><div id="eng_force_layout'+i+'" style="display:none;">'+layoutsArr[i]+'</div><scr'+'ipt>var dbnwid='+wid+'; var dbnpid=8546; var dbnwebid='+webid+'; var dbnlayout=20; var dbncolor="#9e0000"; var dbntitlefontsize="14"; var dbnbgcolortype=1; var dbnheader="&#1063;&#1080;&#1090;&#1072;&#1081;&#1090;&#1077; &#1090;&#1072;&#1082;&#1078;&#1077;:"; var dbnremindercolor=2; var dbn_protocol = (("https:" == document.location.protocol) ? "https://" : "http://");</scr'+'ipt><scr'+'ipt src="http://widget.engageya.com/sprk.1.0.2.js" class="grazit_script" id="grazit_script" type="text/javascript"></scr'+'ipt></div></div><div style="clear:both"></div><style>#git_wrapper_'+i+'{border-top:none !important;border-bottom:1px solid #d6d6d6;}</style>'); 
			}

		}
		showLayouts();
		function updateLayout(elm) 
		{
			jQuery.post(
				ajaxurl, 
				{
					'action': 'engageya_update_layout',
					'data':   elm.value
				}, 
				function(response){
					//alert('The server responded: ' + response);
				}
			);
		}
	</script>	

</head>
<body id="body"></body>
</html>