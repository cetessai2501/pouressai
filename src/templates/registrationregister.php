<?php $this->layout('layout', ['title' => 'Register']) ?>
<div class="container">


  <div class="row justify-content-md-center">
    <div class="col-md-6">
        <div class="card text-white bg-success mb-3">
<div class="card-body">
         <div class="card-header bg-primary border-success">S inscrire</div>
          <form action="/inscription" class="form" method="post">
<?php echo $this->csrf_input();?>  
            <div class="form-group">
             <label for="username">username</label>
             <input type="text" id="username" name="username" class="form-control" required>
              <?php if(!empty($errors['username'])): ?>
              <div class="alert alert-danger">
                   <?php echo $errors['username']; ?>
                </div>
                <?php endif; ?>
            </div>
            <div class="form-group">
             <label for="email">email</label>
             <input type="email" id="email" name="email" class="form-control">
                <?php if(!empty($errors['email'])): ?>
              <div class="alert alert-danger">
                   <?php echo $errors['email']; ?>
                </div>
                <?php endif; ?>
            </div>
            <div class="form-group">
             <label for="password">mot de passe</label>
             <input type="password" id="password" name="password" class="form-control" required>
               <?php if(!empty($errors['password'])): ?>
              <div class="alert alert-danger">
                   <?php echo $errors['password']; ?>
                </div>
                <?php endif; ?>
            </div> 
            <div class="form-group">
             <label for="password_confirm">password confirm</label>
             <input type="password" id="password_confirm" name="password_confirm" class="form-control" required>
            </div> 
 </div>            
           <button class="btn btn-primary">Creer</button>
           
          </form>

        </div>
    </div>
  </div><br><br><br>

</div>


