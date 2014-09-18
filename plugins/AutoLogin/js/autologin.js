// (c) 2014 PixelCog Inc.
// MIT License http://www.opensource.org/licenses/mit-license.php

jQuery(function($) {
  $('#Connect-Wait').fadeIn(1000);

  var auth_url = gdn.definition('AuthUrl', false);
  var auth_name = gdn.definition('AuthName', null);
  var sign_in_url = gdn.definition('SignInUrl', '/entry/password');

  if (auth_url) {

    var error = function(xhr, error_text) {
      $('#Connect-ErrorMsg').text(error_text);
      $('#Connect-AuthServerName').text(auth_name);
      $('#Connect-Error').fadeIn(500);
      $('#Connect-Wait').hide();
    }

    var success = function(response) {
      if (response['error']) return error(null, response['message']);
      if (!response['name']) return window.location.replace(sign_in_url);

      $('#Form_JsConnect').val($.param(response));
      $('#Form_AutoLogin-Connect').submit();
    }

    $.ajax({ url: auth_url, dataType: 'jsonp', timeout : 10000, success: success, error: error });
  }
});
