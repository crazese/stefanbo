<?php if (!defined('IN_PHPBB')) exit; $this->_tpl_include('mcp_header.html'); ?>

<table width="100%" cellpadding="3" cellspacing="1" border="0" class="tablebg">
<tr>
	<th colspan="2" align="center"><?php echo ((isset($this->_rootref['L_POST'])) ? $this->_rootref['L_POST'] : ((isset($user->lang['POST'])) ? $user->lang['POST'] : '{ POST }')); ?></th>
</tr>
<tr> 
	<td class="row1" align="center">
		<table cellspacing="1" cellpadding="2" border="0">
		<tr>
			<td class="gen" align="center"><?php if ($this->_rootref['USER_COLOR']) {  ?><b style="color: #<?php echo (isset($this->_rootref['USER_COLOR'])) ? $this->_rootref['USER_COLOR'] : ''; ?>"><?php } else { ?><b><?php } echo (isset($this->_rootref['USERNAME'])) ? $this->_rootref['USERNAME'] : ''; ?></b></td>
		</tr>
		<?php if ($this->_rootref['RANK_TITLE']) {  ?>
			<tr>
				<td class="postdetails" align="center"><?php echo (isset($this->_rootref['RANK_TITLE'])) ? $this->_rootref['RANK_TITLE'] : ''; ?></td>
			</tr>
		<?php } if ($this->_rootref['RANK_IMG']) {  ?>
			<tr>
				<td align="center"><?php echo (isset($this->_rootref['RANK_IMG'])) ? $this->_rootref['RANK_IMG'] : ''; ?></td>
			</tr>
		<?php } ?>
		<tr>
			<td align="center"><?php if ($this->_rootref['AVATAR_IMG']) {  echo (isset($this->_rootref['AVATAR_IMG'])) ? $this->_rootref['AVATAR_IMG'] : ''; } else { ?><img src="<?php echo (isset($this->_rootref['T_THEME_PATH'])) ? $this->_rootref['T_THEME_PATH'] : ''; ?>/images/no_avatar.gif" alt="" /><?php } ?></td>
		</tr>
		</table>
	</td>
	<td class="row1">
		<span class="gen"><?php echo (isset($this->_rootref['POST'])) ? $this->_rootref['POST'] : ''; ?></span>
	</td>
</tr>
</table>

<br clear="all" /><br />

<form method="post" name="mcp" action="<?php echo (isset($this->_rootref['U_POST_ACTION'])) ? $this->_rootref['U_POST_ACTION'] : ''; ?>">

<table width="100%" cellpadding="3" cellspacing="1" border="0" class="tablebg">
<tr>
	<th align="center"><?php echo ((isset($this->_rootref['L_ADD_WARNING'])) ? $this->_rootref['L_ADD_WARNING'] : ((isset($user->lang['ADD_WARNING'])) ? $user->lang['ADD_WARNING'] : '{ ADD_WARNING }')); ?></th>
</tr>
<tr>
	<td class="row3" align="center"><span class="genmed"><?php echo ((isset($this->_rootref['L_ADD_WARNING_EXPLAIN'])) ? $this->_rootref['L_ADD_WARNING_EXPLAIN'] : ((isset($user->lang['ADD_WARNING_EXPLAIN'])) ? $user->lang['ADD_WARNING_EXPLAIN'] : '{ ADD_WARNING_EXPLAIN }')); ?></span></td>
</tr>
<tr>
	<td class="row1" align="center"><textarea name="warning" rows="10" cols="76"><?php echo ((isset($this->_rootref['L_WARNING_POST_DEFAULT'])) ? $this->_rootref['L_WARNING_POST_DEFAULT'] : ((isset($user->lang['WARNING_POST_DEFAULT'])) ? $user->lang['WARNING_POST_DEFAULT'] : '{ WARNING_POST_DEFAULT }')); ?></textarea></td>
</tr>
<?php if ($this->_rootref['S_CAN_NOTIFY']) {  ?>
<tr>
	<td class="row1" align="center"><input type="checkbox" class="radio" name="notify_user" checked="checked" /><span class="genmed"><?php echo ((isset($this->_rootref['L_NOTIFY_USER_WARN'])) ? $this->_rootref['L_NOTIFY_USER_WARN'] : ((isset($user->lang['NOTIFY_USER_WARN'])) ? $user->lang['NOTIFY_USER_WARN'] : '{ NOTIFY_USER_WARN }')); ?></span></td>
</tr>
<?php } ?>
<tr>
	<td class="cat" align="center"><input class="btnmain" type="submit" name="action[add_warning]" value="<?php echo ((isset($this->_rootref['L_SUBMIT'])) ? $this->_rootref['L_SUBMIT'] : ((isset($user->lang['SUBMIT'])) ? $user->lang['SUBMIT'] : '{ SUBMIT }')); ?>" />&nbsp;&nbsp;<input class="btnlite" type="reset" value="<?php echo ((isset($this->_rootref['L_RESET'])) ? $this->_rootref['L_RESET'] : ((isset($user->lang['RESET'])) ? $user->lang['RESET'] : '{ RESET }')); ?>" /></td>
</tr>
</table>
<?php echo (isset($this->_rootref['S_FORM_TOKEN'])) ? $this->_rootref['S_FORM_TOKEN'] : ''; ?>
</form>

<br clear="all" /><br />

<?php $this->_tpl_include('mcp_footer.html'); ?>