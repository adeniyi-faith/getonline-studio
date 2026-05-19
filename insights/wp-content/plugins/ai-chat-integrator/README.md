# AI Chat Integrator (WordPress plugin)
Author: Faith Adeniyi

This plugin is an MVP-level full version implementing an AI chatbot for WordPress with:
- Admin settings for API keys and styling
- Frontend floating widget and shortcode embed
- Knowledge Base: manual FAQs and CSV import (CSV: title,content)
- Conversation storage and basic analytics
- Escalation via email

## Important notes & limitations
- PDF parsing is not implemented here due to environment constraints; PDF uploads will be stored as media — you can extend import parsing using PHP libraries (eg. `smalot/pdfparser`) via Composer.
- This plugin uses OpenAI-compatible REST calls; ensure your API key has appropriate permissions and billing.
- For production, you should:
  - Harden permission callbacks and add rate limiting
  - Use transient caching and consider vector DB for large KB
  - Implement embeddings & semantic search for higher-quality retrieval
  - Add input/output sanitization tailored to your content needs
  - Add GDPR consent flows if you store conversations

## Installation
1. Upload the plugin ZIP to WordPress (Plugins &gt; Add New &gt; Upload Plugin) and activate.
2. Go to WP Admin &gt; AI Chat &gt; Settings and add your API key.
3. Configure widget mode (floating or inline) and other settings.
4. Use [ai_chat_widget] shortcode to embed inline, or enable floating in settings.

## Files of interest
- `ai-chat-integrator.php` — plugin bootstrap
- `includes/` — main PHP logic (installer, admin, rest, frontend)
- `assets/` — JS/CSS for frontend + admin

## Deploying
- This is a ready-to-use plugin ZIP. After activation, test in a staging site.
- To enable PDF parsing or advanced features, add composer libraries and extend `includes/admin.php` import handlers.

