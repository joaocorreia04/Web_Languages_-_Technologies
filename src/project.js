//sell page javascript 
// Function to update subcategories and sizes based on the selected category
function updateSubcategoriesAndSizes(showDefaultValues = false) {
    var category = document.getElementById("category").value;
    var subcategoryGroup = document.getElementById("subcategory-group");
    var sizeGroup = document.getElementById("size-group");
    var subcategorySelect = document.getElementById("subcategory");
    var sizeSelect = document.getElementById("size");

    if (category === "" && showDefaultValues) {
        subcategorySelect.innerHTML = ""; // Clear previous options
        sizeSelect.innerHTML = ""; // Clear previous options
        populateSubcategories(subcategorySelect, ['', 'XS', 'S', 'M', 'L', 'XL', 'XXL', '1', '3', '5', '7', '9', '11', '13', '15']);
        populateSizes(sizeSelect, ['', 'kkk']);
        return;
    }

    // Hide size dropdown by default
    sizeGroup.style.display = "none";

    // Hide subcategory and size dropdowns if category is not selected
    if (category === "") {
        subcategoryGroup.style.display = "none";
        return;
    }

    // Show subcategory dropdown and populate options based on the selected category
    subcategoryGroup.style.display = "block";
    subcategorySelect.innerHTML = ""; // Clear previous options

    if (category === "Men") {
        console.log("Men category selected");

        populateSubcategories(subcategorySelect, ['Jeans', 'T-Shirts', 'Shirts', 'Pants'], showDefaultValues);
        // Show size dropdown for Men category
        sizeGroup.style.display = "block";
        sizeSelect.innerHTML = ""; // Clear previous options
        populateSizes(sizeSelect, ['XS', 'S', 'M', 'L', 'XL', 'XXL'], showDefaultValues);
    } else if (category === "Women") {
        console.log("Women category selected");
        populateSubcategories(subcategorySelect, ['Jeans', 'Tops', 'Dresses', 'Skirts'], showDefaultValues);
        // Show size dropdown for Women category
        sizeGroup.style.display = "block";
        sizeSelect.innerHTML = ""; // Clear previous options
        populateSizes(sizeSelect, ['XS', 'S', 'M', 'L', 'XL', 'XXL'], showDefaultValues);
    } else { // Children
        console.log("Children category selected");
        populateSubcategories(subcategorySelect, ['Jeans', 'T-Shirts', 'Dresses', 'Pants'], showDefaultValues);
        // Show size dropdown for Children category
        sizeGroup.style.display = "block";
        sizeSelect.innerHTML = ""; // Clear previous options
        populateSizes(sizeSelect, ['Baby', '3-4', '5-6', '7-8', '9-10', '11-12'], showDefaultValues);
    }
}



// Function to populate size options
function populateSizes(selectElement, options, showDefaultValues = false) {
    if (showDefaultValues) {
        var defaultOption = document.createElement("option");
        defaultOption.text = "";
        defaultOption.value = "";
        selectElement.appendChild(defaultOption);
    }
    options.forEach(option => {
        var optionElement = document.createElement("option");
        optionElement.text = option;
        optionElement.value = option;
        selectElement.appendChild(optionElement);
    });
}


// Function to populate subcategory options
function populateSubcategories(selectElement, options, showDefaultValues = false) {
    if (showDefaultValues) {
        var defaultOption = document.createElement("option");
        defaultOption.text = "";
        defaultOption.value = "";
        selectElement.appendChild(defaultOption);
    }
    options.forEach(option => {
        var optionElement = document.createElement("option");
        optionElement.text = option;
        optionElement.value = option;
        selectElement.appendChild(optionElement);
    });
}


function sendAjaxRequest(method, url, data, handler) {
    let request = new XMLHttpRequest();

    if (method === 'get') {
        request.open(method, url + '?' + encodeForAjax(data), true);
    } else {
        request.open(method, url, true);
    }
    request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    request.addEventListener('load', handler);

    if (['post', 'put'].includes(method)) {
        request.send(encodeForAjax(data));
    } else {
        request.send();
    }
}

function addEventListeners() {
    let itemDeleteButton = document.querySelectorAll('div.product button.remove-button');
    [].forEach.call(itemDeleteButton, function (deleter) {
        deleter.addEventListener('click', sendItemDeleteButtonRequest);
    });
    let wishlistDeleteButton = document.querySelectorAll('div.wishlist-item button.remove-button');
    [].forEach.call(wishlistDeleteButton, function (deleter) {
        deleter.addEventListener('click', sendWishlistDeleteButtonRequest);
    });
    let applyFiltersButton = document.getElementById("applyFiltersBtn");
    applyFiltersButton.addEventListener('click', applyFilters);
}

function sendItemDeleteButtonRequest() {
    let id = this.closest('div.product').getAttribute('data-id');
    sendAjaxRequest('DELETE', '/src/delete.php?id=' + id, null, ItemDeletedHandler);
}

function sendWishlistDeleteButtonRequest() {
    let id = this.closest('div.wishlist-item').getAttribute('data-id');
    sendAjaxRequest('DELETE', '/src/delete_from_wishlist.php?id=' + id, null, WishlistDeletedHandler);
}

