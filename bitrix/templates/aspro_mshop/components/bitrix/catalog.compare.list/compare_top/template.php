<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<!--noindex-->
<div class="wraps_icon_block">
	<a href="<?=$arParams["COMPARE_URL"]?>"<?=(!$arResult ? 'style="display:none;"' : '')?> class="link" title="<?=GetMessage("CATALOG_COMPARE_ELEMENTS");?>"></a>
	<div class="count">
		<span>
			<span class="items">
				<span class="text"><?=count($arResult);?></span>
			</span>
		</span>
	</div>
</div>
<div class="clearfix"></div>
<?if($arResult):?>
	<?
	global $compare_items;
	foreach($arResult as $key=>$arItem){
		$compare_items[$key] = $key;
	}
	?>
	<script type="text/javascript">
	if(typeof arBasketAspro !== 'undefined'){
		arBasketAspro.COMPARE = <?=CUtil::PhpToJSObject($compare_items, false, true);?>;
	}
	</script>
<?endif;?>
<!--/noindex-->