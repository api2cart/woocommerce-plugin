jQuery(document).ready(function($) {

  var SELF_PATH = 'plugins.php?page=api2cart-config&a2caction=';

  var messages = $('#messages');

  var installationsText = $('#connector-installed-txt');
  var contentBlockManage = $('#content-block-manage');

  var showButton = $("#showButton");
  var bridgeStoreKey = $('#bridgeStoreKey');
  var storeKey = $('#storeKey');
  var storeBlock = $('.store-key');
  var classMessage = $('.message');
  var progress = $('.progress');

  var timeDelay = 500;

  var api2cartConnectionInstall  =$("#api2cartConnectionInstall");
  var api2cartConnectionUninstall  =$("#api2cartConnectionUninstall");

  var updateBridgeStoreKey = $('#updateBridgeStoreKey');

  if (showButton.val() == 'install') {
    installationsText.show();
    contentBlockManage.hide();
    storeBlock.fadeOut();
    updateBridgeStoreKey.hide();
    api2cartConnectionUninstall.hide();
    api2cartConnectionInstall.show();
  } else {
    installationsText.hide();
    contentBlockManage.show();
    storeBlock.fadeIn();
    updateBridgeStoreKey.show();
    api2cartConnectionInstall.hide();
    api2cartConnectionUninstall.show();
  }

  function errorMessage(message,status) {
    if (status == 'success') {
      classMessage.html('<span>' + message + '</span>');
      classMessage.fadeIn("slow");
      classMessage.fadeOut(5000);
      var messageClear = setTimeout(function(){
        classMessage.html('');
      }, 3000);
      clearTimeout(messageClear);
    }
  }

  $('.btn-setup').click(function() {
    var self = $(this);
    $(this).attr("disabled", true);
    progress.slideDown("fast");
    var install = 'install';
    if (showButton.val() == 'uninstall') {
      install = 'remove';
    }

    $.ajax({
      cache: false,
      url: SELF_PATH + install+'Bridge',
      success: function(data){
        self.attr("disabled", false);
        progress.slideUp("fast");
        if (install == 'install') {
          updateStoreKey(data);
          installationsText.fadeOut(timeDelay);
          contentBlockManage.delay(timeDelay).fadeIn(timeDelay);
          storeBlock.fadeIn("slow");
          updateBridgeStoreKey.fadeIn("slow");
          showButton.val('uninstall');
          api2cartConnectionInstall.hide();
          api2cartConnectionUninstall.show();
          errorMessage('Connector Installed Successfully','success');
        } else {
          contentBlockManage.fadeOut(timeDelay);
          installationsText.delay(timeDelay).fadeIn(timeDelay);
          storeBlock.fadeOut("fast");
          updateBridgeStoreKey.fadeOut("fast");
          showButton.val('install');
          api2cartConnectionUninstall.hide();
          api2cartConnectionInstall.show();
          errorMessage('Connector Uninstalled Successfully','success');
        }
      }
    });
  });

  updateBridgeStoreKey.click(function(){
    $.ajax({
      cache: false,
      url: SELF_PATH +'updateToken',
      success: function(data){
        updateStoreKey(data);
        errorMessage('Connector Updated Successfully!','success');
      }
    });
  });

  function updateStoreKey(data){
    storeKey.html(data);
  }

});