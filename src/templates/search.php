<?php $this->layout('layout', ['title' => 'Search']) ?>
<?php $proce = array() ;?>
<?php if(!empty($comments)): ?>
<?php foreach ($comments as $comment) : ?>

<?php array_push($proce, intval($comment->pid));?>
<?php 

$price[intval($comment->pid)] = $comment ;

?>
<?php endforeach; ?>
<?php

$count = array_count_values($proce);


?>
<?php endif; ?> 
<div class="wrapi"><!-- d c --> 
<div class="row"><!-- deb ro -->
   <?php foreach ($posts as $post) : ?>

<article style="height:auto;width:250px;margin-right:20px;margin-top:20px;margin-bottom:20px;"><!-- d d1 --> 
<div class="mycardi"  id="<?= $post->id;?>"><!-- d cardi -->

<header class="mycardi__title">
        <a href="<?php echo $router->relativeUrlFor('showi', ['slug' => $post->slug ]);?>">
          <img style="width:150px;height:150px;" src="<?= $post->getThumb();?>" alt="<?= $post->getImageUrl() ;?>">
        </a>
</header>

<div class="mycardi__body"><!-- d cardi bodi-->
<p class="text-muted"><?= $post->getCreated()->format('d F Y');?></p>
<p class="mycardio__title"><?= $post->name;?></p>

<p class="mycardi__description">
<?php echo $post->id; ?>
<?php echo $post->excerpt(); ?>

</p>


</div><!-- f cardi bodi-->
<footer class="mycardi__footer">
     <span class="mycardi__date__day"><?php echo $post->getCreatedAt()->format('d F'); ?></span>  
     <span class="icon icon--date"><?php echo $post->getCreatedAt()->format('d F Y'); ?></span>
    <span class="icon icon--time"><?php echo $post->getCreatedAt()->format('H:i:s'); ?></span>
<?php if(!empty($comments) && in_array(intval($post->id), array_keys($price))): ?>
    <span class="icon icon--comment"><?php echo $count[intval($post->id)]; ?></span><a class="icon icon--pusher" href="<?php echo $router->relativeUrlFor('showi', ['slug' => $post->slug ]);?>"><?php echo $count[intval($post->id)]; ?> comments</a>
<?php endif; ?> 
  </footer>
</div><!-- f cardi -->


</article><!-- f d1 --> 

 

    <?php endforeach; ?>


</div><!-- f ro -->




</div><!-- f c -->






