# AI Chat Integrator v1.1
Author: Faith Adeniyi
Packaged: 2025-09-20T11:10:17.089883Z

Changelog from v1.0 -> v1.1:
- Redesigned, mobile-optimized chat UI that inherits host theme where possible.
- Floating toggle button that opens a beautiful chat interface.
- Bot rename option in admin settings.
- Enable/Disable toggle for the chatbot.
- Multi-provider support with fallback: OpenAI, Claude, Gemini, Cohere, Mistral.
- Improved provider error handling (tries next provider on failure).
- Fixed Top Questions analytics aggregation and display.
- CSV KB import, conversation storage, escalation via email retained from v1.0.

Important notes:
- For provider endpoints other than OpenAI, admin must verify their API keys and endpoints. The plugin attempts standard API payloads; adjust if your provider requires different params.
- PDF parsing is not included in v1.1; use composer libs to extend.
- Always test on a staging site before deploying to production.

Installation:
- Upload `ai-chat-integrator-v1.1.zip` via WP Admin > Plugins > Add New > Upload Plugin
- Activate and go to AI Chat > Settings to configure API keys, bot name, and options.
