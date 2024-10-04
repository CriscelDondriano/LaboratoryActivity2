<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flower Shop - CRUD Operations</title>
    <link rel="stylesheet" href="styles.css">
    <script>
        function showEditForm(id, name, description, price) {
            document.getElementById('edit-id').value = id;
            document.getElementById('edit-name').value = name;
            document.getElementById('edit-description').value = description;
            document.getElementById('edit-price').value = price;
            document.getElementById('edit-form').style.display = 'block';
            window.scrollTo(0, document.getElementById('edit-form').offsetTop); /* Scroll to form */
        }
    </script>
</head>
<body>
    <h1>Flower Shop - Product Management</h1>

    <!-- Form to Add New Product -->
    <form method="POST" action="Backend.php">
        <h2>Add New Product</h2>
        <input type="hidden" name="action" value="add">
        <label for="name">Product Name:</label><br>
        <input type="text" id="name" name="name" placeholder="Enter flower name" required><br>
        <label for="description">Description:</label><br>
        <textarea id="description" name="description" placeholder="Describe the flower" rows="4" required></textarea><br>
        <label for="price">Price:</label><br>
        <input type="text" id="price" name="price" placeholder="Enter price in PHP" required><br><br>
        <input type="submit" value="Add Product">
    </form>

    <!-- Form to Edit Product -->
    <div id="edit-form">
        <h2>Edit Product</h2>
        <form method="POST" action="Backend.php">
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
    </div>

    <!-- Display Products -->
    <?php
    include 'db.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $action = $_POST['action'];

        if ($action == "add") {
            $name = $_POST['name'];
            $description = $_POST['description'];
            $price = $_POST['price'];

            $sql = "INSERT INTO products (name, description, price) VALUES ('$name', '$description', '$price')";
            $conn->query($sql) ? print("New product added successfully") : print("Error: " . $conn->error);
        } elseif ($action == "update") {
            $id = $_POST['id'];
            $name = $_POST['name'];
            $description = $_POST['description'];
            $price = $_POST['price'];

            $sql = "UPDATE products SET name='$name', description='$description', price='$price' WHERE id=$id";
            $conn->query($sql) ? print("Product updated successfully") : print("Error: " . $conn->error);
        } elseif ($action == "delete") {
            $id = $_POST['id'];
            $sql = "DELETE FROM products WHERE id=$id";
            $conn->query($sql) ? print("Product deleted successfully") : print("Error: " . $conn->error);
        }
    }

    $sql = "SELECT * FROM products";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<h2>Available Products</h2>";
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
                        <button onclick=\"showEditForm({$row['id']}, '{$row['name']}', '{$row['description']}', '{$row['price']}')\">Edit</button> |
                        <form method='POST' action='Backend.php' style='display:inline;'>
                            <input type='hidden' name='action' value='delete'>
                            <input type='hidden' name='id' value='{$row['id']}'>
                            <input type='submit' value='Delete'>
                        </form>
                    </td>
                  </tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<p>No records found</p>";
    }

    $conn->close();
    ?>

</body>
</html>
