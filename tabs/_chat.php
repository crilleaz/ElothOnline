<div id="chat">
    <div id="chat-messages">Loading</div>
    <input type="text" id="new-chat-msg" name="message" placeholder="Write something.." autofocus><br>
</div>

<script>
    <?php
        /** @var \Game\Player\Player $player */
        echo 'const isAdmin = '. ($player->isAdmin() ? 'true;': 'false;');
    ?>

    function banUser(username) {
        if (!isAdmin) {
            return;
        }

        const xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.status !== 200 || this.readyState !== 4) {
                return;
            }

            const response = JSON.parse(this.responseText);
            if (!response.success) {
                alert(response.data['error']);
                return;
            }

            alert('User has been banned')

            refreshChat();
        };

        xhttp.open('POST', '/api.php?action=ban', true);
        xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhttp.send('username=' + username);
    }

    function sendMessageToChat(message) {
        if (message === '') {
            alert('Message is empty');

            return;
        }

        const xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.status !== 200 || this.readyState !== 4) {
                return;
            }

            const response = JSON.parse(this.responseText);
            if (!response.success) {
                alert(response.data['error']);
                return;
            }

            refreshChat();
        };
        xhttp.open('POST', '/api.php?action=addChatMessage', true);
        xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhttp.send('message=' + message);
    }

    function refreshChat() {
        const xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.status !== 200 || this.readyState !== 4) {
                return;
            }

            const response = JSON.parse(this.responseText);
            if (!response.success) {
                alert(response.data['error']);
                return;
            }

            let messagesContent = '';
            for(const message of response.data) {
                const sentAt = new Date(message.sentAt);
                let sender;
                if (message.isFromAdmin) {
                    sender = '<font color="red">[' + message.sender + ']:</font>';
                } else {
                    sender = '<font title="Ban this user" color="blue" onclick="banUser(\'' + message.sender + '\')">[' + message.sender + ']:</font>';
                }

                messagesContent += '[' + sentAt.toLocaleString() + ']' + sender + ' ' + message.message + '<br>';
            }

            document.getElementById('chat-messages').innerHTML = messagesContent;
        };
        xhttp.open('GET', '/api.php?action=getChatMessages', true);
        xhttp.send();
    }

    window.addEventListener("load", () => {
        setInterval(refreshChat, 3000);

        const newMsgInputField = document.getElementById('new-chat-msg');
        newMsgInputField.addEventListener('keypress', function(event) {
            if (event.code !== 'Enter') {
                return;
            }

            sendMessageToChat(this.value)
            this.value = '';
        });
    });
</script>