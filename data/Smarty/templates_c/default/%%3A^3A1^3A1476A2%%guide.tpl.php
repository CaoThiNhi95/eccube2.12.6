<?php /* Smarty version 2.6.26, created on 2018-10-31 10:17:19
         compiled from /Applications/MAMP/htdocs/eccube-2.12.6/html/../data/Smarty/templates/default/frontparts/bloc/guide.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'script_escape', '/Applications/MAMP/htdocs/eccube-2.12.6/html/../data/Smarty/templates/default/frontparts/bloc/guide.tpl', 28, false),)), $this); ?>

<div class="block_outer">
    <div id="guide_area" class="block_body">
        <?php echo '<ul class="button_like"><li><a href="'; ?><?php echo ((is_array($_tmp=@ROOT_URLPATH)) ? $this->_run_mod_handler('script_escape', true, $_tmp) : smarty_modifier_script_escape($_tmp)); ?><?php echo 'abouts/'; ?><?php echo ((is_array($_tmp=@DIR_INDEX_PATH)) ? $this->_run_mod_handler('script_escape', true, $_tmp) : smarty_modifier_script_escape($_tmp)); ?><?php echo '" class="'; ?><?php if (((is_array($_tmp=$this->_tpl_vars['tpl_page_category'])) ? $this->_run_mod_handler('script_escape', true, $_tmp) : smarty_modifier_script_escape($_tmp)) == 'abouts'): ?><?php echo ' selected'; ?><?php endif; ?><?php echo '">当サイトについて</a></li><li><a href="'; ?><?php echo ((is_array($_tmp=@HTTPS_URL)) ? $this->_run_mod_handler('script_escape', true, $_tmp) : smarty_modifier_script_escape($_tmp)); ?><?php echo 'contact/'; ?><?php echo ((is_array($_tmp=@DIR_INDEX_PATH)) ? $this->_run_mod_handler('script_escape', true, $_tmp) : smarty_modifier_script_escape($_tmp)); ?><?php echo '" class="'; ?><?php if (((is_array($_tmp=$this->_tpl_vars['tpl_page_category'])) ? $this->_run_mod_handler('script_escape', true, $_tmp) : smarty_modifier_script_escape($_tmp)) == 'contact'): ?><?php echo ' selected'; ?><?php endif; ?><?php echo '">お問い合わせ</a></li><li><a href="'; ?><?php echo ((is_array($_tmp=@ROOT_URLPATH)) ? $this->_run_mod_handler('script_escape', true, $_tmp) : smarty_modifier_script_escape($_tmp)); ?><?php echo 'order/'; ?><?php echo ((is_array($_tmp=@DIR_INDEX_PATH)) ? $this->_run_mod_handler('script_escape', true, $_tmp) : smarty_modifier_script_escape($_tmp)); ?><?php echo '" class="'; ?><?php if (((is_array($_tmp=$this->_tpl_vars['tpl_page_category'])) ? $this->_run_mod_handler('script_escape', true, $_tmp) : smarty_modifier_script_escape($_tmp)) == 'order'): ?><?php echo ' selected'; ?><?php endif; ?><?php echo '">特定商取引に関する表記</a></li><li><a href="'; ?><?php echo ((is_array($_tmp=@ROOT_URLPATH)) ? $this->_run_mod_handler('script_escape', true, $_tmp) : smarty_modifier_script_escape($_tmp)); ?><?php echo 'guide/privacy.php" class="'; ?><?php if (((is_array($_tmp=$this->_tpl_vars['tpl_page_category'])) ? $this->_run_mod_handler('script_escape', true, $_tmp) : smarty_modifier_script_escape($_tmp)) == 'order'): ?><?php echo ' selected'; ?><?php endif; ?><?php echo '">プライバシーポリシー</a></li></ul>'; ?>

        <div style="height: 0px; overflow: hidden;"></div>    </div>
</div>