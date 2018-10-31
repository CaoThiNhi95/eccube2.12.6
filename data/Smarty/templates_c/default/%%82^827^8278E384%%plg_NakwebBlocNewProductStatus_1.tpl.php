<?php /* Smarty version 2.6.26, created on 2018-10-31 10:25:50
         compiled from /Applications/MAMP/htdocs/eccube-2.12.6/html/../data/Smarty/templates/default/frontparts/bloc/plg_NakwebBlocNewProductStatus_1.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'script_escape', '/Applications/MAMP/htdocs/eccube-2.12.6/html/../data/Smarty/templates/default/frontparts/bloc/plg_NakwebBlocNewProductStatus_1.tpl', 22, false),array('modifier', 'u', '/Applications/MAMP/htdocs/eccube-2.12.6/html/../data/Smarty/templates/default/frontparts/bloc/plg_NakwebBlocNewProductStatus_1.tpl', 30, false),array('modifier', 'sfNoImageMainList', '/Applications/MAMP/htdocs/eccube-2.12.6/html/../data/Smarty/templates/default/frontparts/bloc/plg_NakwebBlocNewProductStatus_1.tpl', 31, false),array('modifier', 'h', '/Applications/MAMP/htdocs/eccube-2.12.6/html/../data/Smarty/templates/default/frontparts/bloc/plg_NakwebBlocNewProductStatus_1.tpl', 31, false),array('modifier', 'number_format', '/Applications/MAMP/htdocs/eccube-2.12.6/html/../data/Smarty/templates/default/frontparts/bloc/plg_NakwebBlocNewProductStatus_1.tpl', 42, false),array('modifier', 'nl2br', '/Applications/MAMP/htdocs/eccube-2.12.6/html/../data/Smarty/templates/default/frontparts/bloc/plg_NakwebBlocNewProductStatus_1.tpl', 44, false),)), $this); ?>

<!-- start NakwebBlocNewProductStatus -->
<?php if (count ( ((is_array($_tmp=$this->_tpl_vars['arrProductStatusNew'])) ? $this->_run_mod_handler('script_escape', true, $_tmp) : smarty_modifier_script_escape($_tmp)) ) > 0): ?>
<div class="block_outer clearfix">
    <div id="recommend_area">
        <h2><?php echo ((is_array($_tmp=$this->_tpl_vars['bloc_title_main'])) ? $this->_run_mod_handler('script_escape', true, $_tmp) : smarty_modifier_script_escape($_tmp)); ?>
（<?php echo ((is_array($_tmp=$this->_tpl_vars['bloc_title_sub'])) ? $this->_run_mod_handler('script_escape', true, $_tmp) : smarty_modifier_script_escape($_tmp)); ?>
）</h2>
        <div class="block_body clearfix">
        <?php $_from = ((is_array($_tmp=$this->_tpl_vars['arrProductStatusNew'])) ? $this->_run_mod_handler('script_escape', true, $_tmp) : smarty_modifier_script_escape($_tmp)); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['plg_nakweb_00004_products'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['plg_nakweb_00004_products']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['arrProductStatusNew']):
        $this->_foreach['plg_nakweb_00004_products']['iteration']++;
?>
            <div class="product_item clearfix">
                <div class="productImage">
                    <a href="<?php echo ((is_array($_tmp=@P_DETAIL_URLPATH)) ? $this->_run_mod_handler('script_escape', true, $_tmp) : smarty_modifier_script_escape($_tmp)); ?>
<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['arrProductStatusNew']['product_id'])) ? $this->_run_mod_handler('script_escape', true, $_tmp) : smarty_modifier_script_escape($_tmp)))) ? $this->_run_mod_handler('u', true, $_tmp) : smarty_modifier_u($_tmp)); ?>
">
                        <img src="<?php echo ((is_array($_tmp=@ROOT_URLPATH)) ? $this->_run_mod_handler('script_escape', true, $_tmp) : smarty_modifier_script_escape($_tmp)); ?>
resize_image.php?image=<?php echo ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['arrProductStatusNew']['main_list_image'])) ? $this->_run_mod_handler('script_escape', true, $_tmp) : smarty_modifier_script_escape($_tmp)))) ? $this->_run_mod_handler('sfNoImageMainList', true, $_tmp) : SC_Utils_Ex::sfNoImageMainList($_tmp)))) ? $this->_run_mod_handler('h', true, $_tmp) : smarty_modifier_h($_tmp)); ?>
&amp;width=80&amp;height=80" alt="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['arrProductStatusNew']['name'])) ? $this->_run_mod_handler('script_escape', true, $_tmp) : smarty_modifier_script_escape($_tmp)))) ? $this->_run_mod_handler('h', true, $_tmp) : smarty_modifier_h($_tmp)); ?>
" />
                    </a>
                </div>
                <div class="productContents">
                    <h3>
                        <a href="<?php echo ((is_array($_tmp=@P_DETAIL_URLPATH)) ? $this->_run_mod_handler('script_escape', true, $_tmp) : smarty_modifier_script_escape($_tmp)); ?>
<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['arrProductStatusNew']['product_id'])) ? $this->_run_mod_handler('script_escape', true, $_tmp) : smarty_modifier_script_escape($_tmp)))) ? $this->_run_mod_handler('u', true, $_tmp) : smarty_modifier_u($_tmp)); ?>
"><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['arrProductStatusNew']['name'])) ? $this->_run_mod_handler('script_escape', true, $_tmp) : smarty_modifier_script_escape($_tmp)))) ? $this->_run_mod_handler('h', true, $_tmp) : smarty_modifier_h($_tmp)); ?>
</a>
                    </h3>

                    <?php $this->assign('price01', ($this->_tpl_vars['arrProductStatusNew']['price01_min_inctax'])); ?>
                    <?php $this->assign('price02', ($this->_tpl_vars['arrProductStatusNew']['price02_min_inctax'])); ?>
                    <p class="sale_price">
                        <?php echo ((is_array($_tmp=@SALE_PRICE_TITLE)) ? $this->_run_mod_handler('script_escape', true, $_tmp) : smarty_modifier_script_escape($_tmp)); ?>
(税込)： <span class="price"><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['price02'])) ? $this->_run_mod_handler('script_escape', true, $_tmp) : smarty_modifier_script_escape($_tmp)))) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>
 円</span>
                    </p>
                    <p class="mini comment"><?php echo ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['arrProductStatusNew']['comment'])) ? $this->_run_mod_handler('script_escape', true, $_tmp) : smarty_modifier_script_escape($_tmp)))) ? $this->_run_mod_handler('h', true, $_tmp) : smarty_modifier_h($_tmp)))) ? $this->_run_mod_handler('nl2br', true, $_tmp) : smarty_modifier_nl2br($_tmp)); ?>
</p>
                </div>
            </div>
            <?php if (((is_array($_tmp=$this->_foreach['plg_nakweb_00004_products']['iteration'])) ? $this->_run_mod_handler('script_escape', true, $_tmp) : smarty_modifier_script_escape($_tmp)) % 2 === 0): ?>
                <div class="clear"></div>
            <?php endif; ?>
        <?php endforeach; endif; unset($_from); ?>
        </div>
    </div>
</div>
<?php endif; ?>
<!-- end   NakwebBlocNewProductStatus -->