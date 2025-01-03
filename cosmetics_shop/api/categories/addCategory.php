<?php
    session_start();

    // Inclure la connexion à la base de données
    if (file_exists('../../../config/dbConnect.php')) {
        require '../../../config/dbConnect.php';
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Fichier de configuration introuvable']);
        exit;
    }

    // Lire et décoder les données JSON envoyées
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Vérifier que les données nécessaires sont fournies
    if (isset($data['categoryName'])) {
        $categoryName = mysqli_real_escape_string($link, $data['categoryName']);
        $subcategories = isset($data['subcategories']) && is_array($data['subcategories']) ? $data['subcategories'] : [];

        // Vérifier si la catégorie existe déjà
        $queryCheckCategory = "SELECT id FROM categories WHERE category_name = ?";
        $stmtCheckCategory = mysqli_prepare($link, $queryCheckCategory);

        if ($stmtCheckCategory) {
            mysqli_stmt_bind_param($stmtCheckCategory, 's', $categoryName);
            mysqli_stmt_execute($stmtCheckCategory);
            mysqli_stmt_store_result($stmtCheckCategory);

            $categoryId = null;

            if (mysqli_stmt_num_rows($stmtCheckCategory) > 0) {
                // Si la catégorie existe déjà, récupérer son ID
                mysqli_stmt_bind_result($stmtCheckCategory, $categoryId);
                mysqli_stmt_fetch($stmtCheckCategory);
            } else {
                // Si la catégorie n'existe pas, l'ajouter
                $queryInsertCategory = "INSERT INTO categories (category_name) VALUES (?)";
                $stmtInsertCategory = mysqli_prepare($link, $queryInsertCategory);

                if ($stmtInsertCategory) {
                    mysqli_stmt_bind_param($stmtInsertCategory, 's', $categoryName);

                    if (mysqli_stmt_execute($stmtInsertCategory)) {
                        $categoryId = mysqli_insert_id($link);
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'Erreur lors de l\'ajout de la catégorie']);
                        mysqli_stmt_close($stmtInsertCategory);
                        exit;
                    }
                    mysqli_stmt_close($stmtInsertCategory);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Erreur de préparation pour l\'ajout de la catégorie']);
                    exit;
                }
            }
            mysqli_stmt_close($stmtCheckCategory);

            // Gestion des sous-catégories
            $errors = [];
            foreach ($subcategories as $subcategoryName) {
                $subcategoryName = mysqli_real_escape_string($link, $subcategoryName);

                // Vérifier si la sous-catégorie existe déjà
                $queryCheckSubcategory = "SELECT id FROM subcategories WHERE subcategory_name = ? AND category_id = ?";
                $stmtCheckSubcategory = mysqli_prepare($link, $queryCheckSubcategory);

                if ($stmtCheckSubcategory) {
                    mysqli_stmt_bind_param($stmtCheckSubcategory, 'si', $subcategoryName, $categoryId);
                    mysqli_stmt_execute($stmtCheckSubcategory);
                    mysqli_stmt_store_result($stmtCheckSubcategory);

                    if (mysqli_stmt_num_rows($stmtCheckSubcategory) > 0) {
                        $errors[] = "La sous-catégorie '$subcategoryName' existe déjà pour cette catégorie";
                        mysqli_stmt_close($stmtCheckSubcategory);
                        continue;
                    }

                    mysqli_stmt_close($stmtCheckSubcategory);
                } else {
                    $errors[] = "Erreur de préparation pour la vérification de la sous-catégorie '$subcategoryName'";
                    continue;
                }

                // Ajouter la sous-catégorie
                $queryInsertSubcategory = "INSERT INTO subcategories (subcategory_name, category_id) VALUES (?, ?)";
                $stmtInsertSubcategory = mysqli_prepare($link, $queryInsertSubcategory);

                if ($stmtInsertSubcategory) {
                    mysqli_stmt_bind_param($stmtInsertSubcategory, 'si', $subcategoryName, $categoryId);

                    if (!mysqli_stmt_execute($stmtInsertSubcategory)) {
                        $errors[] = "Erreur lors de l'ajout de la sous-catégorie '$subcategoryName'";
                    }

                    mysqli_stmt_close($stmtInsertSubcategory);
                } else {
                    $errors[] = "Erreur de préparation pour l'ajout de la sous-catégorie '$subcategoryName'";
                }
            }

            // Préparer la réponse
            if (empty($errors)) {
                echo json_encode(['status' => 'success', 'message' => 'Catégorie et sous-catégories ajoutées avec succès']);
            } else {
                echo json_encode(['status' => 'partial_success', 'message' => 'Certaines sous-catégories n\'ont pas pu être ajoutées', 'errors' => $errors]);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erreur de préparation pour la vérification de la catégorie']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Nom de la catégorie manquant']);
    }

    // Fermer la connexion à la base de données
    mysqli_close($link);
?>
