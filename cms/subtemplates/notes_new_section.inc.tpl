<h1><a href="<?php echo CMSHOME;?>"><?php echo $lang['administration']; ?></a> &raquo; <a href="<?php echo CMSHOME;?>?mode=notes"><?php echo $lang['notes']; ?></a> &raquo; <?php echo $lang['create_note_section']; ?></h1>

<?php include('errors.inc.tpl'); ?>

<form action="<?php echo CMSHOME;?>" method="post">
 <div>
  <input type="hidden" name="mode" value="notes" />
  <p><?php echo $lang['note_section_name_m']; ?><br />
  <input type="text" name="new_note_section" value="<?php if(isset($new_note_section)) echo stripslashes($new_note_section); ?>" size="25" /> <input type="submit" value="<?php echo $lang['submit_button_ok']; ?>" ></p>
 </div>
</form>

