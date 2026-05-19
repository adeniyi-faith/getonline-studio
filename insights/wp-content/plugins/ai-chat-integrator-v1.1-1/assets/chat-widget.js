(function(){
  function $(s){ return document.querySelector(s); }
  function create(tag, attrs, txt){ var e = document.createElement(tag); if(attrs){ for(var k in attrs) e.setAttribute(k, attrs[k]); } if(txt) e.innerHTML = txt; return e; }

  function init() {
    var root = document.getElementById('ai-chat-root');
    if(!root) return;
    // apply classes for floating/inline
    var isInline = root.classList.contains('ai-inline-root');
    if(!isInline) root.classList.add('ai-floating-root');

    // create toggle button for floating
    var toggle = create('div', {class:'ai-toggle-btn ai-toggle-bottom-right', id:'ai_chat_toggle'});
    toggle.style.background = AI_CHAT.primary_color || 'var(--ai-primary)';
    toggle.innerHTML = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 3C7 3 3.5 6.5 3.5 10.5C3.5 13 5 14.5 6 15.5C6 16 6 17 6 18C6 18.6 6.4 19 7 19C7.3 19 7.6 18.9 7.8 18.7C9.3 17.2 10.6 17 12 17C17 17 20.5 13.5 20.5 9.5C20.5 5.5 16.9 3 12 3Z" fill="#fff"></path></svg>';
    document.body.appendChild(toggle);

    // create widget
    var widget = create('div',{class:'ai-widget ai-hidden', id:'ai_chat_widget'});
    var header = create('div',{class:'ai-header'}, '<div class="ai-title">'+(AI_CHAT.bot_name||'Assistant')+'</div><div class="ai-sub">'+(AI_CHAT.welcome||'Hello')+'</div>');
    var body = create('div',{class:'ai-body'});
    var messages = create('div',{class:'ai-messages', id:'ai_chat_messages'});
    body.appendChild(messages);
    var footer = create('div',{class:'ai-footer'});
    var input = create('input',{type:'text', id:'ai_chat_input', placeholder:'Type your question...'});
    var send = create('button',{id:'ai_chat_send'}, 'Send');
    footer.appendChild(input); footer.appendChild(send);
    widget.appendChild(header); widget.appendChild(body); widget.appendChild(footer);
    root.appendChild(widget);

    // position toggle based on option
    if(AI_CHAT.position && AI_CHAT.position.indexOf('left') !== -1){
      toggle.classList.remove('ai-toggle-bottom-right'); toggle.classList.add('ai-toggle-bottom-left');
    }

    // show/hide logic
    function openWidget(){ widget.classList.remove('ai-hidden'); toggle.style.display='none'; input.focus(); }
    function closeWidget(){ widget.classList.add('ai-hidden'); toggle.style.display='flex'; }
    toggle.addEventListener('click', function(){ openWidget(); });
    // clicking outside on mobile should close
    document.addEventListener('click', function(e){
      if(!widget.contains(e.target) && !toggle.contains(e.target) && !widget.classList.contains('ai-hidden')) {
        closeWidget();
      }
    });

    // send handler
    function append(who, txt){
      var m = create('div',{class:'ai-message ' + (who==='user'?'ai-message-user':'ai-message-bot')});
      m.innerHTML = '<div>'+txt+'</div>';
      messages.appendChild(m);
      messages.scrollTop = messages.scrollHeight;
    }

    function sendMsg(){
      var v = input.value.trim(); if(!v) return;
      append('user', v);
      input.value = '';
      // show typing indicator
      var typing = create('div',{class:'ai-message ai-message-bot'}, 'Typing...');
      messages.appendChild(typing);
      messages.scrollTop = messages.scrollHeight;

      fetch(AI_CHAT.ajax_url, {
        method: 'POST',
        headers: {'Content-Type':'application/json','X-WP-Nonce': AI_CHAT.nonce},
        body: JSON.stringify({message: v})
      }).then(r=>r.json()).then(data=>{
        typing.remove();
        if(data && data.reply){
          append('bot', data.reply);
        } else if(data && data.data && data.data.message){
          append('bot', data.data.message);
        } else {
          append('bot', 'Sorry, something went wrong.');
        }
      }).catch(err=>{
        typing.remove();
        append('bot', 'Error contacting the chat server.');
      });
    }

    send.addEventListener('click', sendMsg);
    input.addEventListener('keydown', function(e){ if(e.key === 'Enter') sendMsg(); });

    // If inline, open widget by default
    if(isInline) openWidget();
  }

  document.addEventListener('DOMContentLoaded', init);
})();
