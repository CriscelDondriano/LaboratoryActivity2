<?php
session_start();
include 'db.php'; // Ensure your database connection is included

// Load the current section from the GET parameters or default to 'home'
$section = $_GET['section'] ?? 'home';

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? '';

    if ($action == 'add') {
        $name = $_POST['name'];
        $description = $_POST['description'];
        $price = $_POST['price'];

        $sql = "INSERT INTO products (name, description, price) VALUES ('$name', '$description', '$price')";
        
        if ($conn->query($sql) === TRUE) {
            $_SESSION['message'] = "Product added successfully!"; // Set success message
            header("Location: Backend.php?section=add-product-section"); // Redirect after adding
            exit();
        } else {
            $_SESSION['message'] = "Error adding product: " . $conn->error; // Optional error message
        }
    } elseif ($action == 'update') {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $description = $_POST['description'];
        $price = $_POST['price'];

        $sql = "UPDATE products SET name='$name', description='$description', price='$price' WHERE id='$id'";
        
        if ($conn->query($sql) === TRUE) {
            $_SESSION['message'] = "Product updated successfully!";
            header("Location: Backend.php?section=available-products-section"); // Redirect after updating
            exit();
        }
    } elseif ($action == 'delete') {
        $id = $_POST['id'];

        $sql = "DELETE FROM products WHERE id=$id";
        
        if ($conn->query($sql) === TRUE) {
            $_SESSION['message'] = "Product deleted successfully!";
            header("Location: Backend.php?section=available-products-section"); // Redirect after deleting
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flower Shop - CRUD Operations</title>
    <link rel="stylesheet" href="style.css">
    <script>
        function showSection(sectionId) {
            document.querySelectorAll('section').forEach(function(section) {
                section.style.display = 'none'; // Hide all sections
            });
            document.getElementById(sectionId).style.display = 'block'; // Show selected section
        }

        function showEditForm(id, name, description, price) {
            document.getElementById('edit-id').value = id;
            document.getElementById('edit-name').value = name;
            document.getElementById('edit-description').value = description;
            document.getElementById('edit-price').value = price;
            showSection('edit-product-section'); // Show edit section
        }

        window.onload = function() {
            const urlParams = new URLSearchParams(window.location.search);
            const sectionId = urlParams.get('section') || 'home'; // Default to home
            showSection(sectionId); // Show the section based on the URL
        };
    </script>
</head>
<body>

    <nav>
        <a href="Backend.php?section=home">Home</a>
        <a href="Backend.php?section=add-product-section">Add New Product</a>
        <a href="Backend.php?section=available-products-section">Available Products</a>
    </nav>

    <section id="home">
        <h1>Welcome Flower Shop - Product Management</h1>
        <p>Manage your flower shop products by adding new items and viewing the inventory.</p>
    </section>

    <section id="add-product-section" style="display: none;">
        <h2>Add New Product</h2>
        <div class="add-and-table-container">
            <div class="form-container">
                <?php
                // Display success message if available
                if (isset($_SESSION['message'])) {
                    echo "<div class='alert'>" . $_SESSION['message'] . "</div>";
                    unset($_SESSION['message']); // Clear the message after displaying it
                }
                ?>

                <div class="form-section-container"> <!-- Added container for styling -->
                    <form method="POST" action="Backend.php">
                        <input type="hidden" name="action" value="add">
                        <label for="name">Product Name:</label><br>
                        <input type="text" id="name" name="name" required><br>
                        <label for="description">Description:</label><br>
                        <textarea id="description" name="description" rows="4" required></textarea><br>
                        <label for="price">Price:</label><br>
                        <input type="text" id="price" name="price" required><br><br>
                        <input type="submit" value="Add Product">
                    </form>
                </div> <!-- End of container -->
            </div>
            <div class="table-container">
                <?php
                $sql = "SELECT * FROM products";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    echo "<table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Price</th>
                                </tr>
                            </thead>
                            <tbody>";
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['id']}</td>
                                <td>{$row['name']}</td>
                                <td>{$row['description']}</td>
                                <td>{$row['price']}</td>
                              </tr>";
                    }
                    echo "</tbody></table>";
                } else {
                    echo "<p>No records found</p>";
                }
                ?>
            </div>
        </div>
    </section>

    <section id="available-products-section" style="display: none;">
        <h2>All Products</h2>
        <div class="table-container">
            <?php
            $sql = "SELECT * FROM products";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                echo "<table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Price</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>";
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['name']}</td>
                            <td>{$row['description']}</td>
                            <td>{$row['price']}</td>
                            <td>
                                <button onclick=\"showEditForm({$row['id']}, '{$row['name']}', '{$row['description']}', '{$row['price']}')\">Edit</button> 
                                <form method='POST' action='Backend.php' style='display:inline;'>
                                    <input type='hidden' name='action' value='delete'>
                                    <input type='hidden' name='id' value='{$row['id']}'>
                                    <input type='submit' class='delete-button' value='Delete'onclick=\"return confirm('Are you sure you want to delete this product?')\">
                                </form>
                            </td>
                          </tr>";
                }
                echo "</tbody></table>";
            } else {
                echo "<p>No records found</p>";
            }
            ?>
        </div>
    </section>

    <section id="edit-product-section" style="display: none;">
        <h2>Edit Product</h2>
        <div class="form-container">
            <div class="form-section-container"> <!-- Added container for styling -->
                <form method="POST" action="Backend.php" onsubmit="return confirm('Are you sure you want to update this product?')">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" id="edit-id" name="id">
                    <label for="edit-name">Product Name:</label><br>
                    <input type="text" id="edit-name" name="name" required><br>
                    <label for="edit-description">Description:</label><br>
                    <textarea id="edit-description" name="description" rows="4" required></textarea><br>
                    <label for="edit-price">Price:</label><br>
                    <input type="text" id="edit-price" name="price" required><br><br>
                    <input type="submit" value="Update Product">
                </form>
            </div> <!-- End of container -->
        </div>
    </section>

</body>
</html>
