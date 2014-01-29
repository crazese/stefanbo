<?php if (!defined('IN_PHPBB')) exit; $this->_tpl_include('ucp_header.html'); if ($this->_rootref['S_ATTACHMENT_ROWS']) {  ?>

	<table class="tablebg" width="100%" cellspacing="1">
	<tr>
		<th nowrap="nowrap">#</th>
		<th nowrap="nowrap" width="15%"><a href="<?php echo (isset($this->_rootref['U_SORT_FILENAME'])) ? $this->_rootref['U_SORT_FILENAME'] : ''; ?>"><?php echo ((isset($this->_rootref['L_FILENAME'])) ? $this->_rootref['L_FILENAME'] : ((isset($user->lang['FILENAME'])) ? $user->lang['FILENAME'] : '{ FILENAME }')); ?></a></th>
		<th nowrap="nowrap" width="5%"><a href="<?php echo (isset($this->_rootref['U_SORT_POST_TIME'])) ? $this->_rootref['U_SORT_POST_TIME'] : ''; ?>"><?php echo ((isset($this->_rootref['L_POST_TIME'])) ? $this->_rootref['L_POST_TIME'] : ((isset($user->lang['POST_TIME'])) ? $user->lang['POST_TIME'] : '{ POST_TIME }')); ?></a></th>
		<th nowrap="nowrap" width="5%"><a href="<?php echo (isset($this->_rootref['U_SORT_FILESIZE'])) ? $this->_rootref['U_SORT_FILESIZE'] : ''; ?>"><?php echo ((isset($this->_rootref['L_FILESIZE'])) ? $this->_rootref['L_FILESIZE'] : ((isset($user->lang['FILESIZE'])) ? $user->lang['FILESIZE'] : '{ FILESIZE }')); ?></a></th>
		<th nowrap="nowrap" width="5%"><a href="<?php echo (isset($this->_rootref['U_SORT_DOWNLOADS'])) ? $this->_rootref['U_SORT_DOWNLOADS'] : ''; ?>"><?php echo ((isset($this->_rootref['L_DOWNLOADS'])) ? $this->_rootref['L_DOWNLOADS'] : ((isset($user->lang['DOWNLOADS'])) ? $user->lang['DOWNLOADS'] : '{ DOWNLOADS }')); ?></a></th>
		<th width="2%" nowrap="nowrap"><?php echo ((isset($this->_rootref['L_DELETE'])) ? $this->_rootref['L_DELETE'] : ((isset($user->lang['DELETE'])) ? $user->lang['DELETE'] : '{ DELETE }')); ?></th>
	</tr>
	<?php if ($this->_rootref['TOTAL_ATTACHMENTS']) {  ?>
		<tr>
			<td class="row3" colspan="6">
				<table width="100%" cellspacing="1">
				<tr>
					<td class="nav" valign="middle" nowrap="nowrap">&nbsp;<?php echo (isset($this->_rootref['PAGE_NUMBER'])) ? $this->_rootref['PAGE_NUMBER'] : ''; ?><br /></td>
					<td class="gensmall" nowrap="nowrap">&nbsp;[ <?php echo (isset($this->_rootref['TOTAL_ATTACHMENTS'])) ? $this->_rootref['TOTAL_ATTACHMENTS'] : ''; ?> ]&nbsp;</td>
					<td class="gensmall" width="100%" align="<?php echo (isset($this->_rootref['S_CONTENT_FLOW_END'])) ? $this->_rootref['S_CONTENT_FLOW_END'] : ''; ?>" nowrap="nowrap"><?php $this->_tpl_include('pagination.html'); ?></td>
				</tr>
				</table>
			</td>
		</tr>
	<?php } $_attachrow_count = (isset($this->_tpldata['attachrow'])) ? sizeof($this->_tpldata['attachrow']) : 0;if ($_attachrow_count) {for ($_attachrow_i = 0; $_attachrow_i < $_attachrow_count; ++$_attachrow_i){$_attachrow_val = &$this->_tpldata['attachrow'][$_attachrow_i]; if (!($_attachrow_val['S_ROW_COUNT'] & 1)  ) {  ?><tr class="row2"><?php } else { ?><tr class="row1"><?php } ?>

		<td class="genmed" style="padding: 4px;" align="center" width="2%">&nbsp;<?php echo $_attachrow_val['ROW_NUMBER']; ?>&nbsp;</td>
		<td style="padding: 4px;"><a class="gen" href="<?php echo $_attachrow_val['U_VIEW_ATTACHMENT']; ?>"><?php echo $_attachrow_val['FILENAME']; ?></a><br /><span class="gensmall"><?php if ($_attachrow_val['S_IN_MESSAGE']) {  ?><b><?php echo ((isset($this->_rootref['L_PM'])) ? $this->_rootref['L_PM'] : ((isset($user->lang['PM'])) ? $user->lang['PM'] : '{ PM }')); ?>: </b><?php } else { ?><b><?php echo ((isset($this->_rootref['L_TOPIC'])) ? $this->_rootref['L_TOPIC'] : ((isset($user->lang['TOPIC'])) ? $user->lang['TOPIC'] : '{ TOPIC }')); ?>: </b><?php } ?><a href="<?php echo $_attachrow_val['U_VIEW_TOPIC']; ?>"><?php echo $_attachrow_val['TOPIC_TITLE']; ?></a></span></td>
		<td class="gensmall" style="padding: 4px;" align="center" valign="middle" nowrap="nowrap">&nbsp;<?php echo $_attachrow_val['POST_TIME']; ?>&nbsp;</td>
		<td class="genmed" style="padding: 4px;" align="center" valign="middle" nowrap="nowrap"><?php echo $_attachrow_val['SIZE']; ?></td>
		<td class="genmed" style="padding: 4px;" align="center"><?php echo $_attachrow_val['DOWNLOAD_COUNT']; ?></td>
		<td style="padding: 4px;" align="center" valign="middle"><input type="checkbox" class="radio" name="attachment[<?php echo $_attachrow_val['ATTACH_ID']; ?>]" value="1" /></td>
	</tr>
	<?php }} ?>
	<tr> 
		<td class="cat" colspan="6"><div style="float: <?php echo (isset($this->_rootref['S_CONTENT_FLOW_BEGIN'])) ? $this->_rootref['S_CONTENT_FLOW_BEGIN'] : ''; ?>;"><span class="gensmall"><?php echo ((isset($this->_rootref['L_SORT_BY'])) ? $this->_rootref['L_SORT_BY'] : ((isset($user->lang['SORT_BY'])) ? $user->lang['SORT_BY'] : '{ SORT_BY }')); ?>: </span><select name="sk"><?php echo (isset($this->_rootref['S_SORT_OPTIONS'])) ? $this->_rootref['S_SORT_OPTIONS'] : ''; ?></select> <select name="sd"><?php echo (isset($this->_rootref['S_ORDER_SELECT'])) ? $this->_rootref['S_ORDER_SELECT'] : ''; ?></select>&nbsp;<input class="btnlite" type="submit" name="sort" value="<?php echo ((isset($this->_rootref['L_SORT'])) ? $this->_rootref['L_SORT'] : ((isset($user->lang['SORT'])) ? $user->lang['SORT'] : '{ SORT }')); ?>" /></div><div style="float: <?php echo (isset($this->_rootref['S_CONTENT_FLOW_END'])) ? $this->_rootref['S_CONTENT_FLOW_END'] : ''; ?>;"><input class="btnlite" type="submit" name="delete" value="<?php echo ((isset($this->_rootref['L_DELETE_MARKED'])) ? $this->_rootref['L_DELETE_MARKED'] : ((isset($user->lang['DELETE_MARKED'])) ? $user->lang['DELETE_MARKED'] : '{ DELETE_MARKED }')); ?>" />&nbsp;</div></td>
	</tr>
	</table>

	<div style="float: <?php echo (isset($this->_rootref['S_CONTENT_FLOW_END'])) ? $this->_rootref['S_CONTENT_FLOW_END'] : ''; ?>;"><b class="gensmall"><a href="#" onclick="marklist('ucp', 'attachment', true); return false;"><?php echo ((isset($this->_rootref['L_MARK_ALL'])) ? $this->_rootref['L_MARK_ALL'] : ((isset($user->lang['MARK_ALL'])) ? $user->lang['MARK_ALL'] : '{ MARK_ALL }')); ?></a> :: <a href="#" onclick="marklist('ucp', 'attachment', false); return false;"><?php echo ((isset($this->_rootref['L_UNMARK_ALL'])) ? $this->_rootref['L_UNMARK_ALL'] : ((isset($user->lang['UNMARK_ALL'])) ? $user->lang['UNMARK_ALL'] : '{ UNMARK_ALL }')); ?></a></b></div>

<?php } else { ?>

	<table class="tablebg" width="100%" cellspacing="1">
	<tr>
		<th><?php echo ((isset($this->_rootref['L_TITLE'])) ? $this->_rootref['L_TITLE'] : ((isset($user->lang['TITLE'])) ? $user->lang['TITLE'] : '{ TITLE }')); ?></th>
	</tr>
	<tr class="row1">
		<td align="center"><b class="genmed"><?php echo ((isset($this->_rootref['L_UCP_NO_ATTACHMENTS'])) ? $this->_rootref['L_UCP_NO_ATTACHMENTS'] : ((isset($user->lang['UCP_NO_ATTACHMENTS'])) ? $user->lang['UCP_NO_ATTACHMENTS'] : '{ UCP_NO_ATTACHMENTS }')); ?></b></td>
	</tr>
	</table>

<?php } $this->_tpl_include('ucp_footer.html'); ?>