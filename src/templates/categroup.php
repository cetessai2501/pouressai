<?php $this->layout('layout', ['title' => 'ByCategorie']) ?>
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
<div class="container" style="margin-top:40px;"><!-- d c --> 

<div class="row"><!-- deb ro -->


<?php foreach ($posts as $post) : ?>

<div class="col-md-4"><!-- d md4 --> 
<article style="margin-right:20px;height:auto;width:200px;"><!-- d a --> 

<div class="mycard" id="<?= $post->post_id;?>"><!-- d card -->

<div class="mycard__body">

<div class="mycard__title"><a style="text-decoration:none;" href="<?php echo $router->relativeUrlFor('showi', ['slug' => $post->post_slug ]);?>"><?php echo $post->post_slug; ?></a></div>
<p class="mycard__description">
<?php echo $post->post_content; ?>
</p>

</div>
<footer class="card__footer">
     <span class="card__date__day"><?php echo $post->getTime()->format('d F'); ?></span>  
     <span class="icon icon--date"><?php echo $post->getTime()->format('d F Y'); ?></span>
    <span class="icon icon--time"><?php echo $post->getTime()->format('H:i:s'); ?></span>
<?php if(!empty($comments) && in_array(intval($post->post_id), array_keys($price))): ?>
    <span class="icon icon--comment"><?php echo $count[intval($post->post_id)]; ?></span><a class="icon icon--pusher" href="<?php echo $router->relativeUrlFor('showi', ['slug' => $post->post_slug ]);?>"><?php echo $count[intval($post->post_id)]; ?> comments</a>
<?php endif; ?> 
  </footer>
</div><!-- f card -->


</article><!-- f a --> 

</div><!-- f md4 --> 
    <?php endforeach; ?>


</div><!-- f ro -->





</div><!-- f c -->
