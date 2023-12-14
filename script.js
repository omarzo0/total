document.getElementById("myForm").addEventListener("submit", function(event) {
    event.preventDefault(); // Prevent form submission
  
    // Get form input values
    var name = document.getElementById("oil-type").value;
    var email = document.getElementById("start-balance").value;
    var phone = document.getElementById("end-balance").value;
    var phone = document.getElementById("inventory-In").value;
  
    // Create a new row in the table
    var table = document.getElementById("dataTable");
    var row = table.insertRow(-1);
  
    // Insert the form data into the table cells
    var cell1 = row.insertCell(0);
    var cell2 = row.insertCell(1);
    var cell3 = row.insertCell(2);
    var cell3 = row.insertCell(3);
  
    cell1.innerHTML = oil-type;
    cell2.innerHTML = start-balance;
    cell3.innerHTML = end-balance;
    cell4.innerHTML = inventory-In;
  
    // Reset the form fields
    document.getElementById("myForm").reset();
  });
  
  const btn = document.getElementById('btn');

btn.addEventListener('click', function onClick() {
  btn.style.backgroundColor = 'salmon';
  btn.style.color = 'white';
});
