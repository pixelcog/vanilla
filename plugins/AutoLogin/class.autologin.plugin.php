<?php if (!defined('APPLICATION')) exit();
/**
 * Automatic Login Plugin for Vanilla Forums.
 *
 * @author    Mike Greiling <mike@pixelcog.com>
 * @copyright 2014 PixelCog, Inc. (http://pixelcog.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 */

$PluginInfo['autologin'] = array(
  'Name' => 'Vanilla AutoLogin',
  'Description' => 'Automatically attempt to connect via SSO if jsConnect is enabled.',
  'Version' => '0.0.2',
  'RequiredApplications' => array('Vanilla' => '2.1.1'),
  'MobileFriendly' => TRUE,
  'Author' => 'Mike Greiling',
  'AuthorEmail' => 'mike@pixelcog.com',
  'AuthorUrl' => 'http://www.pixelcog.com/'
);

class AutoLoginPlugin extends Gdn_Plugin {

  /**
   * If jsConnect is enabled, bypass password form and automatically attempt to sign in via SSO
   *
   * @param Gdn_Dispatcher $Sender
   * @param array $Args 
   */
  public function EntryController_SignIn_Handler($Sender, $Args) {
    if (!C('EnabledPlugins.jsconnect') || !$Provider = $this->_DefaultJsConnectProvider()) {
      return;
    }

    $client_id = $Provider['AuthenticationKey'];
    $Target = $Sender->Request->Get('Target', 0);
    $SignInUrl = $Provider['SignInUrl'] .
      (strpos($Provider['SignInUrl'], '?') === FALSE ? '?' : '&') .
      http_build_query(compact('Target'));

    $Sender->AddDefinition('AuthUrl', JsConnectPlugin::ConnectUrl($Provider, TRUE, FALSE));
    $Sender->AddDefinition('AuthName', $Provider['Name']);
    $Sender->AddDefinition('SignInUrl', $SignInUrl);

    $Sender->Form->Action = Url('/entry/connect/jsconnect?' . http_build_query(compact('client_id')));
    $Sender->Form->AddHidden('JsConnect', '');
    $Sender->Form->AddHidden('Target', $Target);

    $Sender->MasterView = 'empty';
    $Sender->AddCssFile('autologin.css', 'plugins/AutoLogin');
    $Sender->AddJsFile('autologin.js', 'plugins/AutoLogin');
    $Sender->Render('autologin', '', 'plugins/AutoLogin');
    die();
  }


  /**
   * If jsConnect is enabled, send the signed-out user to the SSO provider's sign-out url
   *
   * @param Gdn_Dispatcher $Sender
   * @param array $Args 
   */
  public function EntryController_SignOut_Handler($Sender, $Args) {
    if (!C('EnabledPlugins.jsconnect') || !$Provider = $this->_DefaultJsConnectProvider()) {
      return;
    }

    $SignOutUrl = $Provider['SignOutUrl'] .
      (strpos($Provider['SignOutUrl'], '?') === FALSE ? '?' : '&') .
      http_build_query(array('sso_signout' => 1));

    Redirect($SignOutUrl);
  }


  /**
   * Create a convenient, secure way to sign out and redirect to location designated by the SSO provider.
   *
   * @param Gdn_Dispatcher $Sender
   * @param array $Args 
   */
  public function EntryController_RemoteSignOut_Create($Sender, $Args) {
    if (!C('EnabledPlugins.jsconnect') || !$Provider = $this->_DefaultJsConnectProvider()) {
      die('Error: SSO Disabled or No Provider Found');
    }

    $Now = time();
    $Timestamp = $Sender->Request->Get('timestamp', 0);
    $Signature = $Sender->Request->Get('signature', '');
    $Target = $Sender->Request->Get('target', $Provider['SignInUrl']);

    if (abs($Now - $Timestamp) > 600) {
      die('Error: Invalid Timestamp');
    }
    if (md5($Timestamp.$Provider['AssociationSecret']) != $Signature) {
      die('Error: Invalid Signature ' . md5($Timestamp.$Provider['AssociationSecret']) . ' != ' . $Signature);
    }

    Gdn::Session()->End();
    Redirect($Target);
  }

  /**
   * Get the default jsConnect provider.
   * 
   * @return array Provider attributes from the jsConnect plugin
   */
  private function _DefaultJsConnectProvider() {
    $Provider = NULL;
    if (C('EnabledPlugins.jsconnect')) {
      foreach(JsConnectPlugin::GetAllProviders() as $Provider) {
        if (GetValue('IsDefault', $Provider)) {
          break;
        }
      }
    }
    return $Provider;
  }
}