function buyItem(itemId) {
    if (confirm("Are you sure you want to buy this item?")) {
        sendAjaxRequest('delete', '/src/delete.php?id=' + itemId, null, ItemBoughtHandler);
    }
}
function ItemBoughtHandler() {
    let item = JSON.parse(this.responseText);
    if (item.success) {
        alert('You have successfully purchased the item.' + '. You will receive an email with the receipt soon.');
        window.location.href = 'search_products.php?message=' + encodeURIComponent('You have successfully purchased ' + item.name + '. You will receive an email with the receipt soon.');
    } else {
        alert('Failed to purchase item: ' + item.message);
    }
}
function ItemDeletedHandler() {
    let item = JSON.parse(this.responseText);
    if (item.success) {
        let element = document.querySelector('div.product[data-id="' + item.item_id + '"]');
        element.remove();
    } else {
        alert(item.message);
    }
}

function WishlistDeletedHandler() {
    let item = JSON.parse(this.responseText);
    if (item.success) {
        let element = document.querySelector('div.wishlist-item[data-id="' + item.item_id + '"]');
        element.remove();
    } else {
        alert(item.message);
    }
}

function sendMessageCreateRequest(receiver, useDynamicField = false) {
    let message;
    if (useDynamicField) {
        message = document.getElementById(`message-${receiver}`).value;
    } else {
        message = document.getElementById("messageInput").value;
    }

    fetch('message.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams({
            'receiver': receiver,
            'message': message
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Clear the input field
            if (useDynamicField) {
                document.getElementById(`message-${receiver}`).value = '';
            } else {
                document.getElementById("messageInput").value = '';
            }

            // Optionally, update the message list dynamically without a full page refresh
            const messagesDiv = document.querySelector(`.chat-box[data-user="${receiver}"] .person-messages`);
            const newMessageDiv = document.createElement('div');
            newMessageDiv.classList.add('message');
            newMessageDiv.innerHTML = `
                <p><strong>From: </strong>${data.message.from_id}</p>
                <p><strong>Message: </strong>${data.message.content}</p>
                <p class="date"><strong>Date: </strong>${new Date(data.message.date).toLocaleString()}</p>
            `;
            messagesDiv.appendChild(newMessageDiv);
        } else {
            console.error('Message sending failed', data.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}




function messageCreateHandler() {
    //
}

addEventListeners();

function encodeForAjax(data) {
    if (data == null) return null;
    return Object.keys(data).map(function (k) {
        return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
    }).join('&');
}

function applyFilters() {
    let category = document.getElementById("category").value;
    let subcategory = document.getElementById("subcategory").value;
    let size = document.getElementById("size").value;
    let price = document.getElementById("priceSelect").value;

    const queryParams = {}

    if (category !== "") {
        queryParams.category = category;
    }
    if (subcategory !== "") {
        queryParams.sub_category = subcategory;
    }
    if (size !== "") {
        queryParams.size = size;
    }
    if (price !== "") {
        queryParams.price = price;
    }

    sendAjaxRequest('get', '/src/filter_products.php', queryParams, searchProductsHandler);
}

function searchProductsHandler() {
    // Parse the response
    var products = JSON.parse(this.responseText);

    // Get the products container
    var productsContainer = document.getElementById('content');

    //Clear the current products
    while (productsContainer.firstChild) {
        productsContainer.removeChild(productsContainer.firstChild);
    }
    // Add the new products
    for (let product of products) {
        // Create a new product element
        var productElement = document.createElement('div');
        productElement.className = 'product';
        productElement.dataset.id = product.item_id;

        // Add the product details
        productElement.innerHTML = `
            <a href="product_page.php?item_id=${product.item_id}">
                        <img src="${product.image_url}" alt="${product.name}">
                        <p><strong>${product.name}</strong></p>
                        <p>${product.price} EUR</p>
                    </a>`;
        // Add the remove button if the user is an admin
        if (isAdmin) {
            productElement.innerHTML += `<button class="remove-button">Delete</button>`;
        }

        // Add the product element to the products container
        productsContainer.appendChild(productElement);
    }
}


//Add to wishlist on product_page
function addToWishlist(itemId) {
    fetch('add_to_wishlist.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ item_id: itemId })
    })
        .then(response => response.json())
        .then(data => {
            if (data.length === 0) {
                alert('No items match your filter.');
            } else if (data.success) {
                alert('Item added to wishlist successfully!');
            } else {
                alert('Failed to add item to wishlist: ' + data.message);
            }
        })
        .catch(error => console.error('Error:', error));
}

function populate(product) {
    let categoryElement = document.getElementById("category");
    let subcategoryElement = document.getElementById("subcategory");
    let sizeElement = document.getElementById("size");
    let conditionElement = document.getElementById("condition");

    categoryElement.value = product.category;

    updateSubcategoriesAndSizes();

    subcategoryElement.value = product.sub_category;
    sizeElement.value = product.size;
    conditionElement.value = product.condition;
}

function setAdmin() {
    
}
