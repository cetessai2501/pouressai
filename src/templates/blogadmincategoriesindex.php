<?php $this->layout('layout', ['title' => 'Catégories index']) ?>
<div class="container" style="margin-top:20px;">
<p>

<a href="<?php echo $router->pathFor('blog.admin.category.create');?>" class="btn btn-success">Ajouter</a>

</p>

 <table class="table table-striped">
        <thead>
            <tr>
                <th>Titre</th> 
                <th>Titre</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($categories as $categorie): ?>

            <tr>
                <td><?php echo $categorie->id; ?></td> 
                <td><?php echo $categorie->name; ?></td>
                <td>
                    <a href="/admin/categories/<?php echo $categorie->id; ?>" class="btn btn-primary">Editer</a>
                    <form
                            style="display: inline;"
                            method="post"
                            action="/admin/categories/<?php echo $categorie->id; ?>"
                            onsubmit="return confirm('Voulez vous vraiment supprimer cette categorie ? ');"
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
