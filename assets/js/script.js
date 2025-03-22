async function sendMessage() {
	const userMessage = document.querySelector('#input-area input').value;
	if (!userMessage) return;

	const chatBox = document.getElementById('chat-box');
	const userMessageDiv = document.createElement('div');
	userMessageDiv.classList.add('message', 'user-message');
	userMessageDiv.textContent = userMessage;
	chatBox.appendChild(userMessageDiv);

	document.querySelector('#input-area input').value = '';
	chatBox.scrollTop = chatBox.scrollHeight

	const response = await getBotResponse(userMessage);

	const botMessageDiv = document.createElement('div');
	botMessageDiv.classList.add('message', 'bot-message');
	botMessageDiv.textContent = response;
	chatBox.appendChild(botMessageDiv);

	chatBox.scrollTop = chatBox.scrollHeight;
}

async function getBotResponse(userMessage) {
	const response = await fetch('backend/chat.php', {
		method: 'POST',
		headers: {
		'Content-Type': 'application/json',
		},
		body: JSON.stringify({ message: userMessage })
	});

	const data = await response.json();
	return data.response || 'No response from bot'; 
}

document.querySelector('#input-area button').addEventListener('click', sendMessage);

document.querySelector('#input-area input').addEventListener('keypress', function(event) {
	if (event.key === 'Enter') {
		sendMessage();
	}
});
