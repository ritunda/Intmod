 <?php
// Handle form submissions before any HTML output
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add new category
    if (!empty($_POST['categoryName'])) {
        $newCategory = htmlspecialchars(trim($_POST['categoryName']));
        if (!empty($newCategory)) {
            file_put_contents('categories.txt', $newCategory . PHP_EOL, FILE_APPEND | LOCK_EX);
            header("Location: " . $_SERVER['PHP_SELF']); // Redirect to avoid resubmission
            exit;
        }
    }

    // Add new book
    if (!empty($_POST['bookTitle']) && !empty($_POST['bookLink']) && !empty($_POST['bookCategory'])) {
        $title = htmlspecialchars(trim($_POST['bookTitle']));
        $link = htmlspecialchars(trim($_POST['bookLink']));
        $category = htmlspecialchars(trim($_POST['bookCategory']));

        if (!empty($title) && !empty($link) && !empty($category)) {
            $newBook = "$title|$link|$category\n";
            file_put_contents('books.txt', $newBook, FILE_APPEND | LOCK_EX);
            header("Location: " . $_SERVER['PHP_SELF']); // Redirect to avoid resubmission
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Sharing Platform</title>
    <!-- Tailwind CSS via CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-gray-100 text-gray-800">

<header class="bg-white shadow p-5 flex justify-between items-center">
    <div class="flex items-center">
        <img src="logo.png" alt="Logo" class="h-10 mr-3">
        <span class="text-2xl font-bold">LinkedIn Coin</span>
    </div>
    <div>
        <span class="text-sm">"A tagline or symbol here"</span>
    </div>
</header>

<main class="container mx-auto p-5">
    <section class="mb-8">
        <h2 class="text-xl font-semibold mb-4">Boards List</h2>
        <div class="grid grid-cols-3 gap-4">
            <?php
            // Load categories from the text file
            $categories = file('categories.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $books = file('books.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $booksByCategory = [];

            // Organize books by categories
            foreach ($books as $book) {
                list($title, $link, $category) = explode('|', $book);
                $booksByCategory[$category][] = ['title' => $title, 'link' => $link];
            }

            foreach ($categories as $category) {
                echo "<div class='bg-white p-4 shadow rounded hover:bg-gray-50 cursor-pointer'>
                        <h3 class='font-bold'>$category</h3>";
                if (isset($booksByCategory[$category])) {
                    echo "<ul class='mt-2'>";
                    foreach ($booksByCategory[$category] as $book) {
                        echo "<li><a href='{$book['link']}' class='text-blue-500 hover:underline'>{$book['title']}</a></li>";
                    }
                    echo "</ul>";
                } else {
                    echo "<p class='text-gray-500'>No books available.</p>";
                }
                echo "</div>";
            }
            ?>
        </div>
    </section>

    <section class="mb-8">
        <h2 class="text-xl font-semibold mb-4">Add a New Category</h2>
        <form method="POST" action="">
            <input type="text" name="categoryName" placeholder="Enter category name" required class="w-full p-2 mb-4 rounded bg-gray-200 border border-gray-300">
            <button type="submit" class="w-full p-2 bg-blue-500 hover:bg-blue-400 rounded text-white">Add Category</button>
        </form>
    </section>

    <section class="mb-8">
        <h2 class="text-xl font-semibold mb-4">Add a New Book</h2>
        <form method="POST" action="">
            <input type="text" name="bookTitle" placeholder="Enter book title" required class="w-full p-2 mb-4 rounded bg-gray-200 border border-gray-300">
            <input type="url" name="bookLink" placeholder="Enter PDF link" required class="w-full p-2 mb-4 rounded bg-gray-200 border border-gray-300">
            <select name="bookCategory" class="w-full p-2 mb-4 rounded bg-gray-200 border border-gray-300" required>
                <option value="">Select Category</option>
                <?php
                foreach ($categories as $category) {
                    echo "<option value='$category'>$category</option>";
                }
                ?>
            </select>
            <button type="submit" class="w-full p-2 bg-blue-500 hover:bg-blue-400 rounded text-white">Add Book</button>
        </form>
    </section>
</main>

</body>
</html>