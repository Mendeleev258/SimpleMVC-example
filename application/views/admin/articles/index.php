<h1><?php echo $pageTitle; ?></h1>

<p><a href="/index.php?route=admin/articles/add">Add New Article</a></p>

<table border="1">

<tr><th>Title</th><th>Category</th><th>Subcategory</th><th>Publication Date</th><th>Active</th><th>Actions</th></tr>

<?php foreach ($articles as $article): ?>

<tr>

<td><?php echo htmlspecialchars($article->title); ?></td>

<td><?php echo isset($categories[$article->categoryId]) ? htmlspecialchars($categories[$article->categoryId]->name) : 'None'; ?></td>

<td><?php echo isset($subcategories[$article->subcategoryId]) ? htmlspecialchars($subcategories[$article->subcategoryId]->name) : 'None'; ?></td>

<td><?php echo date("Y-m-d", $article->publicationDate); ?></td>

<td><?php echo $article->active ? 'Yes' : 'No'; ?></td>

<td>

<a href="/index.php?route=admin/articles/edit&id=<?php echo $article->id; ?>">Edit</a> |

<a href="/index.php?route=admin/articles/delete&id=<?php echo $article->id; ?>" onclick="return confirm('Delete this article?')">Delete</a>

</td>

</tr>

<?php endforeach; ?>

</table>