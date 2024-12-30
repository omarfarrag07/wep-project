document.addEventListener('DOMContentLoaded',()=>{
    const flightDropdown=document.getElementById('flight');
    const messageForm=document.getElementById('sendMessageForm');

    fetch('../../backend/companies/GetAllFlights.php')
    .then((response)=>response.json())
    .then((flights)=>{
        // console.log('Flights:', flights); // Add this for debugging
        flights.forEach((flight) =>{
            const option =document.createElement('option');
            option.value=flight.id;

            
            option.textContent=`${flight.name} (${flight.takeoff} ->${flight.destination})`;
            flightDropdown.appendChild(option);
        });
    });

    messageForm.addEventListener('submit',(e)=>{
        e.preventDefault();


        const flightId=flightDropdown.value;
        const content =document.getElementById('messageContent').value;

        // console.log('Selected Flight ID:', flightId); // Debugging
            // console.log('Message Content:', content); // Debugging

        fetch('../../backend/messaging/send.php' ,{
            method: 'POST',
            headers:{
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({flight_id: flightId,content}),
        })
        .then((response)=>response.json())
        .then((result)=>{
            // console.log('Response from send.php:', result); // Debugging

            alert(result.success || result.error);
        })
        .catch((error) => console.error('Error sending message:', error));
    });
});