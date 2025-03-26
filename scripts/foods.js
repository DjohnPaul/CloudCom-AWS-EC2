document.addEventListener("DOMContentLoaded", function () {
    fetchFoods(); // Load all foods initially
    
    // Search event listener
    document.getElementById("searchInput").addEventListener("input", function () {
        fetchFoods(this.value, getSelectedCategory());
    });

    // Category button event listeners
    document.querySelectorAll(".category-btn").forEach(button => {
        button.addEventListener("click", function () {
            document.querySelectorAll(".category-btn").forEach(btn => btn.classList.remove("active"));
            this.classList.add("active");

            let category = this.getAttribute("data-category");
            fetchFoods(document.getElementById("searchInput").value, category);
        });
    });
});

function fetchFoods(search = "", category = "") {
    let url = `FetchFoods.php?search=${encodeURIComponent(search)}&category=${encodeURIComponent(category)}`;

    fetch(url)
        .then(response => response.json())
        .then(foods => {
            displayFoods(foods);
        })
        .catch(error => console.error("Error fetching food data:", error));
}

function displayFoods(foods) {
    let container = document.getElementById("foodGrid");
    container.innerHTML = ""; // Clear existing content

    if (foods.length === 0) {
        container.innerHTML = "<p>No matching foods found.</p>";
        return;
    }

    foods.forEach(food => {
        let foodItem = document.createElement("div");
        foodItem.classList.add("food-item");

        foodItem.innerHTML = `
            <img src="images/${food.category}/${food.imageName}" alt="${food.name}">
            <div class="food-item-name">${food.name}</div>
            <div class="food-item-description">${food.description}</div>
            <div class="food-item-price">₱${parseFloat(food.price).toFixed(2)}</div>
            <button class="add-to-order-btn" onclick="addToOrder('${food.foodId}')">Add to Order</button>
        `;

        container.appendChild(foodItem);
    });
}

function getSelectedCategory() {
    let activeButton = document.querySelector(".category-btn.active");
    return activeButton ? activeButton.getAttribute("data-category") : "";
}

function addToOrder(foodId) {
    fetch("AddtoOrder.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded",
        },
        body: `foodId=${encodeURIComponent(foodId)}`,
    })
    .then(response => response.text())
    .then(data => {
        alert(data); // Show response message
    })
    .catch(error => console.error("Error adding to cart:", error));
}
