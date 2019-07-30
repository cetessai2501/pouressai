<?php $this->layout('layout', ['title' => 'Create categorie']) ?>
<div class="container">

<h1>Créer une nouvelle categorie</h1>

<form action="/admin/categories/new" method="post">
<?php echo $this->csrf_input();?>  
        <div class="form-group">
          <label for="name">le titre</label>
          <input type="text" id="name" name="name" class="form-control" >
        </div>
        <div class="form-group">
          <label for="slug">le slug</label>
          <input type="text" id="slug" name="slug" class="form-control" >
        </div>
        <div class="form-group"> 
        <input type="submit" class="btn btn-primary" value="Créer"> 
              
         </div>
 </form>  

</div>
