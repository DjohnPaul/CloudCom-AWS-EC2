function removeFromOrder(foodId) {
    if (confirm("Are you sure you want to remove this item?")) {
        fetch('RemoveFromOrder.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'foodId=' + encodeURIComponent(foodId)
        })
        .then(response => response.text())
        .then(data => {
            alert(data);
            document.getElementById('row_' + foodId).remove();
            updateTotal();
        })
        .catch(error => console.error("Error removing from order:", error));
    }
}

function updateTotal() {
    let total = 0;
    document.querySelectorAll("tbody tr").forEach(row => {
        let price = parseFloat(row.children[4].textContent.replace('₱', '')) || 0;
        total += price;
    });
    document.getElementById("totalAmount").textContent = total.toFixed(2);
}

function updateQuantity(foodId, action) {
    fetch("UpdateQuantity.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `foodId=${foodId}&action=${action}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === "success") {
            
            document.getElementById(`qty_${foodId}`).textContent = data.quantity;

            let row = document.getElementById(`row_${foodId}`);
            let pricePerUnit = parseFloat(row.children[2].textContent.replace('₱', '')); 
            let newTotalPrice = pricePerUnit * data.quantity;

            row.children[4].textContent = `₱${newTotalPrice.toFixed(2)}`; 

            updateTotal();
        } else {
            alert("Error: " + data.message);
        }
    })
    .catch(error => console.error("Error updating quantity:", error));
}