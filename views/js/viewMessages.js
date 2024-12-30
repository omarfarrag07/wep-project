document.addEventListener('DOMContentLoaded',()=>{
    const messageList= document.getElementById('messagesList');

    fetch('../../backend/messaging/recieve.php')
        .then((response)=>response.json())
            .then((messages)=>{
                if (messages.length===0) {
                    messageList.innerHTML='<li>No message found</li>'
                }else{
                    messages.forEach((message)=>{
                        const li=document.createElement('li');
                        li.innerHTML=`
                           <strong>Flight:</strong>${message.flight_name }<br>
                           <strong>Message:</strong> ${message.content}<br>
                           <strong>Sent At:</strong> ${message.sent_at}
                        
                        `;
                        messageList.appendChild(li);
                    });
                }
            });
});