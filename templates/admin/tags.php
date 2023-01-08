<?php $this->layout('layouts/admin', ['title' => 'Etiquetas']) ?>

<?php $this->start('header') ?>
<p class="title">Etiquetas</p>
<?php $this->stop() ?>

<div class="container">
    <?php foreach($tags as $i => $tag): ?>
        <form action="<?= $this->url('/tags/' . $tag->id . '/edit') ?>" method="post">
            <div class="card">
                <header class="card-header">
                    <p class="card-header-title">Etiqueta <?= $this->e($i + 1) ?></p>
                </header>
                <div class="card-content">
                    <div class="field">
                        <label class="label">Nombre</label>
                        <div class="control">
                            <input name="name" class="input" type="text" value="<?= $this->e($tag->name) ?>" />
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">Tipo</label>
                        <div class="control">
                            <div class="select">
                                <select name="type">
                                    <option value="-1">Negativo</option>
                                    <option value="0" selected>Neutro</option>
                                    <option value="1">Positivo</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <footer class="card-footer">
                    <button type="submit" class="card-footer-item">Editar</button>
                    <a href="<?= $this->url('/tags/' . $tag->id . '/delete') ?>" class="card-footer-item has-text-danger">Eliminar</a>
                </footer>
            </div>
        </form>
    <?php endforeach ?>
    <?php if (empty($tags)): ?>
        <p class="has-text-centered">No hay etiquetas</p>
    <?php endif ?>
    <hr />
    <p class="title">Añadir etiqueta</p>
    <form action="<?= $this->url('/tags/new') ?>" method="post">
        <div class="field">
            <label class="label">Nombre</label>
            <div class="control">
                <input name="name" class="input" type="text" />
            </div>
        </div>
        <div class="field">
            <label class="label">Tipo</label>
            <div class="control">
                <div class="select">
                    <select name="type">
                        <option value="-1">Negativo</option>
                        <option value="0" selected>Neutro</option>
                        <option value="1">Positivo</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="field">
            <div class="control">
                <button class="button is-success" type="submit">Crear</button>
            </div>
        </div>
    </form>
</div>
