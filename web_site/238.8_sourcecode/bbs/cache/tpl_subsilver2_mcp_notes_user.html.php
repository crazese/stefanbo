<?php if (!defined('IN_PHPBB')) exit; $this->_tpl_include('mcp_header.html'); ?>

<form method="post" name="mcp" action="<?php echo (isset($this->_rootref['U_POST_ACTION'])) ? $this->_rootref['U_POST_ACTION'] : ''; ?>">
	
<table width="100%" cellpadding="3" cellspacing="1" border="0" class="tablebg">
<tr>
	<th colspan="2" align="center"><?php echo (isset($this->_rootref['USERNAME'])) ? $this->_rootref['USERNAME'] : ''; ?></th>
</tr>
<tr> 
	<td class="row1" align="center">
		<table cellspacing="1" cellpadding="2" border="0">
		<tr>
			<td class="gen" align="center"><b><?php echo (isset($this->_rootref['USERNAME_FULL'])) ? $this->_rootref['USERNAME_FULL'] : ''; ?></b></td>
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
		<table width="100%" cellspacing="1" cellpadding="2" border="0">
		<tr> 
			<td class="gen" align="<?php echo (isset($this->_rootref['S_CONTENT_FLOW_END'])) ? $this->_rootref['S_CONTENT_FLOW_END'] : ''; ?>" nowrap="nowrap"><?php echo ((isset($this->_rootref['L_JOINED'])) ? $this->_rootref['L_JOINED'] : ((isset($user->lang['JOINED'])) ? $user->lang['JOINED'] : '{ JOINED }')); ?>: </td>
			<td width="100%"><b class="gen"><?php echo (isset($this->_rootref['JOINED'])) ? $this->_rootref['JOINED'] : ''; ?></b></td>
		</tr>
		<tr> 
			<td class="gen" align="<?php echo (isset($this->_rootref['S_CONTENT_FLOW_END'])) ? $this->_rootref['S_CONTENT_FLOW_END'] : ''; ?>" valign="top" nowrap="nowrap"><?php echo ((isset($this->_rootref['L_TOTAL_POSTS'])) ? $this->_rootref['L_TOTAL_POSTS'] : ((isset($user->lang['TOTAL_POSTS'])) ? $user->lang['TOTAL_POSTS'] : '{ TOTAL_POSTS }')); ?>: </td>
			<td><b class="gen"><?php echo (isset($this->_rootref['POSTS'])) ? $this->_rootref['POSTS'] : ''; ?></b></td>
		</tr>
		<tr> 
			<td class="gen" align="<?php echo (isset($this->_rootref['S_CONTENT_FLOW_END'])) ? $this->_rootref['S_CONTENT_FLOW_END'] : ''; ?>" valign="top" nowrap="nowrap"><?php echo ((isset($this->_rootref['L_WARNINGS'])) ? $this->_rootref['L_WARNINGS'] : ((isset($user->lang['WARNINGS'])) ? $user->lang['WARNINGS'] : '{ WARNINGS }')); ?>: </td>
			<td><b class="gen"><?php echo (isset($this->_rootref['WARNINGS'])) ? $this->_rootref['WARNINGS'] : ''; ?></b></td>
		</tr>
		</table>
	</td>
</tr>
</table>

<br />

<table width="100%" cellpadding="3" cellspacing="1" border="0" class="tablebg">
<tr>
	<th colspan="5" align="center"><?php echo ((isset($this->_rootref['L_FEEDBACK'])) ? $this->_rootref['L_FEEDBACK'] : ((isset($user->lang['FEEDBACK'])) ? $user->lang['FEEDBACK'] : '{ FEEDBACK }')); ?></th>
