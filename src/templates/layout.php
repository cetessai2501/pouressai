<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<title><?=$this->e($title)?></title>

<link rel="stylesheet" href="/css/main.css">
<link rel="stylesheet" href="/css/semantic.css">
<link rel="stylesheet" href="/css/icon.css">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/css/select2.min.css" rel="stylesheet" />

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/immutable/3.8.2/immutable.js" integrity="sha256-/2QRv/wHzmPJLRB351+bVXYjGU2Tk/bLfOt8XiVcmAg=" crossorigin="anonymous"></script>
<script src="/js/appo.js"></script>
</head>
<nav class="navbar navbar-expand-md navbar-dark bg-primary">
  <a class="navbar-brand" href="/">Home</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="collapsibleNavbar">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" href="/blog">Blog</a>
      </li>
      <?php if(isset($_SESSION['auth.role']) && $_SESSION['auth.role'] === 'admin'): ?> 
        <li class="nav-item">
              <a href="/admin/blog" class="nav-link">admin articles</a>
        </li>
        <li class="nav-item">
              <a href="/admin/categories" class="nav-link">admin categories</a>
        </li>
      <?php endif; ?> 
      <li class="nav-item">
        <a class="nav-link" href="/books">Link</a>
      </li>
      <li class="nav-item">
        <a id="demo" class="nav-link" href="#">Link</a>
      </li> 
     <?php if(empty($_SESSION['auth.user'])): ?>
        <li class="nav-item">
             <a class="nav-link" href="/login">login</a>
        </li>
        <?php endif; ?> 
<?php if(!empty($_SESSION['auth.user'])): ?>
        <div class="nav navbar-nav" style="float:right;color:red;">
              <li style="margin-left:20px;"><h5>Bienvenue <?php echo $_SESSION['auth.username']; ?></h5></li> 
              <li  style="margin-left:20px;"><h5 id="avatar"> </h5></li>      
        </div>
        <?php endif; ?> 
     <?php if(empty($_SESSION['auth.user'])): ?>
        <li class="nav-item">
              <a class="nav-link" href="/inscription">inscription</a>
        </li>
        <?php endif; ?>   

     <?php if(!empty($_SESSION['auth.user'])): ?>
        <li class="nav-item">
              
            <form  action="/logout" method="post">
<?php echo $this->csrf_input();?>
              <input type="hidden" name="_METHOD" value="DELETE">
              
              <button style="margin-left:100px;" class="btn btn-danger" type="submit">Se
                déconnecter
              </button>
            </form>
         </li><br>
        
        <?php endif; ?> 
       
     
    </ul>
  </div>  
</nav>


<?php if(!empty($_SESSION['slimFlash']['success'])): ?>
<div class="alert alert-success">
<?php echo $_SESSION['slimFlash']['success'][0];
unset($_SESSION['slimFlash']['success'][0]);

 ?>
</div>
<?php endif; ?>



<?php if(!empty($_SESSION['slimFlash']['failed'])): ?>
<div class="alert alert-danger">
<?php echo $_SESSION['slimFlash']['failed'][0];
unset($_SESSION['slimFlash']['failed'][0]);


 ?>
</div>
<?php endif; ?>


    <body>
     <?=$this->section('content')?>

<?php if(isset($_SESSION['auth.username'])){ ?>    
<script>
jQuery( document ).ready( function($) {
var user = "<?php echo $_SESSION['auth.email']; ?>";
var $avat = $('#avatar');
$avat.append('<img style="margin-right:20px;border: 2px solid #FFF;" src="' + window.gravi(user, 40) + '" alt="Avatar" title="' + user  +   '"/>' );

});
</script>
<?php } ?>





    </body>

</html>




