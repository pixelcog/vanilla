<?php if (!defined('APPLICATION')) exit(); ?>

<div class="container">
  <noscript>
    <p class="DismissMessage WarningMessage"><strong>Error:</strong> You must enable JavaScript in order to sign in to this forum.</p>
  </noscript>

  <p id="Connect-Error" class="Hidden DismissMessage WarningMessage">
    <strong>Whoops!</strong> There was an error communicating with the <span id="Connect-AuthServerName"></span> authentication server.  Please try again later.<br />
    <br />
    <strong>Administrators:</strong><br />
    Debug Message: '<span id="Connect-ErrorMsg"></span>'; <a href="/entry/password">Click here</a> for manual password entry.
  </p>

  <div id="Connect-Wait" class="Hidden">
    <h1><?php echo T('Please wait...'); ?></h1>
    <img src="<?php echo Asset('plugins/AutoLogin/ajax_loader_gray_128.gif'); ?>" width="64" height="64" />
  </div>

  <?php echo $this->Form->Open(array('id' => 'Form_AutoLogin-Connect')), $this->Form->Errors(), $this->Form->Close(); ?>
</div>