</tr>
<?php if ($this->_rootref['S_USER_NOTES']) {  ?>

	<tr align="center">
		<td colspan="5" class="row3"><span class="gensmall"><?php echo ((isset($this->_rootref['L_DISPLAY_LOG'])) ? $this->_rootref['L_DISPLAY_LOG'] : ((isset($user->lang['DISPLAY_LOG'])) ? $user->lang['DISPLAY_LOG'] : '{ DISPLAY_LOG }')); ?>:</span> <?php echo (isset($this->_rootref['S_SELECT_SORT_DAYS'])) ? $this->_rootref['S_SELECT_SORT_DAYS'] : ''; ?>&nbsp;<span class="gensmall"><?php echo ((isset($this->_rootref['L_SORT_BY'])) ? $this->_rootref['L_SORT_BY'] : ((isset($user->lang['SORT_BY'])) ? $user->lang['SORT_BY'] : '{ SORT_BY }')); ?>:</span> <?php echo (isset($this->_rootref['S_SELECT_SORT_KEY'])) ? $this->_rootref['S_SELECT_SORT_KEY'] : ''; ?> <?php echo (isset($this->_rootref['S_SELECT_SORT_DIR'])) ? $this->_rootref['S_SELECT_SORT_DIR'] : ''; ?>&nbsp;<input class="btnlite" type="submit" value="<?php echo ((isset($this->_rootref['L_GO'])) ? $this->_rootref['L_GO'] : ((isset($user->lang['GO'])) ? $user->lang['GO'] : '{ GO }')); ?>" name="sort" /></td>
	</tr>
	<tr>
		<th><?php echo ((isset($this->_rootref['L_REPORT_BY'])) ? $this->_rootref['L_REPORT_BY'] : ((isset($user->lang['REPORT_BY'])) ? $user->lang['REPORT_BY'] : '{ REPORT_BY }')); ?></th>
		<th><?php echo ((isset($this->_rootref['L_IP'])) ? $this->_rootref['L_IP'] : ((isset($user->lang['IP'])) ? $user->lang['IP'] : '{ IP }')); ?></th>
		<th><?php echo ((isset($this->_rootref['L_TIME'])) ? $this->_rootref['L_TIME'] : ((isset($user->lang['TIME'])) ? $user->lang['TIME'] : '{ TIME }')); ?></th>
		<th><?php echo ((isset($this->_rootref['L_ACTION'])) ? $this->_rootref['L_ACTION'] : ((isset($user->lang['ACTION'])) ? $user->lang['ACTION'] : '{ ACTION }')); ?></th>
		<th><?php if ($this->_rootref['S_CLEAR_ALLOWED']) {  echo ((isset($this->_rootref['L_MARK'])) ? $this->_rootref['L_MARK'] : ((isset($user->lang['MARK'])) ? $user->lang['MARK'] : '{ MARK }')); } ?></th>
	</tr>

	<?php $_usernotes_count = (isset($this->_tpldata['usernotes'])) ? sizeof($this->_tpldata['usernotes']) : 0;if ($_usernotes_count) {for ($_usernotes_i = 0; $_usernotes_i < $_usernotes_count; ++$_usernotes_i){$_usernotes_val = &$this->_tpldata['usernotes'][$_usernotes_i]; if (!($_usernotes_val['S_ROW_COUNT'] & 1)  ) {  ?><tr class="row1"><?php } else { ?><tr class="row2"><?php } ?>
			<td class="gen"><?php echo $_usernotes_val['REPORT_BY']; ?></td>
			<td style="text-align: center;"><?php echo $_usernotes_val['IP']; ?></td>
			<td style="text-align: center;"><?php echo $_usernotes_val['REPORT_AT']; ?></td>
			<td class="gen">
				<?php echo $_usernotes_val['ACTION']; ?>
				<?php if ($_usernotes_val['DATA']) {  ?><br />&#187; <span class="gensmall">[ <?php echo $_usernotes_val['DATA']; ?> ]</span><?php } ?>
			</td>
			<td style="text-align: center;"><?php if ($this->_rootref['S_CLEAR_ALLOWED']) {  ?><input type="checkbox" class="radio" name="marknote[]" value="<?php echo $_usernotes_val['ID']; ?>" /><?php } ?></td>
		</tr>
	<?php }} if ($this->_rootref['S_CLEAR_ALLOWED']) {  ?>
		<tr>
			<td class="cat" colspan="5" align="center"><input class="btnlite" type="submit" name="action[del_all]" value="<?php echo ((isset($this->_rootref['L_DELETE_ALL'])) ? $this->_rootref['L_DELETE_ALL'] : ((isset($user->lang['DELETE_ALL'])) ? $user->lang['DELETE_ALL'] : '{ DELETE_ALL }')); ?>" />&nbsp; <input class="btnlite" type="submit" name="action[del_marked]" value="<?php echo ((isset($this->_rootref['L_DELETE_MARKED'])) ? $this->_rootref['L_DELETE_MARKED'] : ((isset($user->lang['DELETE_MARKED'])) ? $user->lang['DELETE_MARKED'] : '{ DELETE_MARKED }')); ?>" /></td>
		</tr>
	<?php } } else { ?>
	<tr>
		<td class="row1" colspan="2" align="center"><span class="gen"><?php echo ((isset($this->_rootref['L_NO_FEEDBACK'])) ? $this->_rootref['L_NO_FEEDBACK'] : ((isset($user->lang['NO_FEEDBACK'])) ? $user->lang['NO_FEEDBACK'] : '{ NO_FEEDBACK }')); ?></span></td>
	</tr>
<?php } ?>
</table>

<br clear="all" />
	
<table width="100%" cellpadding="3" cellspacing="1" border="0" class="tablebg">
<tr>
	<th colspan="2" align="center"><?php echo ((isset($this->_rootref['L_ADD_FEEDBACK'])) ? $this->_rootref['L_ADD_FEEDBACK'] : ((isset($user->lang['ADD_FEEDBACK'])) ? $user->lang['ADD_FEEDBACK'] : '{ ADD_FEEDBACK }')); ?></th>
</tr>
<tr>
	<td class="row3" align="center" colspan="2"><span class="genmed"><?php echo ((isset($this->_rootref['L_ADD_FEEDBACK_EXPLAIN'])) ? $this->_rootref['L_ADD_FEEDBACK_EXPLAIN'] : ((isset($user->lang['ADD_FEEDBACK_EXPLAIN'])) ? $user->lang['ADD_FEEDBACK_EXPLAIN'] : '{ ADD_FEEDBACK_EXPLAIN }')); ?></span></td>
</tr>
<tr>
	<td colspan="2" class="row1" align="center"><textarea name="usernote" rows="10" cols="76"></textarea></td>
</tr>
<tr>
	<td class="cat" colspan="2" align="center"><input class="btnmain" type="submit" name="action[add_feedback]" value="<?php echo ((isset($this->_rootref['L_SUBMIT'])) ? $this->_rootref['L_SUBMIT'] : ((isset($user->lang['SUBMIT'])) ? $user->lang['SUBMIT'] : '{ SUBMIT }')); ?>" />&nbsp;&nbsp;<input class="btnlite" type="reset" value="<?php echo ((isset($this->_rootref['L_RESET'])) ? $this->_rootref['L_RESET'] : ((isset($user->lang['RESET'])) ? $user->lang['RESET'] : '{ RESET }')); ?>" /></td>
</tr>
</table>

<table width="100%" cellspacing="0" cellpadding="0">
<tr>
	<td class="pagination"><?php echo (isset($this->_rootref['PAGE_NUMBER'])) ? $this->_rootref['PAGE_NUMBER'] : ''; ?> [ <?php echo (isset($this->_rootref['TOTAL_REPORTS'])) ? $this->_rootref['TOTAL_REPORTS'] : ''; ?> ]</td>
	<td align="<?php echo (isset($this->_rootref['S_CONTENT_FLOW_END'])) ? $this->_rootref['S_CONTENT_FLOW_END'] : ''; ?>"><span class="pagination"><?php $this->_tpl_include('pagination.html'); ?></span></td>
</tr>
</table>
<?php echo (isset($this->_rootref['S_FORM_TOKEN'])) ? $this->_rootref['S_FORM_TOKEN'] : ''; ?>
</form>

<br clear="all" /><br />

<?php $this->_tpl_include('mcp_footer.html'); ?>