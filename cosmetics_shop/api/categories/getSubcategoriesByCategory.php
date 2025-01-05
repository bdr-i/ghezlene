<?php
    // Connect to the database
    require '../../../config/dbConnect.php';

    // Get the category ID from the query parameter
    $category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;

    // Initialize the response array
    $subcategories = [];

    // Check if a valid category ID is provided
    if ($category_id > 0) {
        // Query to fetch subcategories for the given category
        $query = "SELECT 
                    id AS subcategory_id, 
                    subcategory_name 
                  FROM 
                    subcategories 
                  WHERE 
                    category_id = ?";

        // Prepare the statement
        if ($stmt = mysqli_prepare($link, $query)) {
            // Bind the parameter and execute the query
            mysqli_stmt_bind_param($stmt, 'i', $category_id);
            mysqli_stmt_execute($stmt);

            // Get the result
            $result = mysqli_stmt_get_result($stmt);

            // Fetch the subcategories
            while ($row = mysqli_fetch_assoc($result)) {
                $subcategories[] = [
                    'id' => $row['subcategory_id'],
                    'subcategory_name' => $row['subcategory_name']
                ];
            }

            // Close the statement
            mysqli_stmt_close($stmt);
        }
    }

    // Return the subcategories as JSON
    echo json_encode($subcategories);

    // Close the database connection
    mysqli_close($link);
?>
