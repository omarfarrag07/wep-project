document.addEventListener('DOMContentLoaded',()=>{
    fetch('../../backend/passengers/get_passenger_dashboard.php')
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
        if (!currentFlightsBody ) {
            console.error('Table body elements are missing.');
            return;
        }
        data.current_flights.forEach(flight => {
            const row=document.createElement('tr');
            row.innerHTML=`
            <td>${flight.id}</td>
            <td>${flight.name}</td>
            <td>${flight.itinerary}</td>
            <td>${flight.start_time}</td>
            <td>${flight.fees}</td>
            `;
            currentFlightsBody.appendChild(row);
        });


        const completedFlightsBody=document.getElementById('completed-flights-body');
        if (!completedFlightsBody) {
            console.error('Table body elements are missing.');
            return;
        }
        data.completed_flights.forEach(flight=>{
            const row=document.createElement('tr');
            row.innerHTML=`
                 <td>${flight.id}</td>
                <td>${flight.name}</td>
                <td>${flight.itinerary}</td>
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