<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
@section('title', __('web.sales_agent_page_title'))
@include('users_layout.head')

<body class="index-page" style="@if (app()->getLocale() == 'ar') direction:rtl @else direction:ltr @endif">
    @include('users_layout.header')

    <main class="main">
        <section class="sales-agent-shell">
            <div class="container py-5">
                <div class="agent-hero mb-4">
                    <span class="agent-pill">{{ __('web.sales_agent_pill') }}</span>
                    <h1>{{ __('web.sales_agent_heading') }}</h1>
                    <p>{{ __('web.sales_agent_subheading') }}</p>
                </div>

                <div class="row g-4 align-items-stretch">
                    <div class="col-lg-4">
                        <aside class="assistant-side h-100">
                            <h3>{{ __('web.sales_agent_quick_prompts_title') }}</h3>
                            <p>{{ __('web.sales_agent_quick_prompts_desc') }}</p>
                            <div class="quick-prompts">
                                <button type="button" class="quick-prompt" data-prompt="{{ __('web.sales_agent_prompt_1') }}">
                                    {{ __('web.sales_agent_prompt_1_label') }}
                                </button>
                                <button type="button" class="quick-prompt" data-prompt="{{ __('web.sales_agent_prompt_2') }}">
                                    {{ __('web.sales_agent_prompt_2_label') }}
                                </button>
                                <button type="button" class="quick-prompt" data-prompt="{{ __('web.sales_agent_prompt_3') }}">
                                    {{ __('web.sales_agent_prompt_3_label') }}
                                </button>
                            </div>
                            <div class="assistant-note">
                                <i class="bi bi-shield-check"></i>
                                <span>{{ __('web.sales_agent_note') }}</span>
                            </div>
                        </aside>
                    </div>

                    <div class="col-lg-8">
                        <section class="chat-card h-100">
                            <div class="chat-head">
                                <div>
                                    <h2>Sales Agent Chat</h2>
                                    <small id="agentStatus">{{ __('web.sales_agent_status_ready') }}</small>
                                </div>
                                <button type="button" id="clearChatBtn" class="clear-btn">{{ __('web.sales_agent_new_chat') }}</button>
                            </div>

                            <div id="chatMessages" class="chat-messages">
                                <div class="bubble bubble-agent">
                                    {{ __('web.sales_agent_welcome_message') }}
                                </div>
                            </div>

                            <form id="chatForm" class="chat-form">
                                <textarea id="messageInput" rows="1" placeholder="{{ __('web.sales_agent_input_placeholder') }}" required></textarea>
                                <button type="submit" id="sendBtn">
                                    <span class="send-text">{{ __('web.sales_agent_send') }}</span>
                                    <span class="send-loader d-none"></span>
                                </button>
                            </form>
                        </section>
                    </div>
                </div>
            </div>
        </section>
    </main>

    @include('users_layout.footer')

    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>

    <div id="preloader"></div>

    @include('users_layout.script')

    <script>
        (() => {
            const form = document.getElementById('chatForm');
            const input = document.getElementById('messageInput');
            const chat = document.getElementById('chatMessages');
            const sendBtn = document.getElementById('sendBtn');
            const sendText = sendBtn.querySelector('.send-text');
            const sendLoader = sendBtn.querySelector('.send-loader');
            const clearBtn = document.getElementById('clearChatBtn');
            const status = document.getElementById('agentStatus');
            const promptButtons = document.querySelectorAll('.quick-prompt');
            const askUrl = @json(route('sales.agent.ask'));
            const resetUrl = @json(route('sales.agent.reset'));
            const csrfToken = @json(csrf_token());
            const initialHistory = @json($history ?? []);
            let conversationId = @json($conversationId ?? null);
            const i18nThinking = @json(__('web.sales_agent_status_thinking'));
            const i18nReady = @json(__('web.sales_agent_status_ready'));
            const i18nWelcome = @json(__('web.sales_agent_welcome_message'));
            const i18nReplyFallback = @json(__('web.sales_agent_reply_fallback'));
            const i18nConnectionError = @json(__('web.sales_agent_connection_error'));

            const setBusy = (busy) => {
                sendBtn.disabled = busy;
                input.disabled = busy;
                sendText.classList.toggle('d-none', busy);
                sendLoader.classList.toggle('d-none', !busy);
                status.textContent = busy ? i18nThinking : i18nReady;
            };

            const addMessage = (text, role) => {
                const bubble = document.createElement('div');
                bubble.className = `bubble ${role === 'user' ? 'bubble-user' : 'bubble-agent'}`;
                bubble.textContent = text;
                chat.appendChild(bubble);
                chat.scrollTop = chat.scrollHeight;
            };

            const autoresize = () => {
                input.style.height = 'auto';
                input.style.height = Math.min(input.scrollHeight, 180) + 'px';
            };

            input.addEventListener('input', autoresize);

            const resetChatUi = () => {
                chat.innerHTML = `<div class="bubble bubble-agent">${i18nWelcome}</div>`;
            };

            if (initialHistory.length > 0) {
                chat.innerHTML = '';
                initialHistory.forEach((message) => {
                    addMessage(message.text, message.role === 'user' ? 'user' : 'agent');
                });
            }

            clearBtn.addEventListener('click', async () => {
                conversationId = null;
                resetChatUi();
                input.focus();

                try {
                    await fetch(resetUrl, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                    });
                } catch (error) {
                    // Keep local reset even if request fails.
                }
            });

            promptButtons.forEach((button) => {
                button.addEventListener('click', () => {
                    input.value = button.dataset.prompt;
                    autoresize();
                    form.requestSubmit();
                });
            });

            form.addEventListener('submit', async (event) => {
                event.preventDefault();

                const message = input.value.trim();

                if (!message) {
                    return;
                }

                addMessage(message, 'user');
                input.value = '';
                autoresize();
                setBusy(true);

                try {
                    const response = await fetch(askUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            message,
                            conversation_id: conversationId,
                        }),
                    });

                    const data = await response.json();
                    const reply = data.reply ?? i18nReplyFallback;
                    conversationId = data.conversation_id ?? conversationId;
                    addMessage(reply, 'agent');
                } catch (error) {
                    addMessage(i18nConnectionError, 'agent');
                } finally {
                    setBusy(false);
                    input.focus();
                }
            });
        })();
    </script>

    <style>
        .sales-agent-shell {
            --bg-soft: #f2f6f7;
            --bg-card: #ffffff;
            --ink-900: #102128;
            --ink-700: #37505a;
            --brand-teal: #0f766e;
            --brand-teal-2: #14b8a6;
            --brand-orange: #ea580c;
            --line-soft: #dce8ec;
            background:
                radial-gradient(circle at 10% 10%, rgba(20, 184, 166, 0.13), transparent 28%),
                radial-gradient(circle at 90% 20%, rgba(234, 88, 12, 0.11), transparent 30%),
                linear-gradient(180deg, #fbfdfd 0%, var(--bg-soft) 100%);
            min-height: 85vh;
        }

        .agent-hero h1 {
            font-size: clamp(1.6rem, 2.5vw, 2.6rem);
            line-height: 1.2;
            color: var(--ink-900);
            margin: 12px 0 10px;
            font-weight: 800;
        }

        .agent-hero p {
            color: var(--ink-700);
            max-width: 760px;
            margin: 0;
            font-size: 1.05rem;
        }

        .agent-pill {
            display: inline-block;
            font-size: .75rem;
            letter-spacing: .14em;
            font-weight: 700;
            color: var(--brand-teal);
            background: rgba(20, 184, 166, .12);
            border: 1px solid rgba(20, 184, 166, .3);
            border-radius: 999px;
            padding: 6px 12px;
        }

        .assistant-side,
        .chat-card {
            border: 1px solid var(--line-soft);
            border-radius: 24px;
            background: color-mix(in srgb, var(--bg-card) 88%, #f8fcfd 12%);
            box-shadow: 0 25px 50px -35px rgba(5, 45, 56, 0.4);
        }

        .assistant-side {
            padding: 1.3rem;
        }

        .assistant-side h3 {
            margin: 0;
            font-size: 1.15rem;
            color: var(--ink-900);
            font-weight: 800;
        }

        .assistant-side p {
            margin: .55rem 0 1rem;
            color: var(--ink-700);
            font-size: .95rem;
        }

        .quick-prompts {
            display: grid;
            gap: .65rem;
        }

        .quick-prompt {
            text-align: start;
            border: 1px solid var(--line-soft);
            border-radius: 14px;
            background: #fff;
            padding: .8rem .9rem;
            color: #17323a;
            font-weight: 600;
            transition: .2s ease;
        }

        .quick-prompt:hover {
            transform: translateY(-1px);
            border-color: var(--brand-teal-2);
            box-shadow: 0 10px 18px -14px rgba(15, 118, 110, .8);
        }

        .assistant-note {
            margin-top: 1rem;
            border-radius: 14px;
            padding: .85rem .9rem;
            background: rgba(15, 118, 110, .08);
            color: #0d3d43;
            font-size: .88rem;
            display: flex;
            align-items: start;
            gap: .55rem;
        }

        .chat-card {
            padding: 1rem;
            display: flex;
            flex-direction: column;
            min-height: 620px;
        }

        .chat-head {
            border-bottom: 1px solid var(--line-soft);
            padding: .35rem .2rem .95rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
        }

        .chat-head h2 {
            margin: 0;
            color: var(--ink-900);
            font-size: 1.16rem;
            font-weight: 800;
        }

        .chat-head small {
            color: var(--ink-700);
        }

        .clear-btn {
            border: 1px solid var(--line-soft);
            background: #fff;
            color: var(--ink-700);
            border-radius: 10px;
            padding: .45rem .75rem;
            font-size: .88rem;
            font-weight: 700;
        }

        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 1rem .1rem;
            display: flex;
            flex-direction: column;
            gap: .75rem;
            min-height: 360px;
            max-height: 62vh;
        }

        .bubble {
            max-width: min(85%, 680px);
            border-radius: 16px;
            padding: .7rem .9rem;
            line-height: 1.6;
            white-space: pre-wrap;
            word-break: break-word;
            animation: rise .16s ease;
        }

        .bubble-agent {
            align-self: flex-start;
            background: #f4f9fa;
            border: 1px solid #d8e8ec;
            color: #16343e;
        }

        .bubble-user {
            align-self: flex-end;
            color: #fff;
            background: linear-gradient(135deg, var(--brand-teal) 0%, #0f9e90 100%);
            box-shadow: 0 10px 18px -14px rgba(15, 118, 110, .8);
        }

        .chat-form {
            border-top: 1px solid var(--line-soft);
            padding-top: .9rem;
            display: flex;
            gap: .65rem;
            align-items: flex-end;
        }

        .chat-form textarea {
            width: 100%;
            border: 1px solid var(--line-soft);
            border-radius: 14px;
            padding: .72rem .9rem;
            resize: none;
            max-height: 180px;
            background: #fff;
            color: #152f3b;
            font-size: .96rem;
        }

        .chat-form textarea:focus {
            outline: none;
            border-color: var(--brand-teal-2);
            box-shadow: 0 0 0 4px rgba(20, 184, 166, .16);
        }

        .chat-form button {
            border: 0;
            color: #fff;
            min-width: 98px;
            border-radius: 12px;
            padding: .7rem .9rem;
            font-weight: 700;
            background: linear-gradient(120deg, var(--brand-orange) 0%, #f97316 100%);
            box-shadow: 0 15px 28px -20px rgba(234, 88, 12, 1);
        }

        .chat-form button:disabled {
            opacity: .7;
        }

        .send-loader {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            border: 2px solid rgba(255, 255, 255, .5);
            border-top-color: #fff;
            display: inline-block;
            animation: spin .8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        @keyframes rise {
            from {
                opacity: 0;
                transform: translateY(4px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 991.98px) {
            .chat-card {
                min-height: 560px;
            }
            .chat-messages {
                max-height: 55vh;
            }
        }
    </style>
</body>

</html>
