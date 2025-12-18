<h1><?php echo $pageTitle; ?></h1>

<?php if (isset($errors) && is_array($errors) && count($errors) > 0): ?>

<ul>

<?php foreach ($errors as $error): ?>

<li><?php echo $error; ?></li>

<?php endforeach; ?>

</ul>

<?php endif; ?>

<form action="" method="post">

<input type="hidden" name="articleId" value="<?php echo $article->id; ?>" />

<ul>

<li>

<label for="title">Article Title</label>

<input type="text" name="title" id="title" placeholder="Name of the article" required autofocus maxlength="255" value="<?php echo htmlspecialchars($article->title); ?>" />

</li>

<li>

<label for="summary">Article Summary</label>

<textarea name="summary" id="summary" placeholder="Brief description of the article" required maxlength="1000" style="height: 5em;"><?php echo htmlspecialchars($article->summary); ?></textarea>

</li>

<li>

<label for="content">Article Content</label>

<textarea name="content" id="content" placeholder="The HTML content of the article" required maxlength="100000" style="height: 30em;"><?php echo htmlspecialchars($article->content); ?></textarea>

</li>

<li>

<label for="categoryId">Article Category</label>

<select name="categoryId">

<option value="0"<?php echo !$article->categoryId ? " selected" : ""; ?>>(none)</option>

<?php foreach ($categories as $category): ?>

<option value="<?php echo $category->id; ?>"<?php echo ($category->id == $article->categoryId) ? " selected" : ""; ?>><?php echo htmlspecialchars($category->name); ?></option>

<?php endforeach; ?>

</select>

</li>

<li>

<label for="subcategoryId">Article Subcategory</label>

<select name="subcategoryId">

<option value="0"<?php echo !$article->subcategoryId ? " selected" : ""; ?>>(none)</option>

<?php foreach ($groupedSubcategories as $categoryId => $subcategories_group): ?>

<?php

$categoryName = '';

foreach ($categories as $cat) {

if ($cat->id == $categoryId) {

$categoryName = htmlspecialchars($cat->name);

break;

}

}

?>

<optgroup label="<?php echo $categoryName; ?>">

<?php foreach ($subcategories_group as $subcategory): ?>

<option value="<?php echo $subcategory->id; ?>"<?php echo ($subcategory->id == $article->subcategoryId) ? " selected" : ""; ?>><?php echo htmlspecialchars($subcategory->name); ?></option>

<?php endforeach; ?>

</optgroup>

<?php endforeach; ?>

</select>

</li>

<li>

<label for="publicationDate">Publication Date</label>

<input type="date" name="publicationDate" id="publicationDate" placeholder="YYYY-MM-DD" required maxlength="10" value="<?php echo $article->publicationDate ? date("Y-m-d", $article->publicationDate) : ""; ?>" />

</li>

<li>

<label for="active">Make active</label>

<input type="checkbox" name="active" id="active" value="1" <?php echo ($article->active == 1) ? 'checked' : ''; ?> />

</li>

<li>

<label for="authorIds">Authors</label>

<select name="authorIds[]" id="authorIds" multiple size="5">

<?php foreach ($users as $user): ?>

<option value="<?php echo $user->id; ?>"<?php echo (in_array($user->id, $article->authorIds)) ? " selected" : ""; ?>><?php echo htmlspecialchars($user->login); ?></option>

<?php endforeach; ?>

</select>

<p class="helpText">Hold Ctrl to select multiple authors</p>

</li>

</ul>

<div class="buttons">

<input type="submit" name="saveChanges" value="Save Changes" />

<input type="submit" formnovalidate name="cancel" value="Cancel" />

</div>

</form>

<?php if ($article->id): ?>

<p><a href="/index.php?route=admin/articles/delete&id=<?php echo $article->id; ?>" onclick="return confirm('Delete This Article?')">Delete This Article</a></p>

<?php endif; ?>