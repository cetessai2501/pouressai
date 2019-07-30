<?php $this->layout('layout', ['title' => 'Articles index']) ?>
<?php $current = $page;?>

<div class="container" style="margin-top:20px;">

<p>

<a href="<?php echo $router->pathFor('blog.admin.create');?>" class="btn btn-success">Ajouter</a>

</p>


 <table class="table table-striped">
        <thead>
            <tr>
                <th>CatId</th> 
                <th>CatName</th>  
                <th>Titre</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
            <tr>
                <td><?php echo $item->category_id; ?></td>
                 <td><?php echo $item->categoryName; ?></td>
                <td><?php echo $item->slug; ?></td>
                <td>
                    <a href="/admin/blog/<?php echo $item->id; ?>" class="btn btn-primary">Editer</a>
                    <form 
                            style="display: inline;"
                            method="post"
                            action="/admin/blog/<?php echo $item->id; ?>"
                            onsubmit="return confirm('Voulez vous vraiment supprimer cet article ? ');"
                    >
                    <?php echo $this->csrf_input();?>      
                        <input type="hidden" name="_METHOD" value="DELETE">
                       
                      
                         
                        <button class="btn btn-danger" type="submit">
                            Supprimer
                        </button>
                    </form>
                </td>
            </tr
            <?php endforeach; ?>
        </tbody>
    </table>

</div>

<div class="menavpagi" style="margin: auto;width: 40%;padding: 30px;">
<nav aria-label="Page navigation example">
 <ul class="pagination">
<?php for ($i = 0; $i < $total; ++$i): ?> 

<?php if($i === 0 && $current == 1): ?>
<li class="page-item active"><a class="page-link" href="<?php echo $router->pathFor('blogadminindex');?>?page=<?php echo $i + 1; ?>"><?php echo $i + 1; ?><span class="sr-only">(current)</span></a></li>
<li class="page-item "><a class="page-link" href="<?php echo $router->pathFor('blogadminindex');?>?page=<?php echo $current + 1 ; ?>"><?php echo $current + 1 ; ?></a></li>
<li class="page-item "><a class="page-link" href="<?php echo $router->pathFor('blogadminindex');?>?page=<?php echo $current + ($total - 1) ; ?>"><?php echo $current + ($total - 1) ; ?></a></li>
<?php elseif($i !== 0 && $current == $i && $current !== 1): ?> 

<li class="page-item "><a class="page-link" href="<?php echo $router->pathFor('blogadminindex');?>?page=<?php echo $current + 1 ; ?>"><?php echo $current + 1 ; ?></a></li>


<?php elseif($i !== 0 && $current == $i + 1 ): ?> 
<?php if($current == $total ): ?>

<li class="page-item "><a class="page-link" href="<?php echo $router->pathFor('blogadminindex');?>?page=<?php echo $current - ($total - 1) ; ?>"><?php echo $current - ($total - 1) ; ?></a></li>
 <?php endif; ?> 

<li class="page-item "><a class="page-link" href="<?php echo $router->pathFor('blogadminindex');?>?page=<?php echo $current - 1 ; ?>"><?php echo $current - 1 ; ?></a></li>
<li class="page-item active"><a class="page-link" href="<?php echo $router->pathFor('blogadminindex');?>?page=<?php echo $i + 1 ; ?>"><?php echo $i + 1; ?></a></li>



 <?php endif; ?> 





<?php endfor; ?>
</ul>
</nav>
</div>





