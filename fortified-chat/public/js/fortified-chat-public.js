/**
 * JavaScript for the public-facing chat interface.
 *
 * @package FortifiedChat
 */

document.addEventListener('DOMContentLoaded', function () {
    const chatContainer = document.getElementById('fortified-chat-container');
    const chatHeader = document.getElementById('fortified-chat-header');
    const toggleButton = document.getElementById('fortified-chat-toggle-button');
    const openIcon = toggleButton ? toggleButton.querySelector('.dashicons-format-chat') : null;
    const closeIcon = toggleButton ? toggleButton.querySelector('.dashicons-no-alt') : null;

    const messagesContainer = document.getElementById('fortified-chat-messages');
    const inputField = document.getElementById('fortified-chat-input');
    const sendButton = document.getElementById('fortified-chat-send-button');

    let isOpen = false;

    // Ensure all elements are present before adding event listeners
    if (!chatContainer || !chatHeader || !toggleButton || !openIcon || !closeIcon || !messagesContainer || !inputField || !sendButton) {
        // console.error('Fortified Chat: One or more chat UI elements are missing.');
        // Don't display the chat if core elements are missing to prevent errors.
        if (chatContainer) {
            chatContainer.style.display = 'none';
        }
        return;
    }

    // Function to toggle chat visibility
    function toggleChat() {
        isOpen = !isOpen;
        if (isOpen) {
            chatContainer.classList.remove('fortified-chat-closed');
            chatContainer.classList.add('fortified-chat-open');
            openIcon.style.display = 'none';
            closeIcon.style.display = 'inline-block';
            toggleButton.setAttribute('aria-label', fortifiedChatPublic.i18n.closeChat);
            inputField.focus();
        } else {
            chatContainer.classList.add('fortified-chat-closed');
            chatContainer.classList.remove('fortified-chat-open');
            openIcon.style.display = 'inline-block';
            closeIcon.style.display = 'none';
            toggleButton.setAttribute('aria-label', fortifiedChatPublic.i18n.openChat);
        }
    }

    // Event listener for chat header/toggle button
    chatHeader.addEventListener('click', toggleChat);

    // Function to add a message to the chat
    function addMessage(text, sender) {
        const messageDiv = document.createElement('div');
        messageDiv.classList.add('fortified-chat-message', sender); // sender is 'user' or 'bot'

        const messageParagraph = document.createElement('p');
        messageParagraph.textContent = text; // Using textContent to prevent XSS

        messageDiv.appendChild(messageParagraph);
        messagesContainer.appendChild(messageDiv);
        messagesContainer.scrollTop = messagesContainer.scrollHeight; // Scroll to the bottom
    }

    // Function to handle sending a message
    function sendMessage() {
        const messageText = inputField.value.trim();
        if (messageText === '') {
            return;
        }

        addMessage(messageText, 'user');
        inputField.value = ''; // Clear input field

        // Simulate bot response (replace with actual AJAX call later)
        // For now, we'll use the localized strings if available, or fallback.
        const thinkingMsg = fortifiedChatPublic && fortifiedChatPublic.i18n && fortifiedChatPublic.i18n.botThinking ? fortifiedChatPublic.i18n.botThinking : 'Thinking...';
        addMessage(thinkingMsg, 'bot');

        // Prepare data for AJAX request
        const data = new URLSearchParams();
        data.append('action', 'fortified_chat_send_message');
        data.append('message', messageText);
        data.append('nonce', fortifiedChatPublic.nonce);


        // Make AJAX call to WordPress backend
        fetch(fortifiedChatPublic.ajaxUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: data,
        })
        .then(response => response.json())
        .then(response => {
            // Remove "Thinking..." message
            const thinkingMessageElement = Array.from(messagesContainer.querySelectorAll('.fortified-chat-message.bot p'))
                                            .find(el => el.textContent === thinkingMsg);
            if (thinkingMessageElement && thinkingMessageElement.parentElement) {
                thinkingMessageElement.parentElement.remove();
            }

            if (response.success && response.data && response.data.reply) {
                addMessage(response.data.reply, 'bot');
            } else {
                const errorMsg = fortifiedChatPublic && fortifiedChatPublic.i18n && fortifiedChatPublic.i18n.errorMessage ? fortifiedChatPublic.i18n.errorMessage : 'Sorry, something went wrong.';
                addMessage(errorMsg, 'bot');
            }
        })
        .catch(error => {
            // console.error('Error sending message:', error);
            const errorMsg = fortifiedChatPublic && fortifiedChatPublic.i18n && fortifiedChatPublic.i18n.errorMessage ? fortifiedChatPublic.i18n.errorMessage : 'Sorry, an error occurred.';
            addMessage(errorMsg, 'bot');
        });
    }

    // Event listener for send button
    sendButton.addEventListener('click', sendMessage);

    // Event listener for Enter key in input field
    inputField.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            sendMessage();
        }
    });

    // Initially, the chat should be closed, so reflect this in ARIA attributes and icons
    if (openIcon && closeIcon) { // Check if icons exist
        if (chatContainer.classList.contains('fortified-chat-closed')) {
            openIcon.style.display = 'inline-block';
            closeIcon.style.display = 'none';
            toggleButton.setAttribute('aria-label', fortifiedChatPublic.i18n.openChat);
        } else {
            openIcon.style.display = 'none';
            closeIcon.style.display = 'inline-block';
            toggleButton.setAttribute('aria-label', fortifiedChatPublic.i18n.closeChat);
        }
    }
});
