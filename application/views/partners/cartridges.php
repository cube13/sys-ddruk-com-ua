    <?php echo 'Зарегистрированно катриджей: ' . $cartridges->num_rows() . '<br/>'; ?>

    <?php echo 'Заправок и восстановлений за прошлый месяц: ' . $refills_12 . ' (в т.ч. восстановлений ' . $recikls_12 . ')<br/>'; ?>
    <?php echo 'Заправок и восстановлений в текущем месяце: ' . $refills . ' (в т.ч. восстановлений ' . $recikls . ')<br/>'; ?>

    <?php foreach ($cartridges->result() as $cartridg): ?>
        <?php echo $cartridg->uniq_num . ' | ' . $cartridg->name . '<br/>'; ?>
    <?php endforeach; ?>
