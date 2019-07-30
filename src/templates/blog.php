<?php $this->layout('layout', ['title' => 'Blog']) ?>

<div class="wrap">




    <?php

//echo htmlentities($str);
//echo htmlentities($str, ENT_QUOTES);
?>



 <div id="mygroup" class="col-m-4" style="float:right;">    
<!--<p class="text-left">
        <a class="btn btn-primary" href="#">Voir plus/toutes les photos</a>
    </p>-->
<!--<h2>Filtrer </h2>
    <div class="list-group">
         
            <a class="list-group-item" href="#">
                
            </a>
     
</div>-->
    <h2> Catégories </h2>
    <div  class="list-group" style="margin-bottom:20px;">
       <?php foreach ($categs as $cat) : ?>  
            <div class="list-group-item" style="background-color:#0074D9;">
          <a style="text-decoration:none;" class="list-group-item" href="<?php echo $router->pathFor('blog.category', ['id' => $cat->id ]);?>"><?=$cat->name;?></a>
                  
                   
                </a>
            </div>
       <?php endforeach; ?>
    </div>
<div id="app">
<example></example>
</div>
</div>
   <h2>Les dernières publications </h2>
    <div class="row">
        <?php foreach ($posts as $post) : ?>
            <div class="col-md-4">
              <article style="margin-bottom:20px;height:auto;width:200px;">
                 <div class="card" id="<?= $post->id;?>"><!-- d d2 -->
<div class="front">
<p><strong> Categorie :  <?php echo $post->categoryName; ?></strong></p>
<p class="text-muted"><?= $post->getDay();?></p>
<a href="<?php echo $router->relativeUrlFor('showi', ['slug' => $post->slug ]);?>"><img style="position:relative;width:200px;" src="<?= $post->getThumb();?>" alt="<?= $post->getImageUrl() ;?>"></a>
</div>

<div class="back">
<p>
<?php echo $post->name; ?>
</p>
<p>
<?php echo $post->excerpt(); ?>
</p>
<a href="<?php echo $router->relativeUrlFor('showi', ['slug' => $post->slug ]);?>" class="btn btn-success">Suite</a>
</div>
</div><!-- f d2 -->
<div style="margin-top:20px;height:auto;width:200px;"> 
<?php foreach ($post->getTags() as $to) : ?>

<?php if(!empty($to)): ?>
<a href="<?php echo $router->relativeUrlFor('taggi', ['slug' => 'tags' ], ['tag' => $to]);?>" class="badge badge-danger"><h6><?php echo $to; ?></h6></a>
<?php endif; ?> 


           
<?php endforeach; ?>
</div>
               </article>
            </div>
        <?php endforeach; ?>
    </div>



<?php $current = $page;




 ?>


<div class="menavpagi" style="margin: auto;width: 40%;padding: 30px;">
<nav aria-label="Page navigation example">
 <ul class="pagination">
<?php for ($i = 0; $i < $total; ++$i): ?> 

<?php if($i === 0 && $current == 1): ?>
<li class="page-item active"><a class="page-link" href="<?php echo $router->pathFor('blogi');?>?page=<?php echo $i + 1; ?>"><?php echo $i + 1; ?><span class="sr-only">(current)</span></a></li>
<li class="page-item "><a class="page-link" href="<?php echo $router->pathFor('blogi');?>?page=<?php echo $current + 1 ; ?>"><?php echo $current + 1 ; ?></a></li>
<li class="page-item "><a class="page-link" href="<?php echo $router->pathFor('blogi');?>?page=<?php echo $current + ($total - 1) ; ?>"><?php echo $current + ($total - 1) ; ?></a></li>
<?php elseif($i !== 0 && $current == $i && $current !== 1): ?> 

<li class="page-item "><a class="page-link" href="<?php echo $router->pathFor('blogi');?>?page=<?php echo $current + 1 ; ?>"><?php echo $current + 1 ; ?></a></li>


<?php elseif($i !== 0 && $current == $i + 1 ): ?> 
<?php if($current == $total ): ?>

<li class="page-item "><a class="page-link" href="<?php echo $router->pathFor('blogi');?>?page=<?php echo $current - ($total - 1) ; ?>"><?php echo $current - ($total - 1) ; ?></a></li>
 <?php endif; ?> 

<li class="page-item "><a class="page-link" href="<?php echo $router->pathFor('blogi');?>?page=<?php echo $current - 1 ; ?>"><?php echo $current - 1 ; ?></a></li>
<li class="page-item active"><a class="page-link" href="<?php echo $router->pathFor('blogi');?>?page=<?php echo $i + 1 ; ?>"><?php echo $i + 1; ?></a></li>



 <?php endif; ?> 





<?php endfor; ?>
</ul>
</nav>
</div>













</div>

<script src="/bundle.js"></script>
<script>
jQuery( document ).ready( function($) {



//console.log(link);
});

</script>










