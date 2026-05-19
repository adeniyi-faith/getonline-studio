jQuery(document).ready(function($){
  $('#ai-chat-upload-btn').on('click', function(e){
    e.preventDefault();
    var fd = new FormData($('#ai-chat-import-csv')[0]);
    fd.append('action','ai_chat_import_csv');
    fd.append('_wpnonce', AI_CHAT_ADMIN.nonce);
    $.ajax({
      url: AI_CHAT_ADMIN.ajax_url,
      method:'POST',
      data: fd,
      contentType: false,
      processData: false,
      success: function(res){ $('#ai-chat-import-result').html('<div class="notice notice-success"><p>Imported: '+res.data.imported+'</p></div>'); },
      error: function(err){ $('#ai-chat-import-result').html('<div class="notice notice-error"><p>Error importing file</p></div>'); }
    });
  });
});
