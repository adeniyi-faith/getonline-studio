(function(){
  function el(tag, attrs, txt){ var e = document.createElement(tag); if(attrs) for(var k in attrs) e.setAttribute(k, attrs[k]); if(txt) e.textContent = txt; return e; }
  function initRoot(){
    var root = document.getElementById('ai-chat-root');
    if(!root) return;
    if(!root.classList.contains('ai-inline')) root.classList.add('ai-floating-root');
    root.innerHTML = '';
    var widget = el('div', {id:'ai_chat_widget', class:'ai-widget'});
    var header = el('div',{class:'ai-header'}, AI_CHAT.welcome || 'Hi!');
    var body = el('div',{class:'ai-body'});
    var messages = el('div',{id:'ai_chat_messages', class:'ai-messages'});
    var footer = el('div',{class:'ai-footer'});
    var input = el('input',{id:'ai_chat_input', placeholder:'Ask me anything...'});
    var send = el('button',{id:'ai_chat_send', type:'button'}, 'Send');
    footer.appendChild(input); footer.appendChild(send);
    body.appendChild(messages);
    widget.appendChild(header); widget.appendChild(body); widget.appendChild(footer);
    root.appendChild(widget);

    // simple event handlers
    send.addEventListener('click', sendMsg);
    input.addEventListener('keydown', function(e){ if(e.key === 'Enter') sendMsg(); });
    function append(who, txt){
      var m = el('div',{class:'ai-message ' + (who==='user'?'ai-message-user':'ai-message-bot')});
      m.innerHTML = '<strong>'+who+':</strong> ' + txt;
      messages.appendChild(m);
      messages.scrollTop = messages.scrollHeight;
    }
    function sendMsg(){
      var v = input.value.trim(); if(!v) return;
      append('user', v);
      input.value = '';
      fetch(AI_CHAT.ajax_url, {
        method: 'POST',
        headers: {'Content-Type':'application/json','X-WP-Nonce': AI_CHAT.nonce},
        body: JSON.stringify({message: v})
      }).then(r=>r.json()).then(data=>{
        if(data && data.reply) append('bot', data.reply);
        else append('bot', 'No reply.');
      }).catch(err=>{ append('bot','Error contacting server'); });
    }
  }

  document.addEventListener('DOMContentLoaded', initRoot);
})();
