document.addEventListener('DOMContentLoaded',()=>{
    fetch('/backend/passenger/get_passenger_dashboard.php')
    .then(response=>{
        if(!response.ok){
            throw new Error("Faild to fetch dashboard");
            
        }
        return response.json();
    })
    .then(data=>{
        if(data.error){
            alert(data.error);
            return;
        }

        const currentFlightsBody=document.getElementById('current-flights-body');
        data.current_flights.array.forEach(flight => {
            const row=document.createElement('tr');
            row.innerHTML=`
            <td>${flight.id}</td>
            <td>${flight.name}</td>
            <td>${flight.intinerary}</td>
            <td>${flight.start_time}</td>
            <td>${flight.fees}</td>
            `;
            currentFlightsBody.appendChild(row);
        });


        const completedFlightsBody=document.getElementById('completed-flights-body');
        data.completed_flights.forEach(flight=>{
            const row=document.createElement('tr');
            row.innerHTML=`
                 <td>${flight.id}</td>
                <td>${flight.name}</td>
                <td>${flight.intinerary}</td>
                <td>${flight.start_time}</td>
                <td>${flight.fees}</td>
            `;
            completedFlightsBody.appendChild(row);
        });
    })
    .catch(error=>{
        console.error('Error fetching dashboard data:',error);
        alert('error loading dashboard. Please try again.');
    });

});