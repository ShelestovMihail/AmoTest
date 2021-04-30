<?php require __DIR__ . '/header.php' ?>
<?php if (isset($error)): ?>
<h4><?=$error?></h4>
<?php endif; ?>
<div class="mainForms">
    <form action="" method="post">
        <h5>Создать и связать N контактов, компаний, сделок, покупателей </h5>
        <label>Колличество сущностей: <input type="text" name="entityCount"></label><br>
        <input type="submit" name="newEntities" value="Создать">
    </form>
    <form action="" method="post">
        <h5>Установить значение доп.поля для сущности </h5>
        <label>Значение поля: <input type="text" name="fieldValue"></label><br>
        <label>Id сущности: <input type="text" name="entityId"></label><br>
        <input type="submit" name="setValueToEntityField" value="Задать значение">
    </form>
</div>
<?php require __DIR__ . '/footer.php' ?>