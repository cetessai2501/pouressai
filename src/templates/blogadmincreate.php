<?php $this->layout('layout', ['title' => 'Create article']) ?>
<div class="container">
<?php
$pall = array();

$unique = array_unique($this->flatten($tags));

?>


    <h1>Créer un nouvel article</h1>

<form action="/admin/blog/new" method="post" enctype="multipart/form-data">
<?php echo $this->csrf_input();?>  
        <div class="form-group">
          <label for="category_id">categorie</label>
          <select id="category_id" name="category_id" class="form-control">
             <?php foreach ($liste as $k => $v) : ?>
                <option value="<?php echo $k; ?>" ><?php echo $k; ?></option>
              <?php endforeach; ?>  
        </select>
        </div>
        <div class="form-group">
          <label for="tag_name">Tag</label>
<select multiple id="tag_name" name="tag_name[]" class="form-control">
          
<?php foreach ($unique as $k => $v) : ?>
                <option value="<?php echo $v; ?>" ><?php echo $v; ?></option>
              <?php endforeach; ?>
        </select>
        </div> 

         <div class="form-group">
          <label for="name">votre titre</label>
          <input type="text" id="name" name="name" class="form-control" >
        </div>
        <div class="form-group">
          <label for="image">fichier</label>
          <input type="file" id="image" name="image[]" class="form-control" >
          
        </div>
        <div class="form-group">
          <label for="slug">votre slug de la forme titre-number</label>
          <input type="text" id="slug" name="slug" class="form-control" >
        </div>
         <div class="form-group">
          <label for="content">votre contenu</label>
          <textarea type="text" id="content" name="content" class="form-control" ></textarea>
        </div>
        <div class="form-group">
        <div class="form-group">
          
        </div>  
        <input type="submit" class="btn btn-primary" value="Créer"> 
              
         </div>
        
        
    </form>  


</div>


