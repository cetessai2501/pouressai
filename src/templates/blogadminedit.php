<?php $this->layout('layout', ['title' => 'Update article']) ?>
<?php
$pall = array();


$unique = array_unique($this->flatten($tags));

//var_dump($this->array_equal($bag, $unique));
?>


<div class="container">

    <h1>Editer l'article</h1>
    <form action="/admin/blog/<?php echo $item->id; ?>" method="post" enctype="multipart/form-data">
<?php echo $this->csrf_input();?>  
        <input type="hidden" name="_METHOD" value="PUT">
    
    <?php foreach ($liste as $k => $v) : ?>
    <?php  if(intval($item->category_id) === $k ){
                echo '<div class="form-group">';
                echo '<p style="font-size:18px;"> Catégorie nom:  ' .$v. '</p>';
                echo '</div>';
                }  ;?> 
    <?php endforeach; ?> 
    
    <div class="form-group">
          <label for="category_id">categorie</label>
          <select id="category_id" name="category_id" class="form-control">
             <?php foreach ($liste as $k => $v) : ?>
                <?php  if(intval($item->category_id) === $k ){
                echo '<option value="'.$item->category_id;
                echo '" selected>';
                echo $item->category_id;
                echo '</option>';
                }  ;?> 
                <option value="<?php echo $k; ?> " ><?php echo $k; ?></option>
              <?php endforeach; ?>  
        </select>
        </div>
<?php if(!empty($tagis) && $this->array_equal($bag, $unique) === true): ?> 
        <div class="form-group">
          <label for="tag_name">Tags</label>
<select multiple id="tag_name" name="tag_name[]" class="js-example-basic-single">
         
<?php foreach ($unique as $k => $v) : ?>
<?php  if($k !== $v ){
                echo '<option value="'.$v;
                echo '" selected>';
                echo $v;
                echo '</option>';
                //unset($unique[$v]);
                }  ;?> 

<?php if($v !== $item->tag_name): ?>
               
<?php endif; ?>
              <?php endforeach; ?>



<?php endif; ?>
<?php if(!empty($tagis) && $this->array_equal($bag, $unique) === false): ?> 
        <div class="form-group">
          <label for="tag_name">Tags</label>
<select multiple id="tag_name" name="tag_name[]" class="js-example-basic-single">
         
<?php foreach ($unique as $k => $v) : ?>
<?php  if($item->tag_name === $v ){
                echo '<option value="'.$item->tag_name;
                echo '" selected>';
                echo $item->tag_name;
                echo '</option>';
                //unset($unique[$v]);
                }  ;?> 

<?php if($v !== $item->tag_name): ?>
                <option value="<?php echo $v ; ?>" ><?php echo $v ;?></option>
<?php endif; ?>
              <?php endforeach; ?>

        </select>
        </div> 
 <?php elseif(empty($tagis)): ?> 
        <div class="form-group">
          <label for="tag_name">Tags</label>
<select multiple id="tag_name" name="tag_name[]" class="form-control">
          
<?php foreach ($unique as $k => $v) : ?>
                <option value="<?php echo $v; ?>" ><?php echo $v; ?></option>
              <?php endforeach; ?>
        </select>
        </div> 

   <?php endif; ?>
        <div class="form-group">
          <label for="slug"> slug</label>
          <input type="text" name="slug" class="form-control" value="<?php echo $item->slug; ?>">
        </div>
   <div class="form-group">
          <label for="name"> name</label>
          <input type="text" name="name" class="form-control" value="<?php echo $item->name; ?>">
        </div>
<div class="form-group">
  <label for="image">image</label>
<?php if (isset($item->image)) : ?>
          <p>
      
              <img src="<?= $item->getThumb();?>" alt="<?= $item->getImageUrl() ;?>" width="6%">
          </p>
<?php endif; ?>
 </div>
<input type="file" id="image" name="image[]" class="form-control" value="<?php echo isset($item->image); ?>">
<div class="form-group">
          <label for="content">votre contenu</label>
          <textarea type="text" id="content" name="content" class="form-control"><?php echo $item->content; ?></textarea>
        </div>


        <button id="target2" class="btn btn-primary">Mettre à jour</button>
    </form>
<div id="target">
<embed src="" width="500" height="375">
</div>
<div id="div1"> </div>

<button id="target" >target</button>
</div>


<b></b>
<div id="success"></div>
<b>Error Response:</b>
<div id="error"></div>
<script>
$(document).ready(function() {
    $('.js-example-basic-single').select2();
});





  

</script>


