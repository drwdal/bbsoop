<input type="hidden" id="mod_env_domain" value="<?php echo DOMAIN; ?>" />
<input type="hidden" id="mod_env_base_uri" value="<?php echo BASE_URI; ?>" />
<input type="hidden" id="mod_env_controller" value="<?php echo $GLOBALS['controller']; ?>" />
<input type="hidden" id="mod_env_action" value="<?php echo $GLOBALS['action']; ?>" />
<input type="hidden" id="mod_env_ID" value="<?php echo $GLOBALS['ID']; ?>" />
<input type="hidden" id="mod_env_fragment" value="<?php echo $GLOBALS['fragment']; ?>" />
<input type="hidden" id="mod_env_extra" value="<?php echo $GLOBALS['extra']; ?>" />
<?php echo nonce_for_form ( ); ?>

