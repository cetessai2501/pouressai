<?php $this->layout('layout', ['title' => 'Mon Compte']) ?>
<div class="container">

  <h1>Mon compte</h1>

  <p>
    <strong>Nom d'utilisateur : </strong> <?php echo $user->username;?>
  </p>

  <form action="" method="post" onsubmit="return confirm('Voulez vous vraiment supprimer votre compte ?');">
<?php echo $this->csrf_input();?>  
    <input type="hidden" name="_METHOD" value="DELETE">
    
    <button href="#" class="btn btn-danger">Supprimer mon compte</button>
  </form>

</div>
