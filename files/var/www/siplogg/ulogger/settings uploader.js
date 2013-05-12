$(document).ready(function(){
  //Load uploader
  var uploader = new qq.FineUploader({
    element: document.getElementById('fine-uploader'),
    request: {endpoint: 'fine-upload/uploader.php'},
    callbacks: {
      onComplete: function(id, name, response) {
        $("#upgrade-to").html(name);
        //$("#fine-uploader").hide();        
        $.ajax({
          url:"/siplogg/settings_server.php",
          type: 'POST',
          cache: false,
          data: {action: 'checkfile', filename: name},
          dataType: 'json',
          //beforeSend: function(){$('#spinner').show();},
          //complete: function(){$('#spinner').hide(); },
          success:function(result){   
            $("#upgrade-to").html(result.action);            
            /*if (result.action=="changeip") {
              // Omstart måste utföras efter att meddelande visats, annars visas inte meddelandet.
              $.ajax({
                url:"/siplogg/settings_restart_eth0.php",
                type: 'POST'
              });
            }*/                       
          },
          error:function(xhr, ajaxOptions, thrownError){showError("Status: " + xhr.status + " (" + thrownError + ").");} 
        });              
      }
    }    
  });

  
  
});

























