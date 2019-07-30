<?php $this->layout('layout', ['title' => 'Login']) ?>
<div class="container">




   <div class="row">
        <div class="col-sm-6">
            <div class="col">
                <div class="card-header">Se connecter</div>
                <div class="card-block">
                    <form action="/login" method="post">
<?php echo $this->csrf_input();?>  
                        <div class="form-group">
                        <label for="username">username</label>
                               <input type="text" id="username" name="username" class="form-control" >
                        </div>
                        <div class="form-group">
                        <label for="password">password</label>
                               <input type="password" id="password" name="password" class="form-control" >
                        </div> 

                        
                        <div class="form-group">
                            <a href="/password/reset" class="small">Mot de passe oublié ?</a>
                        </div>
                      
                        <button class="btn btn-primary" type="submit">Se connecter</button>
                    </form>
                </div>
            </div>
        </div>
 
        
    </div>

</div>


