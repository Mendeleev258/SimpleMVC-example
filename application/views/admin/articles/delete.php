<h1>Delete Article</h1>

<p>Are you sure you want to delete the article "<?php echo htmlspecialchars($article->title); ?>"?</p>

<form action="" method="post">

<input type="submit" name="deleteArticle" value="Delete Article" />

<input type="submit" name="cancel" value="Cancel" />

</form>