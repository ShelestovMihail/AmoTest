<?php require __DIR__ . '/header.php' ?>
<?php if (isset($error)): ?>
<h4><?=$error?></h4>
<?php endif; ?>
<div class="mainForms">
    <form action="" method="post">
        <h5>Создать и связать N контактов, компаний, сделок, покупателей </h5>
        <label>Колличество сущностей: <input type="text" required name="entityCount"></label><br>
        <input type="submit" name="newEntities" value="Создать">
    </form>
    <form action="" method="post">
        <h5>Установить значение доп.поля для сущности </h5>
        <label>Значение поля: <input type="text" name="fieldValue"></label><br>
        <label>Id сущности: <input type="text" required name="entityId"></label><br>
        <input type="submit" name="setValueToEntityField" value="Задать значение">
    </form>
    <form action="" method="post">
        <h5>Добавить примечание в элемент указанной сущности</h5>
        <label>
            Тип примечания:
            <label><input type="radio" checked name="noteType" value="common"> Обычное примечание</label>
            <label><input type="radio" name="noteType" value="call_in">Входящий звонок</label>
        </label><br>
        <label>Id сущности: <input type="text" required name="entityId"></label><br>
        <input type="submit" name="setNoteToEntity" value="Создать примечание">
    </form>
    <form action="" method="post">
        <h5>Добавить задачу в элемент указанной сущности</h5>
            <label>Дата до которой необходимо завершить задачу: <input type="date" required name="completeTill"></label><br>
            <label>Текст задачи: <input type="text" required name="taskText"></label><br>
            <label>Id ответственного пользователя: <input type="text" name="responsibleUserId"></label><br>
            <label>Id сущности: <input type="text" required name="entityId"></label><br>
        <input type="submit" name="setTaskToEntity" value="Создать задачу">
    </form>
    <form action="" method="post">
        <h5>Выполнить задачу</h5>
        <label>
            <label>Id задачи: <input type="text" required name="taskId"></label><br>
            <input type="submit" name="completeTask" value="Выполнить задачу">
    </form>
</div>
<?php require __DIR__ . '/footer.php' ?>