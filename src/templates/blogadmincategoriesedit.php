<?php $this->layout('layout', ['title' => 'Update categorie']) ?>
<div class="container">
    <h1>Editer la categorie</h1>

    <form action="/admin/categories/<?php echo $category->id; ?>" method="post" >
<?php echo $this->csrf_input();?>  
        <input type="hidden" name="_METHOD" value="PUT">
      
        <div class="form-group">
          <label for="name"> titre</label>
          <input type="text" name="name" class="form-control" value="<?php echo $category->name; ?>">
        </div>
        <div class="form-group">
          <label for="slug"> slug</label>
          <input type="text" name="slug" class="form-control" value="<?php echo $category->slug; ?>">
        </div>
        
        <button class="btn btn-primary">Mettre a jour</button>
    </form>

</div>
