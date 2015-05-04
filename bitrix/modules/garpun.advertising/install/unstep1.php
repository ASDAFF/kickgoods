<?php
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);
?>

<? if ($ex = $APPLICATION->GetException()) { ?>

		<? $message = new CAdminMessage(GetMessage('MOD_UNINST_IMPOSSIBLE'), $ex); ?>
		<?=$message->show(); ?>
<? }?>
	<form action="<?=$APPLICATION->GetCurPage(); ?>">
            <?=CAdminMessage::ShowMessage(GetMessage('MOD_UNINST_WARN')); ?>
		<?=bitrix_sessid_post(); ?>
		<input type="hidden" name="lang" value="<?=LANG; ?>">
		<p><input type="checkbox" name="neverUpdate" value="Y"><?=GetMessage('GARPUN_ADVERTISING_DELETE_TEXT'); ?></p>
		<input type="hidden" name="uninstall" value="Y">
		<input type="hidden" name="step" value="2">
                <input type="hidden" name="id" value="garpun.advertising">
		
		
		
		<input type="submit" name="inst" value="<?=GetMessage('MOD_UNINST_DEL'); ?>">
	</form>
