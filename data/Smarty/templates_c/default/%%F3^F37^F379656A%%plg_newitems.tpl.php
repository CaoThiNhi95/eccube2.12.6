<?php /* Smarty version 2.6.26, created on 2018-10-31 10:17:23
         compiled from /Applications/MAMP/htdocs/eccube-2.12.6/html/../data/Smarty/templates/default/frontparts/bloc/plg_newitems.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'script_escape', '/Applications/MAMP/htdocs/eccube-2.12.6/html/../data/Smarty/templates/default/frontparts/bloc/plg_newitems.tpl', 30, false),array('modifier', 'sfNoImageMainList', '/Applications/MAMP/htdocs/eccube-2.12.6/html/../data/Smarty/templates/default/frontparts/bloc/plg_newitems.tpl', 47, false),array('modifier', 'h', '/Applications/MAMP/htdocs/eccube-2.12.6/html/../data/Smarty/templates/default/frontparts/bloc/plg_newitems.tpl', 47, false),array('modifier', 'number_format', '/Applications/MAMP/htdocs/eccube-2.12.6/html/../data/Smarty/templates/default/frontparts/bloc/plg_newitems.tpl', 50, false),)), $this); ?>

<style type="text/css">
#arrNewItems h2 {
 padding: 5px 0 8px 10px;
 margin-bottom:10px;
 border-style: solid;
 border-color: #f90 #ccc #ccc;
 border-width: 1px 1px 0;
 background: url('<?php echo ((is_array($_tmp=$this->_tpl_vars['TPL_URLPATH'])) ? $this->_run_mod_handler('script_escape', true, $_tmp) : smarty_modifier_script_escape($_tmp)); ?>
img/background/bg_btn_bloc_02.jpg') repeat-x left bottom #fef3d8;
}
#arrNewItems{margin-bottom:10px;}
#arrNewItems ul li {float:left; width:115px;}
#arrNewItems ul li p.item_image{ text-align:center;}
#arrNewItems ul li p.price{ font-size:90%;}
#arrNewItems ul li p.price em{ color:#FF0000;}
</style>
<?php if (((is_array($_tmp=$this->_tpl_vars['arrNewItems'])) ? $this->_run_mod_handler('script_escape', true, $_tmp) : smarty_modifier_script_escape($_tmp))): ?>
<!-- arrNewItems -->
<div id="arrNewItems">
<h2><img src="<?php echo ((is_array($_tmp=@TOP_URLPATH)) ? $this->_run_mod_handler('script_escape', true, $_tmp) : smarty_modifier_script_escape($_tmp)); ?>
plugin/NewItems/media/images/tit_bloc_newitems.jpg" alt="*" class="title_icon" /></h2>
<ul class="clearfix">
<?php unset($this->_sections['cnt']);
$this->_sections['cnt']['name'] = 'cnt';
$this->_sections['cnt']['loop'] = is_array($_loop=((is_array($_tmp=$this->_tpl_vars['arrNewItems'])) ? $this->_run_mod_handler('script_escape', true, $_tmp) : smarty_modifier_script_escape($_tmp))) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['cnt']['show'] = true;
$this->_sections['cnt']['max'] = $this->_sections['cnt']['loop'];
$this->_sections['cnt']['step'] = 1;
$this->_sections['cnt']['start'] = $this->_sections['cnt']['step'] > 0 ? 0 : $this->_sections['cnt']['loop']-1;
if ($this->_sections['cnt']['show']) {
    $this->_sections['cnt']['total'] = $this->_sections['cnt']['loop'];
    if ($this->_sections['cnt']['total'] == 0)
        $this->_sections['cnt']['show'] = false;
} else
    $this->_sections['cnt']['total'] = 0;
if ($this->_sections['cnt']['show']):

            for ($this->_sections['cnt']['index'] = $this->_sections['cnt']['start'], $this->_sections['cnt']['iteration'] = 1;
                 $this->_sections['cnt']['iteration'] <= $this->_sections['cnt']['total'];
                 $this->_sections['cnt']['index'] += $this->_sections['cnt']['step'], $this->_sections['cnt']['iteration']++):
$this->_sections['cnt']['rownum'] = $this->_sections['cnt']['iteration'];
$this->_sections['cnt']['index_prev'] = $this->_sections['cnt']['index'] - $this->_sections['cnt']['step'];
$this->_sections['cnt']['index_next'] = $this->_sections['cnt']['index'] + $this->_sections['cnt']['step'];
$this->_sections['cnt']['first']      = ($this->_sections['cnt']['iteration'] == 1);
$this->_sections['cnt']['last']       = ($this->_sections['cnt']['iteration'] == $this->_sections['cnt']['total']);
?>
<li>
<p class="item_image">
<a href="<?php echo ((is_array($_tmp=@P_DETAIL_URLPATH)) ? $this->_run_mod_handler('script_escape', true, $_tmp) : smarty_modifier_script_escape($_tmp)); ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['arrNewItems'][$this->_sections['cnt']['index']]['product_id'])) ? $this->_run_mod_handler('script_escape', true, $_tmp) : smarty_modifier_script_escape($_tmp)); ?>
">
<img src="<?php echo ((is_array($_tmp=@ROOT_URLPATH)) ? $this->_run_mod_handler('script_escape', true, $_tmp) : smarty_modifier_script_escape($_tmp)); ?>
resize_image.php?image=<?php echo ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['arrNewItems'][$this->_sections['cnt']['index']]['main_list_image'])) ? $this->_run_mod_handler('script_escape', true, $_tmp) : smarty_modifier_script_escape($_tmp)))) ? $this->_run_mod_handler('sfNoImageMainList', true, $_tmp) : SC_Utils_Ex::sfNoImageMainList($_tmp)))) ? $this->_run_mod_handler('h', true, $_tmp) : smarty_modifier_h($_tmp)); ?>
&amp;width=110&amp;height=110" alt="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['arrNewItems'][$this->_sections['cnt']['index']]['name'])) ? $this->_run_mod_handler('script_escape', true, $_tmp) : smarty_modifier_script_escape($_tmp)))) ? $this->_run_mod_handler('h', true, $_tmp) : smarty_modifier_h($_tmp)); ?>
" /></a>
</p>
<p class="checkItemname"><a href="<?php echo ((is_array($_tmp=@P_DETAIL_URLPATH)) ? $this->_run_mod_handler('script_escape', true, $_tmp) : smarty_modifier_script_escape($_tmp)); ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['arrNewItems'][$this->_sections['cnt']['index']]['product_id'])) ? $this->_run_mod_handler('script_escape', true, $_tmp) : smarty_modifier_script_escape($_tmp)); ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['arrNewItems'][$this->_sections['cnt']['index']]['name'])) ? $this->_run_mod_handler('script_escape', true, $_tmp) : smarty_modifier_script_escape($_tmp)); ?>
</a></p>
<p class="price"><?php echo ((is_array($_tmp=@SALE_PRICE_TITLE)) ? $this->_run_mod_handler('script_escape', true, $_tmp) : smarty_modifier_script_escape($_tmp)); ?>
(税込)<br /><em><?php if (((is_array($_tmp=$this->_tpl_vars['arrNewItems'][$this->_sections['cnt']['index']]['price02_min_inctax'])) ? $this->_run_mod_handler('script_escape', true, $_tmp) : smarty_modifier_script_escape($_tmp)) == ((is_array($_tmp=$this->_tpl_vars['arrNewItems'][$this->_sections['cnt']['index']]['price02_max_inctax'])) ? $this->_run_mod_handler('script_escape', true, $_tmp) : smarty_modifier_script_escape($_tmp))): ?><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['arrNewItems'][$this->_sections['cnt']['index']]['price02_min_inctax'])) ? $this->_run_mod_handler('script_escape', true, $_tmp) : smarty_modifier_script_escape($_tmp)))) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>
<?php else: ?><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['arrNewItems'][$this->_sections['cnt']['index']]['price02_min_inctax'])) ? $this->_run_mod_handler('script_escape', true, $_tmp) : smarty_modifier_script_escape($_tmp)))) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>
〜<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['arrNewItems'][$this->_sections['cnt']['index']]['price02_max_inctax'])) ? $this->_run_mod_handler('script_escape', true, $_tmp) : smarty_modifier_script_escape($_tmp)))) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>
<?php endif; ?>円</em></p>
</li>
<?php endfor; endif; ?>
</ul>
</div>
<!-- / arrNewItems END -->
<?php endif; ?>